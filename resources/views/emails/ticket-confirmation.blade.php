{{-- resources/views/emails/ticket-confirmation.blade.php --}}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; background: #f0f4ff; margin: 0; padding: 20px; }
.container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
.header { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; padding: 32px; text-align: center; }
.header h1 { margin: 0; font-size: 24px; }
.header p { margin: 8px 0 0; opacity: .85; }
.body { padding: 32px; }
.event-card { background: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #4f46e5; }
.btn { display: inline-block; background: #4f46e5; color: #fff !important; text-decoration: none; padding: 12px 32px; border-radius: 8px; font-weight: bold; margin-top: 16px; }
.detail-row { display: flex; gap: 12px; margin-bottom: 8px; font-size: 14px; }
.detail-label { color: #64748b; width: 100px; flex-shrink: 0; }
.detail-value { font-weight: 600; }
.footer { background: #f8fafc; padding: 20px 32px; text-align: center; font-size: 12px; color: #94a3b8; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <div style="font-size:40px;margin-bottom:8px">🎉</div>
        <h1>Registration Confirmed!</h1>
        <p>You're all set for the event</p>
    </div>
    <div class="body">
        <p>Hi <strong>{{ $registration->user->name }}</strong>,</p>
        <p>Your registration has been confirmed! Here are your event details:</p>

        <div class="event-card">
            <h2 style="margin:0 0 16px;color:#1e293b;font-size:20px">{{ $registration->event->title }}</h2>
            <div class="detail-row"><span class="detail-label">📅 Date</span><span class="detail-value">{{ $registration->event->start_date->format('l, F j, Y') }}</span></div>
            <div class="detail-row"><span class="detail-label">🕐 Time</span><span class="detail-value">{{ $registration->event->start_date->format('g:i A') }}</span></div>
            <div class="detail-row"><span class="detail-label">📍 Venue</span><span class="detail-value">{{ $registration->event->is_online ? 'Online Event' : ($registration->event->venue?->name ?? 'TBA') }}</span></div>
            <div class="detail-row"><span class="detail-label">🎟 Ticket</span><span class="detail-value">{{ $registration->ticketType->name }} × {{ $registration->quantity }}</span></div>
            <div class="detail-row"><span class="detail-label">📋 Reg #</span><span class="detail-value" style="font-family:monospace">{{ $registration->registration_number }}</span></div>
            <div class="detail-row"><span class="detail-label">💰 Amount</span><span class="detail-value">{{ $registration->final_amount == 0 ? 'Free' : '₱' . number_format($registration->final_amount, 2) }}</span></div>
        </div>

        <p style="color:#64748b;font-size:14px">Your QR code tickets are attached to this email as a PDF. Present the QR code at the venue entrance for check-in.</p>

        <a href="{{ route('attendee.tickets.show', $registration) }}" class="btn">View My Tickets →</a>
    </div>
    <div class="footer">
        <p>EventPro — Your Event Management Platform</p>
        <p>If you have questions, please contact the event organizer.</p>
    </div>
</div>
</body>
</html>
