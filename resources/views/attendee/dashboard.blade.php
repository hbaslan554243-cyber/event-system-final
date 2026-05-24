{{-- resources/views/attendee/dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'My Dashboard')

@section('hero')
@endsection

@push('styles')
<style>
    .ep-hero-wrap {
        background: var(--ep-bg) !important;
        padding-bottom: 0 !important;
        min-height: 0 !important;
    }
    .ep-hero-wrap::after { display: none !important; }
    .ep-hero-content { display: none !important; }

    .ep-nav {
        background: #fff !important;
        border-bottom: 1px solid var(--ep-border) !important;
        box-shadow: 0 1px 8px rgba(0,0,0,.06) !important;
    }
    .ep-brand { color: var(--ep-primary) !important; }
    .ep-nav-links .nav-link { color: var(--ep-text) !important; }
    .ep-nav-links .nav-link:hover,
    .ep-nav-links .nav-link.active {
        color: var(--ep-primary) !important;
        background: var(--ep-primary-lt) !important;
    }
    .btn-ep-ghost { color: var(--ep-text) !important; }
    .btn-ep-ghost:hover {
        color: var(--ep-primary) !important;
        background: var(--ep-primary-lt) !important;
    }
    .ep-avatar-btn {
        background: rgba(0,0,0,.06) !important;
        border-color: rgba(0,0,0,.15) !important;
        color: var(--ep-text) !important;
    }
    .ep-avatar-btn:hover {
        background: rgba(0,0,0,.1) !important;
        color: var(--ep-text) !important;
    }
    .ep-avatar-name { color: var(--ep-text) !important; }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">My Dashboard</h3>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
        <a href="{{ route('events.index') }}" class="btn btn-primary"><i class="bi bi-search me-2"></i>Find Events</a>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fs-3 fw-bold text-primary">{{ $stats['upcoming_events'] }}</div>
                <div class="small text-muted">Upcoming Events</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fs-3 fw-bold text-success">{{ $stats['attended_events'] }}</div>
                <div class="small text-muted">Events Attended</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fs-3 fw-bold text-info">₱{{ number_format($stats['total_spent'], 0) }}</div>
                <div class="small text-muted">Total Spent</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fs-3 fw-bold text-warning">{{ $stats['pending_feedback'] }}</div>
                <div class="small text-muted">Pending Reviews</div>
            </div>
        </div>
    </div>

    {{-- Upcoming Events --}}
    <h5 class="fw-bold mb-3">My Upcoming Events</h5>
    @forelse($upcoming as $reg)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <img src="{{ $reg->event->banner_url }}" class="rounded-3"
                         style="width:100%;height:70px;object-fit:cover;">
                </div>
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-1">{{ $reg->event->title }}</h6>
                    <div class="text-muted small">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $reg->event->start_date->format('M d, Y • g:i A') }}
                        <span class="mx-2">·</span>
                        <i class="bi bi-ticket me-1"></i>
                        {{ $reg->ticketType->name }} × {{ $reg->quantity }}
                    </div>
                    <div class="text-muted small">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $reg->event->is_online ? 'Online' : ($reg->event->venue?->name ?? 'TBA') }}
                    </div>
                </div>
                <div class="col-md-2 text-center">
                    <span class="badge rounded-pill {{ in_array($reg->payment_status, ['paid','free']) ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $reg->payment_status === 'free' ? 'Free' : ucfirst($reg->payment_status) }}
                    </span>
                    <div class="text-muted small mt-1">
                        {{ $reg->event->start_date->diffForHumans() }}
                    </div>
                </div>
                <div class="col-md-2 text-end">
                    <a href="{{ route('attendee.tickets.show', $reg) }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-ticket me-1"></i>View Ticket
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="text-muted">
            <i class="bi bi-calendar3 d-block fs-1 mb-3"></i>
            <h6>No upcoming events</h6>
            <p class="small mb-3">You haven't registered for any upcoming events yet.</p>
            <a href="{{ route('events.index') }}" class="btn btn-primary">Browse Events</a>
        </div>
    </div>
    @endforelse

    {{-- Events Pending Review --}}
    @php
        $pendingReviews = \App\Models\Registration::with('event')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['confirmed', 'attended'])
            ->whereHas('event', fn($q) => $q->whereIn('status', ['completed', 'ongoing']))
            ->whereDoesntHave('event.feedbacks', fn($q) => $q->where('user_id', auth()->id()))
            ->latest()->get();
    @endphp

    @if($pendingReviews->count())
    <h5 class="fw-bold mb-3 mt-4">⭐ Pending Reviews</h5>
    @foreach($pendingReviews as $reg)
    <div class="card border-0 shadow-sm mb-3" style="border-left: 4px solid #f59e0b !important">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <img src="{{ $reg->event->banner_url }}" class="rounded-3"
                         style="width:100%;height:60px;object-fit:cover;">
                </div>
                <div class="col-md-7">
                    <h6 class="fw-semibold mb-1">{{ $reg->event->title }}</h6>
                    <div class="text-muted small">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $reg->event->start_date->format('M d, Y') }}
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <a href="{{ route('attendee.feedback.create', $reg->event) }}"
                       class="btn btn-warning btn-sm">
                        <i class="bi bi-star me-1"></i>Leave a Review
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @endif
</div>
@endsection