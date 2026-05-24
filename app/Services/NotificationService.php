<?php
namespace App\Services;
use App\Models\{Registration, Payment, Event, Announcement, NotificationLog};
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendTicketConfirmation(Registration $registration): void
    {
        $registration->load('event','ticketType','tickets','user');
        try {
            Mail::send('emails.ticket-confirmation', compact('registration'), function($m) use ($registration) {
                $m->to($registration->user->email, $registration->user->name)
                  ->subject('Your Ticket for ' . $registration->event->title);
            });
            $this->log($registration->user_id, $registration->event_id, 'email', 'Ticket Confirmation', 'sent');
        } catch (\Exception $e) {
            $this->log($registration->user_id, $registration->event_id, 'email', 'Ticket Confirmation', 'failed', $e->getMessage());
        }
    }
    public function sendEventReminder(Event $event, int $hoursBefore = 24): void
    {
        $registrations = $event->registrations()->with('user')->where('status','confirmed')->get();
        foreach ($registrations as $reg) {
            try {
                Mail::send('emails.event-reminder', compact('event','reg'), function($m) use ($event, $reg) {
                    $m->to($reg->user->email, $reg->user->name)->subject('Reminder: ' . $event->title);
                });
            } catch (\Exception $e) {}
        }
    }
    public function sendEventAnnouncement(Event $event, Announcement $announcement): void
    {
        $registrations = $event->registrations()->with('user')->where('status','confirmed')->get();
        foreach ($registrations as $reg) {
            try {
                Mail::send('emails.announcement', compact('event','announcement','reg'), function($m) use ($event, $announcement, $reg) {
                    $m->to($reg->user->email, $reg->user->name)->subject('[' . $event->title . '] ' . $announcement->title);
                });
            } catch (\Exception $e) {}
        }
        $announcement->update(['sent_at' => now()]);
    }
    public function sendRefundConfirmation(Payment $payment): void
    {
        try {
            Mail::send('emails.refund-confirmation', compact('payment'), function($m) use ($payment) {
                $m->to($payment->user->email, $payment->user->name)->subject('Refund Confirmation');
            });
        } catch (\Exception $e) {}
    }
    private function log(?int $userId, ?int $eventId, string $type, string $subject, string $status, ?string $error = null): void
    {
        NotificationLog::create([
            'user_id'       => $userId,
            'event_id'      => $eventId,
            'type'          => $type,
            'subject'       => $subject,
            'body'          => '',
            'status'        => $status,
            'error_message' => $error,
            'sent_at'       => now(),
        ]);
    }
}
