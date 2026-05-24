<?php
namespace App\Http\Controllers;
use App\Models\{Registration, Payment};
use App\Services\{PaymentService, TicketService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function show(Registration $registration)
    {
        abort_if($registration->user_id !== Auth::id(), 403);
        abort_if($registration->payment_status !== 'pending', 422, 'Payment already processed.');
        return view('attendee.payment.checkout', compact('registration'));
    }
    public function process(Request $request, Registration $registration)
{
    abort_if($registration->user_id !== Auth::id(), 403);

    $request->validate([
        'gateway' => 'required|in:stripe,paypal,gcash,bank_transfer'
    ]);

    try {
        $payment = $this->paymentService->process(
            $registration,
            $request->gateway,
            $request->all()
        );

        return redirect()
            ->route('attendee.tickets.show', $registration)
            ->with('success', '✅ Payment successful! Your tickets are ready.');

    } catch (\Exception $e) {
        return back()->with('error', 'Payment failed: ' . $e->getMessage());
    }
}
    public function receipt(Payment $payment)
    {
        abort_if($payment->user_id !== Auth::id(), 403);
        $payment->load('registration.event','user');
        return view('attendee.payment.receipt', compact('payment'));
    }
    public function downloadReceipt(Payment $payment)
    {
        abort_if($payment->user_id !== Auth::id(), 403);
        return $this->paymentService->generateReceiptPdf($payment);
    }
}
