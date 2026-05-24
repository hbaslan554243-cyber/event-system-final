@extends('layouts.admin')
@section('title', 'Organizer Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Organizer Dashboard</h4>
        <p class="text-muted mb-0">Hello, {{ auth()->user()->name }}! Here's your events overview.</p>
    </div>
    <a href="{{ route('organizer.events.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Create Event</a>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card border-0 shadow-sm p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small mb-1">My Events</div>
                    <div class="fs-3 fw-bold">{{ $stats['total_events'] }}</div>
                    <div class="small text-success">{{ $stats['active_events'] }} active</div>
                </div>
                <div class="p-3 rounded-3 bg-primary bg-opacity-10"><i class="bi bi-calendar3 fs-4 text-primary"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card border-0 shadow-sm p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small mb-1">Registrations</div>
                    <div class="fs-3 fw-bold">{{ number_format($stats['total_registrations']) }}</div>
                    <div class="small text-muted">all time</div>
                </div>
                <div class="p-3 rounded-3" style="background:#fef3c7"><i class="bi bi-person-check fs-4" style="color:#d97706"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card border-0 shadow-sm p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small mb-1">Total Revenue</div>
                    <div class="fs-3 fw-bold">₱{{ number_format($stats['total_revenue'], 0) }}</div>
                    <div class="small text-muted">completed</div>
                </div>
                <div class="p-3 rounded-3" style="background:#dcfce7"><i class="bi bi-cash fs-4" style="color:#16a34a"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card border-0 shadow-sm p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small mb-1">Quick Actions</div>
                    <div class="mt-2 d-flex flex-column gap-1">
                        <a href="{{ route('organizer.events.create') }}" class="btn btn-sm btn-primary">+ New Event</a>
                    </div>
                </div>
                <div class="p-3 rounded-3" style="background:#ede9fe"><i class="bi bi-lightning fs-4" style="color:#7c3aed"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Upcoming Events --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between">
                <h6 class="fw-semibold mb-0"><i class="bi bi-calendar-check me-2 text-primary"></i>Upcoming Events</h6>
                <a href="{{ route('organizer.events.index') }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($upcoming_events as $event)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div class="text-center bg-primary bg-opacity-10 rounded-3 p-2" style="min-width:50px">
                        <div class="fw-bold text-primary">{{ $event->start_date->format('d') }}</div>
                        <div class="text-muted small" style="font-size:.65rem">{{ $event->start_date->format('M') }}</div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-medium small">{{ Str::limit($event->title, 40) }}</div>
                        <div class="text-muted" style="font-size:.75rem">
                            <i class="bi bi-geo-alt me-1"></i>{{ $event->venue?->name ?? 'Online' }}
                        </div>
                    </div>
                    <a href="{{ route('organizer.events.show', $event) }}" class="btn btn-sm btn-outline-primary">Manage</a>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar3 d-block fs-2 mb-2"></i>No upcoming events.
                    <div class="mt-2"><a href="{{ route('organizer.events.create') }}" class="btn btn-sm btn-primary">Create One</a></div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Registrations --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-person-plus me-2 text-success"></i>Recent Registrations</h6>
            </div>
            <div class="card-body p-0">
                @forelse($recent_registrations as $reg)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <img src="{{ $reg->user->avatar_url }}" class="rounded-circle" width="36" height="36">
                    <div class="flex-grow-1">
                        <div class="fw-medium small">{{ $reg->user->name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ Str::limit($reg->event->title, 30) }} • {{ $reg->ticketType->name }}</div>
                    </div>
                    <div class="text-end">
                        <span class="badge rounded-pill {{ $reg->payment_status === 'paid' || $reg->payment_status === 'free' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $reg->payment_status === 'free' ? 'Free' : (ucfirst($reg->payment_status)) }}
                        </span>
                        <div class="text-muted" style="font-size:.7rem">{{ $reg->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted"><i class="bi bi-inbox d-block fs-2 mb-2"></i>No registrations yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
