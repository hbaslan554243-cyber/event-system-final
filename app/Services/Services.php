<?php
namespace App\Services;
use App\Models\{Registration, Ticket};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TicketService
{
    public function generateTickets(Registration $registration): void
    {
        for ($i = 0; $i < $registration->quantity; $i++) {
            $qrData = Str::uuid()->toString();
            $qrPath = 'tickets/qr/' . $registration->id . '_' . $i . '.png';

            // Generate QR code if package is available
            if (class_exists(\QrCode::class)) {
                \QrCode::format('png')->size(300)->generate($qrData, Storage::disk('public')->path($qrPath));
            }

            Ticket::create([
                'registration_id' => $registration->id,
                'event_id'        => $registration->event_id,
                'user_id'         => $registration->user_id,
                'ticket_type_id'  => $registration->ticket_type_id,
                'qr_code_path'    => $qrPath,
                'qr_code_data'    => $qrData,
                'status'          => 'valid',
            ]);
        }
        $registration->update(['qr_code' => $registration->tickets()->first()?->qr_code_data]);
        app(NotificationService::class)->sendTicketConfirmation($registration);
    }

    public function downloadTicketPdf(Ticket $ticket)
    {
        $ticket->load('event.venue', 'ticketType', 'user', 'registration');
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.ticket', compact('ticket'));
        $pdf->setPaper([0, 0, 595, 280]);
        return $pdf->download('ticket-' . $ticket->ticket_number . '.pdf');
    }

    public function validateQrCode(string $qrData): array
    {
        $ticket = Ticket::where('qr_code_data', $qrData)->with('event','user','ticketType','registration')->first();
        if (!$ticket) return ['valid' => false, 'message' => 'Invalid QR code.'];
        if ($ticket->status === 'used') return ['valid' => false, 'message' => 'Ticket already used.'];
        if ($ticket->status === 'cancelled') return ['valid' => false, 'message' => 'Ticket has been cancelled.'];
        $ticket->update(['status' => 'used', 'used_at' => now()]);
        $ticket->registration->update(['status' => 'attended', 'checked_in_at' => now()]);
        return ['valid' => true, 'message' => 'Valid! Welcome, ' . $ticket->user->name, 'ticket' => $ticket];
    }
}
