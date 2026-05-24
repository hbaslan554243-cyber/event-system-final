@extends('layouts.app')
@section('title', $event->title)

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
{{-- Hero --}}
<div class="position-relative" style="height:380px;overflow:hidden;">
    <img src="{{ $event->banner_url }}" class="w-100 h-100" style="object-fit:cover;filter:brightness(.6);" alt="{{ $event->title }}">
    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-end">
        <div class="container pb-4 text-white">
            <div class="d-flex gap-2 mb-2">
                <span class="badge px-3 py-2" style="background:{{ $event->category->color }}">{{ $event->category->name }}</span>
                <span class="badge bg-{{ $event->status === 'ongoing' ? 'success' : ($event->status === 'upcoming' ? 'info' : 'secondary') }} px-3 py-2">
                    {{ ucfirst($event->status) }}
                </span>
                @if($event->is_online)<span class="badge bg-dark px-3 py-2"><i class="bi bi-camera-video me-1"></i>Online</span>@endif
            </div>
            <h1 class="display-5 fw-bold mb-2">{{ $event->title }}</h1>
            <div class="d-flex flex-wrap gap-4 opacity-90">
                <span><i class="bi bi-person-circle me-2"></i>By {{ $event->organizer->name }}</span>
                <span><i class="bi bi-calendar3 me-2"></i>{{ $event->start_date->format('l, F j, Y • g:i A') }}</span>
                @if($event->venue)
                <span><i class="bi bi-geo-alt me-2"></i>{{ $event->venue->name }}, {{ $event->venue->city }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        {{-- Left: Details --}}
        <div class="col-lg-8">

            {{-- About --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="fw-bold mb-3">About This Event</h4>
                    <div class="text-secondary lh-lg">{!! nl2br(e($event->description)) !!}</div>
                </div>
            </div>

            {{-- Venue Info --}}
            @if($event->venue && !$event->is_online)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Venue</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-semibold">{{ $event->venue->name }}</h6>
                            <p class="text-muted mb-1">{{ $event->venue->address }}</p>
                            <p class="text-muted mb-1">{{ $event->venue->city }}, {{ $event->venue->country }}</p>
                            @if($event->venue->contact_phone)
                                <p class="text-muted mb-0"><i class="bi bi-telephone me-1"></i>{{ $event->venue->contact_phone }}</p>
                            @endif
                            <div class="mt-2">
                                <span class="badge bg-light text-dark"><i class="bi bi-people me-1"></i>Capacity: {{ number_format($event->venue->capacity) }}</span>
                            </div>
                            @if($event->venue->amenities)
                            <div class="mt-2 d-flex flex-wrap gap-1">
                                @foreach($event->venue->amenities as $amenity)
                                    <span class="badge bg-primary bg-opacity-10 text-primary small">{{ $amenity }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Gallery --}}
            @if($event->media->where('category','gallery')->count())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-images text-primary me-2"></i>Gallery</h5>
                    <div class="row g-2">
                        @foreach($event->media->where('category','gallery')->take(6) as $m)
                        <div class="col-4">
                            <a href="{{ $m->url }}" target="_blank">
                                <img src="{{ $m->url }}" class="img-fluid rounded" style="height:120px;width:100%;object-fit:cover;" alt="{{ $m->title }}">
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Reviews --}}
            @if($reviews->count())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-star-fill text-warning me-2"></i>Reviews</h5>
                        <div class="text-muted small">{{ number_format($event->avg_rating,1) }}/5 · {{ $event->total_reviews }} reviews</div>
                    </div>
                    @foreach($reviews as $review)
                    <div class="d-flex gap-3 mb-4">
                        <img src="{{ $review->user->avatar_url }}" class="rounded-circle" width="42" height="42">
                        <div>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="fw-semibold">{{ $review->user->name }}</span>
                                <span class="text-warning small">{{ str_repeat('★', $review->overall_rating) }}</span>
                                <span class="text-muted small">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            @if($review->comment)<p class="text-muted mb-1 mt-1 small">{{ $review->comment }}</p>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Ticket Sidebar --}}
        <div class="col-lg-4">
            <div class="sticky-top" style="top:80px">

                {{-- Registration Status --}}
                @if($userRegistration)
                <div class="card border-0 shadow-sm mb-3 border-success" style="border-left:4px solid #22c55e!important">
                    <div class="card-body">
                        <div class="text-success fw-semibold mb-1"><i class="bi bi-check-circle-fill me-2"></i>You're Registered!</div>
                        <div class="small text-muted">Reg #{{ $userRegistration->registration_number }}</div>
                        <a href="{{ route('attendee.tickets.show', $userRegistration) }}" class="btn btn-success btn-sm w-100 mt-2">View Ticket</a>
                    </div>
                </div>
                @endif

                {{-- Tickets --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0 py-3"><h6 class="fw-semibold mb-0">Tickets</h6></div>
                    <div class="card-body p-0">
                        @foreach($event->ticketTypes->where('is_active', true) as $tt)
                        <div class="px-4 py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ $tt->name }}</div>
                                    @if($tt->description)<div class="text-muted small">{{ $tt->description }}</div>@endif
                                    @if($tt->perks)
                                        <div class="mt-1">
                                            @foreach($tt->perks as $perk)<div class="small text-success"><i class="bi bi-check me-1"></i>{{ $perk }}</div>@endforeach
                                        </div>
                                    @endif
                                    <div class="text-muted small mt-1">{{ $tt->quantity_remaining }} left</div>
                                </div>
                                <div class="text-end">
                                    @if($tt->price == 0)
                                        <span class="fw-bold text-success">FREE</span>
                                    @else
                                        <span class="fw-bold text-primary">₱{{ number_format($tt->price, 2) }}</span>
                                    @endif
                                    <div class="small text-muted">per ticket</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer bg-white border-0 py-3">
                        @auth
                            @if(!$userRegistration && in_array($event->status, ['upcoming','ongoing']) && !$event->is_full)
                                <a href="{{ route('attendee.events.checkout', $event) }}" class="btn btn-primary w-100 py-2 fw-semibold">
                                    Register Now <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            @elseif($event->is_full)
                                <button class="btn btn-secondary w-100" disabled>Sold Out</button>
                            @elseif($event->status === 'completed')
                                <button class="btn btn-secondary w-100" disabled>Event Ended</button>
                            @elseif($event->status === 'cancelled')
                                <button class="btn btn-danger w-100" disabled>Event Cancelled</button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary w-100 py-2 fw-semibold">Login to Register</a>
                        @endauth
                    </div>
                </div>

                {{-- Event Info Card --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Event Details</h6>
                        <ul class="list-unstyled small">
                            <li class="d-flex gap-2 mb-2">
                                <i class="bi bi-calendar3 text-primary mt-1"></i>
                                <div>
                                    <div>{{ $event->start_date->format('M d, Y') }}</div>
                                    <div class="text-muted">{{ $event->start_date->format('g:i A') }} – {{ $event->end_date->format('g:i A') }}</div>
                                </div>
                            </li>
                            <li class="d-flex gap-2 mb-2">
                                <i class="bi bi-geo-alt text-primary mt-1"></i>
                                <div>{{ $event->is_online ? 'Online Event' : ($event->venue?->city ?? 'TBA') }}</div>
                            </li>
                            <li class="d-flex gap-2 mb-2">
                                <i class="bi bi-people text-primary mt-1"></i>
                                <div>{{ number_format($event->registrations->count()) }} registered</div>
                            </li>
                            <li class="d-flex gap-2">
                                <i class="bi bi-person text-primary mt-1"></i>
                                <div>Organized by <span class="fw-medium">{{ $event->organizer->name }}</span></div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Related Events --}}
    @if($relatedEvents->count())
    <div class="mt-5">
        <h4 class="fw-bold mb-4">More in {{ $event->category->name }}</h4>
        <div class="row g-3">
            @foreach($relatedEvents as $related)
            <div class="col-md-3">@include('components.event-card', ['event' => $related])</div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection