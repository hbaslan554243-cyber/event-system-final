@extends('layouts.app')

@section('title', 'Home')

@section('hero')
@endsection

@section('content')

{{-- Search Bar --}}
<section class="bg-white py-4 shadow-sm">
    <div class="container">
        <form action="{{ route('events.index') }}" method="GET">
            <div class="row g-2 align-items-center">
                <div class="col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search events, organizers..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-3">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach(\App\Models\Category::all() as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary w-100">Find Events</button>
                </div>
            </div>
        </form>
    </div>
</section>

{{-- Featured Events --}}
@if(isset($featured) && $featured->count())
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Featured Events</h2>
                <p class="text-muted mb-0">Handpicked events you won't want to miss</p>
            </div>
            <a href="{{ route('events.index') }}" class="btn btn-outline-primary">View All</a>
        </div>
        <div class="row g-4">
            @foreach($featured as $event)
            <div class="col-lg-4">
                <div class="card h-100 shadow-sm border-0 event-card">
                    <div class="position-relative">
                        <img src="{{ $event->banner_url }}" class="card-img-top" alt="{{ $event->title }}" style="height:200px;object-fit:cover;">
                        <span class="position-absolute top-0 end-0 m-2 badge" style="background:{{ $event->category->color }}">{{ $event->category->name }}</span>
                        @if($event->is_featured)
                            <span class="position-absolute top-0 start-0 m-2 badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Featured</span>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-semibold">{{ Str::limit($event->title, 60) }}</h5>
                        <p class="card-text text-muted small flex-grow-1">{{ Str::limit($event->short_description ?? $event->description, 100) }}</p>
                        <div class="mt-auto">
                            <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                                <i class="bi bi-calendar3"></i>
                                {{ $event->start_date->format('M d, Y • g:i A') }}
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted small mb-3">
                                <i class="bi bi-geo-alt"></i>
                                {{ $event->is_online ? 'Online Event' : ($event->venue?->city ?? 'TBA') }}
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($event->ticketTypes->min('price') == 0)
                                        <span class="fw-bold text-success">FREE</span>
                                    @else
                                        <span class="fw-bold text-primary">From ₱{{ number_format($event->ticketTypes->min('price'), 2) }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('events.show', $event->slug) }}" class="btn btn-primary btn-sm px-3">View Event</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Categories --}}
<section class="py-5">
    <div class="container">
        <h2 class="fw-bold text-center mb-2">Browse by Category</h2>
        <p class="text-muted text-center mb-4">Find the events that match your interests</p>
        <div class="row g-3 justify-content-center">
            @foreach(\App\Models\Category::withCount('events')->get() as $cat)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('events.index', ['category' => $cat->id]) }}" class="text-decoration-none">
                    <div class="card text-center border-0 shadow-sm p-3 h-100 category-card" style="border-top: 3px solid {{ $cat->color }} !important; transition: transform .2s">
                        <div class="fs-2 mb-2">{{ $cat->icon ?? '📅' }}</div>
                        <div class="fw-semibold small">{{ $cat->name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $cat->events_count }} events</div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-5" style="background: linear-gradient(135deg, #1e293b, #334155);">
    <div class="container text-center text-white py-3">
        <h2 class="fw-bold mb-3">Ready to host your event?</h2>
        <p class="lead opacity-75 mb-4">Join thousands of organizers who trust EventPro to manage their events from ticketing to attendance.</p>
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5 me-3">Get Started Free</a>
        <a href="{{ route('events.index') }}" class="btn btn-outline-light btn-lg px-5">Browse Events</a>
    </div>
</section>

@endsection