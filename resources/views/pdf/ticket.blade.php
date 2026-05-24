<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Ticket — {{ $ticket->event->title }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; background: #f0f4ff; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
  .ticket { width: 680px; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 40px rgba(0,0,0,.15); }
  .ticket-header { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; padding: 32px; }
  .ticket-header .category { font-size: 12px; opacity: .8; text-transform: uppercase; letter-spacing: .1em; margin-bottom: 8px; }
  .ticket-header h1 { font-size: 26px; font-weight: 700; margin-bottom: 8px; }
  .ticket-header .type { display: inline-block; background: rgba(255,255,255,.2); border-radius: 999px; padding: 4px 16px; font-size: 13px; }
  .divider { display: flex; align-items: center; }
  .divider-line { flex: 1; height: 1px; background: repeating-linear-gradient(90deg, #e2e8f0 0, #e2e8f0 8px, transparent 8px, transparent 16px); }
  .divider-circle { width: 24px; height: 24px; background: #f0f4ff; border-radius: 50%; flex-shrink: 0; }
  .ticket-body { padding: 32px; }
  .details { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px; }
  .detail-item label { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #94a3b8; display: block; margin-bottom: 4px; }
  .detail-item span { font-size: 15px; font-weight: 600; color: #1e293b; }
  .qr-section { text-align: center; padding: 24px; border: 2px dashed #e2e8f0; border-radius: 12px; margin-bottom: 24px; }
  .qr-section img { width: 160px; height: 160px; margin-bottom: 12px; }
  .qr-section .qr-fallback { width: 160px; height: 160px; background: #f1f5f9; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 48px; margin-bottom: 12px; }
  .qr-code-text { font-family: monospace; font-size: 12px; color: #64748b; word-break: break-all; }
  .ticket-number { font-family: monospace; font-size: 13px; color: #64748b; }
  .footer { display: flex; justify-content: space-between; align-items: center; padding: 16px 32px; background: #f8fafc; border-top: 1px solid #e2e8f0; }
  .footer .brand { font-weight: 700; color: #4f46e5; }
  .print-btn { display: block; margin: 24px auto 0; padding: 12px 40px; background: #4f46e5; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; }
  @media print {
    body { background: #fff; padding: 0; }
    .ticket { box-shadow: none; }
    .print-btn { display: none; }
  }
</style>
</head>
<body>

<div class="ticket">
    <div class="ticket-header">
        <div class="category">{{ $ticket->event->category->name ?? 'Event' }}</div>
        <h1>{{ $ticket->event->title }}</h1>
        <span class="type">{{ strtoupper($ticket->ticketType->name) }}</span>
    </div>

    <div class="divider">
        <div class="divider-circle"></div>
        <div class="divider-line"></div>
        <div class="divider-circle"></div>
    </div>

    <div class="ticket-body">
        <div class="details">
            <div class="detail-item">
                <label>📅 Date</label>
                <span>{{ $ticket->event->start_date->format('l, F j, Y') }}</span>
            </div>
            <div class="detail-item">
                <label>🕐 Time</label>
                <span>{{ $ticket->event->start_date->format('g:i A') }} – {{ $ticket->event->end_date->format('g:i A') }}</span>
            </div>
            <div class="detail-item">
                <label>📍 Venue</label>
                <span>{{ $ticket->event->is_online ? 'Online Event' : ($ticket->event->venue?->name ?? 'TBA') }}</span>
            </div>
            <div class="detail-item">
                <label>👤 Attendee</label>
                <span>{{ $ticket->user->name }}</span>
            </div>
            <div class="detail-item">
                <label>🎟 Ticket Type</label>
                <span>{{ $ticket->ticketType->name }}</span>
            </div>
            <div class="detail-item">
                <label>📋 Registration</label>
                <span class="ticket-number">{{ $ticket->registration->registration_number }}</span>
            </div>
        </div>

        <div class="qr-section">
            @php
                $qrPath = storage_path('app/public/' . $ticket->qr_code_path);
            @endphp
            @if($ticket->qr_code_path && file_exists($qrPath))
                <img src="{{ asset('storage/' . $ticket->qr_code_path) }}" alt="QR Code">
            @else
                <div class="qr-fallback">🎫</div>
            @endif
            <div class="qr-code-text">{{ $ticket->ticket_number }}</div>
            <div style="font-size:12px;color:#94a3b8;margin-top:6px">Present this QR code at the venue entrance</div>
        </div>

        <div style="text-align:center">
            <span class="badge" style="background:{{ $ticket->status === 'valid' ? '#dcfce7' : '#fee2e2' }};color:{{ $ticket->status === 'valid' ? '#166534' : '#991b1b' }};padding:6px 16px;border-radius:999px;font-size:13px;font-weight:600;">
                {{ strtoupper($ticket->status) }}
            </span>
        </div>
    </div>

    <div class="footer">
        <span class="brand">🎟 EventPro</span>
        <span style="font-size:12px;color:#94a3b8;">Generated {{ now()->format('M d, Y') }}</span>
    </div>
</div>

<button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>

</body>
</html>
