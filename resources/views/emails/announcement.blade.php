{{-- resources/views/emails/announcement.blade.php --}}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; background:#f0f4ff; margin:0; padding:20px; }
.container { max-width:600px; margin:0 auto; background:#fff; border-radius:12px; overflow:hidden; }
.header { padding:24px 32px; border-bottom:1px solid #e2e8f0; }
.badge { display:inline-block; padding:4px 12px; border-radius:999px; font-size:12px; font-weight:600; }
.badge-info { background:#dbeafe; color:#1d4ed8; }
.badge-warning { background:#fef3c7; color:#92400e; }
.badge-urgent { background:#fee2e2; color:#991b1b; }
.badge-update { background:#e0f2fe; color:#0c4a6e; }
.body { padding:32px; }
.footer { background:#f8fafc; padding:20px 32px; text-align:center; font-size:12px; color:#94a3b8; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <span class="badge badge-{{ $announcement->type }}">{{ strtoupper($announcement->type) }}</span>
        <h2 style="margin:12px 0 4px;color:#1e293b;font-size:20px">{{ $announcement->title }}</h2>
        <p style="margin:0;color:#64748b;font-size:14px">{{ $event->title }}</p>
    </div>
    <div class="body">
        <p>Hi <strong>{{ $reg->user->name }}</strong>,</p>
        <p style="line-height:1.8;color:#374151;">{{ $announcement->message }}</p>
        <div style="background:#f8fafc;border-radius:8px;padding:16px;margin:20px 0;">
            <p style="margin:0;font-size:13px;color:#64748b;">
                📅 <strong>Event:</strong> {{ $event->title }}<br>
                📆 <strong>Date:</strong> {{ $event->start_date->format('l, F j, Y') }}<br>
                🕐 <strong>Time:</strong> {{ $event->start_date->format('g:i A') }}
            </p>
        </div>
        <p style="font-size:13px;color:#94a3b8;">— {{ $announcement->creator->name }}, Event Organizer</p>
    </div>
    <div class="footer">EventPro — Event Management Platform</div>
</div>
</body>
</html>
