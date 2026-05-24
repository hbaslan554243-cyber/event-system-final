{{-- resources/views/components/event-card.blade.php --}}
<div class="card h-100 border-0 shadow-sm event-card" style="border-radius:12px;overflow:hidden;transition:transform .2s,box-shadow .2s;">
    <div class="position-relative">
        <img src="{{ $event->banner_url }}" class="card-img-top" alt="{{ $event->title }}" style="height:180px;object-fit:cover;">
        <div class="position-absolute top-0 start-0 m-2 d-flex gap-1">
            <span class="badge" style="background:{{ $event->category->color }}">{{ $event->category->name }}</span>
        </div>
        @if(isset($featured) && $featured)
            <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark"><i class="bi bi-star-fill"></i></span>
        @endif
        @if($event->is_online)
            <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark"><i class="bi bi-camera-video me-1"></i>Online</span>
        @endif
    </div>
    <div class="card-body d-flex flex-column p-3">
        <h6 class="card-title fw-semibold mb-2 lh-sm" style="font-size:.9rem">{{ Str::limit($event->title, 55) }}</h6>
        <div class="text-muted mb-1" style="font-size:.78rem"><i class="bi bi-calendar3 me-1"></i>{{ $event->start_date->format('M d, Y • g:i A') }}</div>
        <div class="text-muted mb-2" style="font-size:.78rem"><i class="bi bi-geo-alt me-1"></i>{{ $event->is_online ? 'Online' : ($event->venue?->city ?? 'TBA') }}</div>
        @if($event->avg_rating > 0)
        <div class="text-muted mb-2" style="font-size:.78rem">
            <span class="text-warning">{{ str_repeat('★', round($event->avg_rating)) }}</span>
            {{ number_format($event->avg_rating, 1) }} ({{ $event->total_reviews }})
        </div>
        @endif
        <div class="mt-auto d-flex justify-content-between align-items-center">
            @if($event->ticketTypes->min('price') == 0)
                <span class="fw-bold text-success small">FREE</span>
            @else
                <span class="fw-bold text-primary small">From ₱{{ number_format($event->ticketTypes->min('price'), 0) }}</span>
            @endif
            <a href="{{ route('events.show', $event->slug) }}" class="btn btn-primary btn-sm px-3">Register</a>
        </div>
    </div>
</div>
