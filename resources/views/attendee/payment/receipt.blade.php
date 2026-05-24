<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Receipt — {{ $payment->transaction_id }}</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Inter', Arial, sans-serif; background: #f0f4ff; min-height: 100vh; padding: 40px 20px; }

  .page-wrapper { max-width: 720px; margin: 0 auto; }

  /* Action Bar */
  .action-bar { display: flex; gap: 12px; justify-content: flex-end; margin-bottom: 24px; }
  .btn { padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
  .btn-primary { background: #4f46e5; color: #fff; }
  .btn-outline { background: #fff; color: #374151; border: 1px solid #e2e8f0; }
  .btn:hover { opacity: .9; }

  /* Receipt Card */
  .receipt { background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.1); }

  /* Header */
  .receipt-header { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #fff; padding: 40px; text-align: center; position: relative; overflow: hidden; }
  .receipt-header::before { content: ''; position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,.06); border-radius: 50%; }
  .receipt-header::after { content: ''; position: absolute; bottom: -60px; left: -40px; width: 180px; height: 180px; background: rgba(255,255,255,.04); border-radius: 50%; }
  .receipt-header .brand { font-size: 14px; opacity: .8; letter-spacing: .1em; text-transform: uppercase; margin-bottom: 16px; }
  .receipt-header .success-icon { font-size: 56px; margin-bottom: 12px; }
  .receipt-header h1 { font-size: 28px; font-weight: 700; margin-bottom: 8px; }
  .receipt-header .subtitle { opacity: .85; font-size: 15px; }
  .receipt-header .txn-id { margin-top: 16px; background: rgba(255,255,255,.15); display: inline-block; padding: 6px 20px; border-radius: 999px; font-size: 13px; font-family: monospace; letter-spacing: .05em; }

  /* Perforated Divider */
  .perforated { display: flex; align-items: center; background: #f8fafc; }
  .perf-circle { width: 28px; height: 28px; border-radius: 50%; background: #f0f4ff; flex-shrink: 0; }
  .perf-line { flex: 1; height: 2px; background: repeating-linear-gradient(90deg, #e2e8f0 0, #e2e8f0 10px, transparent 10px, transparent 20px); }

  /* Body */
  .receipt-body { padding: 40px; }

  /* Event Banner */
  .event-banner { display: flex; gap: 20px; align-items: center; background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 14px; padding: 20px; margin-bottom: 32px; border: 1px solid #e2e8f0; }
  .event-banner img { width: 100px; height: 75px; object-fit: cover; border-radius: 10px; flex-shrink: 0; }
  .event-banner .event-info h2 { font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
  .event-banner .event-meta { display: flex; flex-direction: column; gap: 4px; }
  .event-banner .event-meta span { font-size: 13px; color: #64748b; display: flex; align-items: center; gap: 6px; }
  .event-badge { margin-left: auto; flex-shrink: 0; }
  .badge { padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: 600; }
  .badge-success { background: #dcfce7; color: #166534; }
  .badge-primary { background: #ede9fe; color: #6d28d9; }

  /* Section Title */
  .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #94a3b8; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9; }

  /* Detail Grid */
  .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 32px; }
  .detail-item label { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; display: block; margin-bottom: 4px; }
  .detail-item span { font-size: 15px; font-weight: 600; color: #1e293b; }
  .detail-item .mono { font-family: monospace; font-size: 13px; }

  /* Ticket Types Table */
  .ticket-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
  .ticket-table th { background: #f8fafc; font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: #64748b; font-weight: 600; padding: 10px 16px; text-align: left; }
  .ticket-table td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #374151; }
  .ticket-table tr:last-child td { border-bottom: none; }
  .ticket-table .ticket-name { font-weight: 600; color: #1e293b; }
  .ticket-table .ticket-type-badge { font-size: 11px; padding: 2px 10px; border-radius: 999px; margin-left: 8px; }
  .type-vip  { background: #fef3c7; color: #92400e; }
  .type-free { background: #dcfce7; color: #166534; }
  .type-paid { background: #dbeafe; color: #1e40af; }

  /* Price Summary */
  .price-summary { background: #f8fafc; border-radius: 12px; padding: 20px; margin-bottom: 32px; }
  .price-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 14px; }
  .price-row .label { color: #64748b; }
  .price-row .value { font-weight: 500; color: #1e293b; }
  .price-row.discount .label { color: #16a34a; }
  .price-row.discount .value { color: #16a34a; }
  .price-divider { border: none; border-top: 1px solid #e2e8f0; margin: 12px 0; }
  .price-row.total { margin-bottom: 0; }
  .price-row.total .label { font-size: 16px; font-weight: 700; color: #1e293b; }
  .price-row.total .value { font-size: 22px; font-weight: 800; color: #4f46e5; }

  /* Payment Method */
  .payment-method { display: flex; align-items: center; gap: 16px; background: #f8fafc; border-radius: 12px; padding: 16px 20px; margin-bottom: 32px; border: 1px solid #e2e8f0; }
  .payment-method .pm-icon { font-size: 28px; }
  .payment-method .pm-info label { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; display: block; }
  .payment-method .pm-info span { font-size: 15px; font-weight: 600; color: #1e293b; }
  .payment-method .pm-status { margin-left: auto; }

  /* Attendee Info */
  .attendee-card { display: flex; align-items: center; gap: 16px; padding: 16px 0; border-bottom: 1px solid #f1f5f9; }
  .attendee-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }
  .attendee-info label { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; display: block; }
  .attendee-info span { font-size: 15px; font-weight: 600; color: #1e293b; }
  .attendee-info small { font-size: 13px; color: #64748b; }

  /* QR Section */
  .qr-section { text-align: center; padding: 28px; background: #f8fafc; border-radius: 14px; margin-bottom: 32px; border: 2px dashed #e2e8f0; }
  .qr-section .qr-img { width: 140px; height: 140px; margin: 0 auto 12px; }
  .qr-section .qr-img img { width: 100%; height: 100%; }
  .qr-section .qr-fallback { width: 140px; height: 140px; background: #e2e8f0; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 48px; margin: 0 auto 12px; }
  .qr-section p { font-size: 13px; color: #64748b; margin-bottom: 6px; }
  .qr-section .ticket-no { font-family: monospace; font-size: 13px; color: #4f46e5; font-weight: 600; }

  /* Important Notes */
  .notes-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 16px 20px; margin-bottom: 32px; }
  .notes-box h4 { font-size: 13px; font-weight: 700; color: #92400e; margin-bottom: 10px; }
  .notes-box ul { list-style: none; padding: 0; }
  .notes-box ul li { font-size: 13px; color: #78350f; margin-bottom: 6px; padding-left: 20px; position: relative; }
  .notes-box ul li::before { content: '→'; position: absolute; left: 0; color: #f59e0b; }

  /* Action Buttons */
  .receipt-actions { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 8px; }
  .action-btn { padding: 14px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; transition: opacity .2s; }
  .action-btn:hover { opacity: .85; }
  .action-primary { background: #4f46e5; color: #fff; }
  .action-success { background: #22c55e; color: #fff; }
  .action-outline { background: #fff; color: #374151; border: 1px solid #e2e8f0; }

  /* Footer */
  .receipt-footer { background: #1e293b; color: #fff; padding: 28px 40px; text-align: center; }
  .receipt-footer .brand { font-size: 18px; font-weight: 700; color: #818cf8; margin-bottom: 8px; }
  .receipt-footer p { font-size: 13px; color: #94a3b8; line-height: 1.7; }
  .receipt-footer .divider { border-color: rgba(255,255,255,.1); margin: 16px 0; }
  .receipt-footer .footer-links { display: flex; justify-content: center; gap: 24px; margin-top: 12px; }
  .receipt-footer .footer-links a { color: #64748b; font-size: 12px; text-decoration: none; }

  /* Print Styles */
  @media print {
    body { background: #fff; padding: 0; }
    .action-bar, .receipt-actions, .action-btn { display: none !important; }
    .receipt { box-shadow: none; border-radius: 0; }
    .receipt-footer { background: #1e293b !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .receipt-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  }

  @media (max-width: 600px) {
    .receipt-body { padding: 24px; }
    .receipt-header { padding: 28px 24px; }
    .detail-grid { grid-template-columns: 1fr; }
    .receipt-actions { grid-template-columns: 1fr; }
    .event-banner { flex-direction: column; }
    .event-badge { margin-left: 0; }
  }
</style>
</head>
<body>

<div class="page-wrapper">

    {{-- Action Bar --}}
    <div class="action-bar">
        <a href="{{ route('attendee.dashboard') }}" class="btn btn-outline">
            ← Back to Dashboard
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Print Receipt
        </button>
    </div>

    <div class="receipt">

        {{-- Header --}}
        <div class="receipt-header">
            <div class="brand">🎟 EventPro</div>
            <div class="success-icon">✅</div>
            <h1>Payment Confirmed!</h1>
            <p class="subtitle">Your registration is complete. See you at the event!</p>
            <div class="txn-id">{{ $payment->transaction_id }}</div>
        </div>

        {{-- Perforated --}}
        <div class="perforated">
            <div class="perf-circle"></div>
            <div class="perf-line"></div>
            <div class="perf-circle"></div>
        </div>

        <div class="receipt-body">

            {{-- Event Info --}}
            <div class="event-banner">
                <img src="{{ $payment->registration->event->banner_url }}"
                     alt="{{ $payment->registration->event->title }}">
                <div class="event-info">
                    <h2>{{ $payment->registration->event->title }}</h2>
                    <div class="event-meta">
                        <span>📅 {{ $payment->registration->event->start_date->format('l, F j, Y') }}</span>
                        <span>🕐 {{ $payment->registration->event->start_date->format('g:i A') }} – {{ $payment->registration->event->end_date->format('g:i A') }}</span>
                        <span>📍 {{ $payment->registration->event->is_online ? 'Online Event' : ($payment->registration->event->venue?->name ?? 'TBA') }}</span>
                        @if(!$payment->registration->event->is_online && $payment->registration->event->venue)
                        <span>🗺️ {{ $payment->registration->event->venue->address }}, {{ $payment->registration->event->venue->city }}</span>
                        @endif
                    </div>
                </div>
                <div class="event-badge">
                    <span class="badge badge-success">✓ Confirmed</span>
                </div>
            </div>

            {{-- Transaction Details --}}
            <div class="section-title">Transaction Details</div>
            <div class="detail-grid" style="margin-bottom:32px">
                <div class="detail-item">
                    <label>Registration #</label>
                    <span class="mono">{{ $payment->registration->registration_number }}</span>
                </div>
                <div class="detail-item">
                    <label>Transaction ID</label>
                    <span class="mono">{{ $payment->transaction_id }}</span>
                </div>
                <div class="detail-item">
                    <label>Date & Time Paid</label>
                    <span>{{ $payment->paid_at->format('M d, Y') }}</span>
                    <small style="color:#64748b;font-size:12px">{{ $payment->paid_at->format('g:i A') }}</small>
                </div>
                <div class="detail-item">
                    <label>Payment Status</label>
                    <span><span class="badge badge-success">✓ Completed</span></span>
                </div>
                <div class="detail-item">
                    <label>Gateway Reference</label>
                    <span class="mono" style="font-size:12px">{{ $payment->gateway_transaction_id ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <label>Currency</label>
                    <span>{{ $payment->currency }} (Philippine Peso)</span>
                </div>
            </div>

            {{-- Ticket Details --}}
            <div class="section-title">Ticket Details</div>
            <table class="ticket-table">
                <thead>
                    <tr>
                        <th>Ticket Type</th>
                        <th>Category</th>
                        <th style="text-align:center">Qty</th>
                        <th style="text-align:right">Unit Price</th>
                        <th style="text-align:right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span class="ticket-name">{{ $payment->registration->ticketType->name }}</span>
                            <span class="ticket-type-badge type-{{ $payment->registration->ticketType->type }}">
                                {{ strtoupper($payment->registration->ticketType->type) }}
                            </span>
                        </td>
                        <td>{{ $payment->registration->event->category->name }}</td>
                        <td style="text-align:center">{{ $payment->registration->quantity }}</td>
                        <td style="text-align:right">
                            {{ $payment->registration->unit_price == 0 ? 'FREE' : '₱' . number_format($payment->registration->unit_price, 2) }}
                        </td>
                        <td style="text-align:right;font-weight:600">
                            {{ $payment->registration->total_amount == 0 ? 'FREE' : '₱' . number_format($payment->registration->total_amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Price Summary --}}
            <div class="price-summary">
                <div class="price-row">
                    <span class="label">Subtotal ({{ $payment->registration->quantity }} ticket{{ $payment->registration->quantity > 1 ? 's' : '' }})</span>
                    <span class="value">₱{{ number_format($payment->registration->total_amount, 2) }}</span>
                </div>
                @if($payment->registration->discount_amount > 0)
                <div class="price-row discount">
                    <span class="label">
                        🏷️ Discount
                        @if($payment->registration->coupon_code)
                            (Code: {{ $payment->registration->coupon_code }})
                        @endif
                    </span>
                    <span class="value">−₱{{ number_format($payment->registration->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="price-row">
                    <span class="label">Service Fee</span>
                    <span class="value">₱0.00</span>
                </div>
                <hr class="price-divider">
                <div class="price-row total">
                    <span class="label">Total Amount Paid</span>
                    <span class="value">
                        {{ $payment->amount == 0 ? 'FREE' : '₱' . number_format($payment->amount, 2) }}
                    </span>
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="section-title">Payment Method</div>
            <div class="payment-method">
                <div class="pm-icon">
                    {{ match($payment->gateway) {
                        'stripe'        => '💳',
                        'paypal'        => '🅿️',
                        'gcash'         => '📱',
                        'bank_transfer' => '🏦',
                        default         => '💰'
                    } }}
                </div>
                <div class="pm-info">
                    <label>Payment Method</label>
                    <span>
                        {{ match($payment->gateway) {
                            'stripe'        => 'Credit / Debit Card',
                            'paypal'        => 'PayPal',
                            'gcash'         => 'GCash',
                            'bank_transfer' => 'Bank Transfer',
                            default         => ucfirst($payment->gateway)
                        } }}
                    </span>
                </div>
                <div class="pm-status">
                    <span class="badge badge-success">✓ Verified</span>
                </div>
            </div>

            {{-- Attendee Info --}}
            <div class="section-title">Attendee Information</div>
            <div style="margin-bottom:32px">
                <div class="attendee-card">
                    <img src="{{ $payment->user->avatar_url }}"
                         class="attendee-avatar" alt="{{ $payment->user->name }}">
                    <div class="attendee-info">
                        <label>Full Name</label>
                        <span>{{ $payment->user->name }}</span>
                        <small>{{ $payment->user->email }}</small>
                    </div>
                    @if($payment->user->phone)
                    <div class="attendee-info" style="margin-left:auto">
                        <label>Phone</label>
                        <span>{{ $payment->user->phone }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- QR Code --}}
            @if($payment->registration->tickets->count())
            <div class="section-title">Your Ticket QR Code</div>
            @php $ticket = $payment->registration->tickets->first(); @endphp
            <div class="qr-section">
                @if($ticket->qr_code_path && file_exists(storage_path('app/public/' . $ticket->qr_code_path)))
                    <div class="qr-img">
                        <img src="{{ asset('storage/' . $ticket->qr_code_path) }}" alt="QR Code">
                    </div>
                @else
                    <div class="qr-fallback">🎫</div>
                @endif
                <p>Present this QR code at the venue entrance for check-in</p>
                <div class="ticket-no">{{ $ticket->ticket_number }}</div>
                @if($payment->registration->quantity > 1)
                <p style="margin-top:8px;color:#94a3b8;font-size:12px">
                    + {{ $payment->registration->quantity - 1 }} more ticket(s) —
                    <a href="{{ route('attendee.tickets.show', $payment->registration) }}" style="color:#4f46e5">View All Tickets</a>
                </p>
                @endif
            </div>
            @endif

            {{-- Important Notes --}}
            <div class="notes-box">
                <h4>⚠️ Important Reminders</h4>
                <ul>
                    <li>Please arrive at least 30 minutes before the event starts.</li>
                    <li>Bring a valid ID that matches your registration name.</li>
                    <li>This receipt serves as proof of payment — keep it safe.</li>
                    <li>Tickets are non-transferable and non-refundable unless the event is cancelled.</li>
                    @if($payment->registration->event->is_online)
                    <li>This is an online event — check your email for the meeting link.</li>
                    @else
                    <li>Venue: {{ $payment->registration->event->venue?->name ?? 'TBA' }}, {{ $payment->registration->event->venue?->city ?? '' }}</li>
                    @endif
                </ul>
            </div>

            {{-- Action Buttons --}}
            <div class="receipt-actions">
                <a href="{{ route('attendee.tickets.show', $payment->registration) }}"
                   class="action-btn action-primary">
                    🎟️ View Ticket
                </a>
                <a href="{{ route('attendee.tickets.download', $payment->registration->tickets->first()) }}"
                   class="action-btn action-success">
                    📥 Download Ticket
                </a>
                <button onclick="window.print()" class="action-btn action-outline">
                    🖨️ Print Receipt
                </button>
            </div>

        </div>

        {{-- Footer --}}
        <div class="receipt-footer">
            <div class="brand">🎟 EventPro</div>
            <p>
                Thank you for your purchase, <strong style="color:#fff">{{ $payment->user->name }}</strong>!<br>
                A confirmation email has been sent to <strong style="color:#fff">{{ $payment->user->email }}</strong>
            </p>
            <hr class="divider">
            <p style="font-size:12px">
                This is an official receipt generated by EventPro.<br>
                Receipt generated on {{ now()->format('F j, Y \a\t g:i A') }}
            </p>
            <div class="footer-links">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('events.index') }}">Browse Events</a>
                <a href="{{ route('attendee.dashboard') }}">Dashboard</a>
                <a href="{{ route('attendee.tickets.index') }}">My Tickets</a>
            </div>
        </div>

    </div>
</div>

</body>
</html>