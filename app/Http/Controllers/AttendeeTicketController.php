<?php
namespace App\Http\Controllers;
use App\Models\{Registration, Ticket};
use App\Services\TicketService;
use Illuminate\Support\Facades\Auth;

class AttendeeTicketController extends Controller
{
    public function index()
    {
        $registrations = Registration::with('event','ticketType','tickets','payment')
            ->where('user_id', Auth::id())->latest()->paginate(10);
        return view('attendee.tickets.index', compact('registrations'));
    }
    public function show(Registration $registration)
    {
        abort_if($registration->user_id !== Auth::id(), 403);
        $registration->load('event.venue','ticketType','tickets','payment');
        return view('attendee.tickets.show', compact('registration'));
    }
    public function download(Ticket $ticket)
    {
        abort_if($ticket->user_id !== Auth::id(), 403);
        $ticket->load('event','ticketType','registration');
        return app(TicketService::class)->downloadTicketPdf($ticket);
    }
}
