{{-- resources/views/emails/event-reminder.blade.php --}}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; background:#f0f4ff; margin:0; padding:20px; }
.container { max-width:600px; margin:0 auto; background:#fff; border-radius:12px; overflow:hidden; }
.header { background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; padding:32px; text-align:center; }
.body { padding:32px; }
.detail { display:flex; gap:12px; margin-bottom:10px; font-size:14px; }
.label { color:#64748b; width:80px; flex-shrink:0; }
.value { font-weight:600; color:#1e293b; }
.btn { display:inline-block; background:#4f46e5; color:#fff !important; text-decoration:none; padding:12px 32px; border-radius:8px; font-weight:bold; }
.footer { background:#f8fafc; padding:20px 32px; text-align:center; font-size:12px; color:#94a3b8; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <div style="font-size:3rem;margin-bottom:8px">⏰</div>
        <h2 style="margin:0;font-size:22px">Event Reminder!</h2>
        <p style="margin:8px 0 0;opacity:.9">Your event is coming up soon</p>
    </div>
    <div class="body">
        <p>Hi <strong>{{ $reg->user->name }}</strong>,</p>
        <p>This is a friendly reminder that <strong>{{ $event->title }}</strong> is happening soon!</p>
        <div style="background:#fffbeb;border-radius:8px;padding:20px;margin:20px 0;border-left:4px solid #f59e0b;">
            <div class="detail"><span class="label">📅 Date</span><span class="value">{{ $event->start_date->format('l, F j, Y') }}</span></div>
            <div class="detail"><span class="label">🕐 Time</span><span class="value">{{ $event->start_date->format('g:i A') }}</span></div>
            <div class="detail"><span class="label">📍 Venue</span><span class="value">{{ $event->is_online ? 'Online Event' : ($event->venue?->name ?? 'TBA') }}</span></div>
            @if(!$event->is_online && $event->venue)
            <div class="detail"><span class="label">📌 Address</span><span class="value">{{ $event->venue->address }}, {{ $event->venue->city }}</span></div>
            @endif
        </div>
        <p>Don't forget to bring your ticket! You can download it from the link below.</p>
        <a href="{{ route('attendee.tickets.show', $reg) }}" class="btn">View My Ticket →</a>
    </div>
    <div class="footer">EventPro — Sent because you registered for this event</div>
</div>
</body>
</html>
