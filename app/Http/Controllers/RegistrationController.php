<?php
namespace App\Http\Controllers;
use App\Models\{Event, TicketType, Registration, Coupon};
use App\Services\{TicketService, PaymentService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};

class RegistrationController extends Controller
{
    public function __construct(private TicketService $ticketService, private PaymentService $paymentService) {}

    public function showCheckout(Event $event)
    {
        abort_if($event->is_full, 422, 'This event is fully booked.');
        abort_if(!in_array($event->status, ['upcoming','ongoing']), 422, 'Registration is not open.');
        $ticketTypes = $event->ticketTypes()->where('is_active', true)->get();
        return view('attendee.events.checkout', compact('event','ticketTypes'));
    }
    

    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'quantity'       => 'required|integer|min:1|max:10',
            'coupon_code'    => 'nullable|string',
        ]);
        $ticketType  = TicketType::findOrFail($validated['ticket_type_id']);
        if (!$ticketType->is_active) {
    abort(422, 'This ticket type is no longer active.');
}
if ($ticketType->quantity_remaining <= 0) {
    abort(422, 'Sorry, this ticket is sold out.');
}
        abort_if($ticketType->quantity_remaining < $validated['quantity'], 422, 'Not enough tickets available.');

        $unitPrice   = $ticketType->price;
        $totalAmount = $unitPrice * $validated['quantity'];
        $discount    = 0;
        $couponCode  = null;

        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('event_id', $event->id)->where('code', $request->coupon_code)->first();
            if ($coupon && $coupon->isValid($totalAmount)) {
                $discount   = $coupon->calculateDiscount($totalAmount);
                $couponCode = $coupon->code;
                $coupon->increment('used_count');
            }
        }
        $finalAmount = max(0, $totalAmount - $discount);

        DB::transaction(function() use ($event, $ticketType, $validated, $unitPrice, $totalAmount, $discount, $finalAmount, $couponCode, &$registration) {
            $registration = Registration::create([
                'event_id'        => $event->id,
                'user_id'         => Auth::id(),
                'ticket_type_id'  => $ticketType->id,
                'quantity'        => $validated['quantity'],
                'unit_price'      => $unitPrice,
                'total_amount'    => $totalAmount,
                'discount_amount' => $discount,
                'final_amount'    => $finalAmount,
                'status'          => $finalAmount == 0 ? 'confirmed' : 'pending',
                'payment_status'  => $finalAmount == 0 ? 'free' : 'pending',
                'coupon_code'     => $couponCode,
            ]);
            $ticketType->increment('quantity_sold', $validated['quantity']);
            if ($finalAmount == 0) {
                $this->ticketService->generateTickets($registration);
            }
        });

        if ($finalAmount > 0) {
            return redirect()->route('attendee.payment.show', $registration);
        }
        return redirect()->route('attendee.tickets.show', $registration)->with('success', 'Registration successful! Your tickets have been generated.');
    }

    public function cancel(Registration $registration)
    {
        abort_if($registration->user_id !== Auth::id(), 403);
        abort_if($registration->event->start_date <= now(), 422, 'Cannot cancel after event start.');
        DB::transaction(function() use ($registration) {
            $registration->ticketType->decrement('quantity_sold', $registration->quantity);
            $registration->tickets()->update(['status' => 'cancelled']);
            $registration->update(['status' => 'cancelled']);
            if ($registration->payment && $registration->payment->status === 'completed') {
                app(PaymentService::class)->refund($registration->payment);
            }
        });
        return redirect()->route('attendee.registrations.index')->with('success', 'Registration cancelled successfully.');
    }
}
