@extends('layouts.app')
@section('title', 'My Tickets')

@section('hide_hero', true)

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">My Tickets</h3>
            <p class="text-muted mb-0">All your event registrations and tickets</p>
        </div>
        <a href="{{ route('events.index') }}" class="btn btn-primary">
            <i class="bi bi-search me-2"></i>Find More Events
        </a>
    </div>

    @forelse($registrations as $reg)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <img src="{{ $reg->event->banner_url }}" class="img-fluid rounded-3"
                         style="height:70px;width:100%;object-fit:cover;" alt="">
                </div>
                <div class="col-md-5">
                    <h6 class="fw-semibold mb-1">{{ $reg->event->title }}</h6>
                    <div class="text-muted small">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $reg->event->start_date->format('M d, Y • g:i A') }}
                    </div>
                    <div class="text-muted small">
                        <i class="bi bi-ticket me-1"></i>
                        {{ $reg->ticketType->name }} &times; {{ $reg->quantity }}
                        &nbsp;|&nbsp;
                        <i class="bi bi-hash me-1"></i>{{ $reg->registration_number }}
                    </div>
                </div>
                <div class="col-md-2 text-center">
                    <span class="badge rounded-pill
                        {{ match($reg->status) {
                            'confirmed'  => 'bg-success',
                            'attended'   => 'bg-primary',
                            'cancelled'  => 'bg-danger',
                            'pending'    => 'bg-warning text-dark',
                            default      => 'bg-secondary'
                        } }}">
                        {{ ucfirst($reg->status) }}
                    </span>
                    <div class="mt-1">
                        <span class="badge rounded-pill
                            {{ match($reg->payment_status) {
                                'paid'     => 'bg-success',
                                'free'     => 'bg-info',
                                'refunded' => 'bg-secondary',
                                'failed'   => 'bg-danger',
                                default    => 'bg-warning text-dark'
                            } }}">
                            {{ ucfirst($reg->payment_status) }}
                        </span>
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <div class="fw-bold text-primary mb-2">
                        {{ $reg->final_amount == 0 ? 'Free' : '₱' . number_format($reg->final_amount, 2) }}
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        @if(in_array($reg->status, ['confirmed','attended']))
                            <a href="{{ route('attendee.tickets.show', $reg) }}"
                               class="btn btn-sm btn-primary">
                                <i class="bi bi-ticket me-1"></i>View Ticket
                            </a>
                        @endif
                        @if($reg->payment && $reg->payment->status === 'completed')
                            <a href="{{ route('attendee.payment.receipt', $reg->payment) }}"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-receipt me-1"></i>Receipt
                            </a>
                        @endif
                        @if($reg->status === 'pending' && $reg->payment_status === 'pending')
                            <a href="{{ route('attendee.payment.show', $reg) }}"
                               class="btn btn-sm btn-warning">
                                <i class="bi bi-credit-card me-1"></i>Pay Now
                            </a>
                        @endif
                        @if($reg->event->status === 'completed' && $reg->status === 'attended')
                            @php $hasFeedback = $reg->event->feedbacks->where('user_id', auth()->id())->count(); @endphp
                            @if(!$hasFeedback)
                                <a href="{{ route('attendee.feedback.create', $reg->event) }}"
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-star me-1"></i>Review
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="text-muted">
            <i class="bi bi-ticket-perforated d-block fs-1 mb-3"></i>
            <h5>No tickets yet</h5>
            <p class="small mb-3">Register for an event to see your tickets here.</p>
            <a href="{{ route('events.index') }}" class="btn btn-primary">Browse Events</a>
        </div>
    </div>
    @endforelse

    <div class="mt-4">{{ $registrations->links() }}</div>
</div>
@endsection