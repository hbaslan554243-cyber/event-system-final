<?php
namespace App\Http\Controllers\Organizer;
use App\Http\Controllers\Controller;
use App\Models\{Event, Registration, Ticket};
use Illuminate\Http\Request;

class AttendeeController extends Controller
{
    public function index(Event $event)
    {
        $attendees = Registration::with('user','ticketType')
            ->where('event_id', $event->id)
            ->latest()->paginate(25);
        return view('organizer.attendees.index', compact('event','attendees'));
    }

    public function checkIn(Registration $registration)
    {
        abort_if($registration->status !== 'confirmed', 422, 'Registration not confirmed.');
        $registration->update(['status' => 'attended', 'checked_in_at' => now()]);
        return response()->json([
            'message' => 'Check-in successful!',
            'name'    => $registration->user->name
        ]);
    }

    public function scanQr(Request $request)
    {
        $input = trim($request->qr_data);

        // Try finding by qr_code_data first, then by ticket_number
        $ticket = Ticket::with('registration.user', 'registration.event')
            ->where('qr_code_data', $input)
            ->orWhere('ticket_number', $input)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found. Please check the ticket number and try again.'
            ], 422);
        }

        if ($ticket->status === 'used') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket already used. This attendee has already checked in.'
            ], 422);
        }

        if ($ticket->registration->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'This registration has been cancelled.'
            ], 422);
        }

        // Mark ticket as used and registration as attended
        $ticket->update(['status' => 'used', 'used_at' => now()]);
        $ticket->registration->update([
            'status'        => 'attended',
            'checked_in_at' => now()
        ]);

        return response()->json([
    'success'         => true,
    'message'         => 'Welcome, ' . $ticket->registration->user->name . '!',
    'name'            => $ticket->registration->user->name,
    'registration_id' => $ticket->registration->id,
]);

        return response()->json([
            'success' => true,
            'message' => 'Welcome, ' . $ticket->registration->user->name . '!',
            'name'    => $ticket->registration->user->name,
        ]);
    }
}