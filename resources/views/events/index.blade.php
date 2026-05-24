{{-- resources/views/events/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Browse Events')

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
</style>
@endpush

@section('content')

<div class="bg-primary text-white py-4">
    <div class="container">
        <h2 class="fw-bold mb-1">Browse Events</h2>
        <p class="opacity-75 mb-0">Discover {{ \App\Models\Event::published()->count() }} events happening near you and online</p>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">
        {{-- Sidebar Filters --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm sticky-top" style="top:80px">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Filter Events</h6>
                    <form method="GET" action="{{ route('events.index') }}">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Search</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Keywords..." value="{{ request('search') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Category</label>
                            <select name="category" class="form-select form-select-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }} ({{ $cat->events_count }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Date</label>
                            <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="free" id="freeOnly" value="1" {{ request('free') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="freeOnly">Free events only</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="online" id="onlineOnly" value="1" {{ request('online') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="onlineOnly">Online events only</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Apply Filters</button>
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-2">Clear</a>
                    </form>
                </div>
            </div>
        </div>

        {{-- Event Grid --}}
        <div class="col-lg-9">
            @if($featured->count() && !request()->hasAny(['search','category','status','date','free','online']))
            <div class="mb-4">
                <h5 class="fw-bold mb-3">🌟 Featured</h5>
                <div class="row g-3">
                    @foreach($featured as $event)
                    <div class="col-md-4">
                        @include('components.event-card', ['event' => $event, 'featured' => true])
                    </div>
                    @endforeach
                </div>
                <hr class="my-4">
            </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">All Events <span class="text-muted fw-normal small">({{ $events->total() }} found)</span></h5>
                <div class="d-flex gap-2 align-items-center">
                    <span class="small text-muted">Sort by:</span>
                    <select class="form-select form-select-sm" style="width:auto" onchange="window.location='?'+new URLSearchParams({...Object.fromEntries(new URLSearchParams(location.search)), sort: this.value})">
                        <option value="date">Date</option>
                        <option value="popular">Popular</option>
                    </select>
                </div>
            </div>

            @if($events->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">No events found</h5>
                <p class="text-muted">Try different filters or check back later.</p>
                <a href="{{ route('events.index') }}" class="btn btn-primary">View All Events</a>
            </div>
            @else
            <div class="row g-3">
                @foreach($events as $event)
                <div class="col-md-6 col-xl-4">
                    @include('components.event-card', ['event' => $event])
                </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $events->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection