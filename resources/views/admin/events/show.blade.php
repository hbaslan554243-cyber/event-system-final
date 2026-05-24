@extends('layouts.admin')
@section('title', $event->title)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Events</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($event->title, 30) }}</li>
@endsection

@section('content')

{{-- Pending Review Banner --}}
@if($event->status === 'pending_review')
<div class="alert alert-warning d-flex align-items-center gap-3 mb-4">
    <i class="bi bi-hourglass-split fs-4"></i>
    <div class="flex-grow-1">
        <strong>This event is awaiting your approval.</strong>
        <div class="small">Submitted by <strong>{{ $event->organizer->name }}</strong> — review the details below before approving.</div>
    </div>
    <form method="POST" action="{{ route('admin.events.approve', $event) }}">
        @csrf @method('PATCH')
        <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Approve & Publish</button>
    </form>
    <form method="POST" action="{{ route('admin.events.cancel', $event) }}" onsubmit="return confirm('Reject this event?')">
        @csrf @method('PATCH')
        <button class="btn btn-danger"><i class="bi bi-x-lg me-1"></i>Reject</button>
    </form>
</div>
@endif

{{-- Header --}}
<div class="card border-0 shadow-sm mb-4 overflow-hidden">
    <div class="row g-0">
        <div class="col-md-3">
            <img src="{{ $event->banner_url }}" class="img-fluid h-100" style="object-fit:cover;min-height:180px;" alt="">
        </div>
        <div class="col-md-9">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge px-3 py-2" style="background:{{ $event->category->color }}">{{ $event->category->name }}</span>
                        <span class="badge px-3 py-2 {{ match($event->status) {
                            'upcoming'       => 'bg-info',
                            'ongoing'        => 'bg-success',
                            'completed'      => 'bg-secondary',
                            'cancelled'      => 'bg-danger',
                            'draft'          => 'bg-warning text-dark',
                            'pending_review' => 'bg-primary',
                            default          => 'bg-primary'
                        } }}">{{ $event->status === 'pending_review' ? 'Pending Review' : ucfirst($event->status) }}</span>
                        @if($event->is_featured)<span class="badge bg-warning text-dark px-3 py-2">⭐ Featured</span>@endif
                    </div>
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('admin.events.feature', $event) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm {{ $event->is_featured ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="bi bi-star me-1"></i>{{ $event->is_featured ? 'Unfeature' : 'Feature' }}
                            </button>
                        </form>
                        @if(in_array($event->status, ['draft','pending_review']))
                        <form method="POST" action="{{ route('admin.events.approve', $event) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-success btn-sm"><i class="bi bi-check-lg me-1"></i>Approve</button>
                        </form>
                        @endif
                        @if(!in_array($event->status, ['cancelled','completed']))
                        <form method="POST" action="{{ route('admin.events.cancel', $event) }}"
                              onsubmit="return confirm('Cancel this event?')">
                            @csrf @method('PATCH')
                            <button class="btn btn-danger btn-sm"><i class="bi bi-x-lg me-1"></i>Cancel</button>
                        </form>
                        @endif
                    </div>
                </div>
                <h3 class="fw-bold mb-2">{{ $event->title }}</h3>
                <div class="d-flex flex-wrap gap-3 text-muted small">
                    <span><i class="bi bi-person me-1"></i>{{ $event->organizer->name }}</span>
                    <span><i class="bi bi-calendar3 me-1"></i>{{ $event->start_date->format('M d, Y • g:i A') }}</span>
                    <span><i class="bi bi-geo-alt me-1"></i>{{ $event->is_online ? 'Online' : ($event->venue?->name ?? 'TBA') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-primary">{{ $stats['total_registrations'] }}</div>
            <div class="small text-muted">Registrations</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-success">₱{{ number_format($stats['revenue'], 0) }}</div>
            <div class="small text-muted">Revenue</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-info">{{ $stats['checked_in'] }}</div>
            <div class="small text-muted">Checked In</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-warning">{{ number_format($stats['avg_rating'] ?? 0, 1) }}</div>
            <div class="small text-muted">Avg Rating</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Ticket Types --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0">🎟 Ticket Types</h6>
            </div>
            <div class="card-body p-0">
                @forelse($event->ticketTypes as $tt)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $tt->name }}
                            <span class="badge ms-1 {{ match($tt->type){'vip'=>'bg-warning text-dark','free'=>'bg-success',default=>'bg-primary'} }}">
                                {{ strtoupper($tt->type) }}
                            </span>
                        </div>
                        <div class="text-muted small">₱{{ number_format($tt->price, 2) }} · {{ $tt->quantity_sold }}/{{ $tt->quantity_available }} sold</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-success small">₱{{ number_format($tt->price * $tt->quantity_sold, 0) }}</div>
                        <div class="text-muted" style="font-size:.7rem">revenue</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted small">No ticket types.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Registrations --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0">👥 Recent Registrations</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 small text-muted fw-semibold">Attendee</th>
                                <th class="small text-muted fw-semibold">Ticket</th>
                                <th class="small text-muted fw-semibold">Payment</th>
                                <th class="small text-muted fw-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($event->registrations->take(10) as $reg)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $reg->user->avatar_url }}" class="rounded-circle" width="30" height="30">
                                        <div>
                                            <div class="fw-medium small">{{ $reg->user->name }}</div>
                                            <div class="text-muted" style="font-size:.7rem">{{ $reg->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">{{ $reg->ticketType->name }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ in_array($reg->payment_status, ['paid','free']) ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($reg->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill {{ $reg->status === 'attended' ? 'bg-success' : 'bg-info' }}">
                                        {{ ucfirst($reg->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">No registrations yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Reviews --}}
    @if($event->feedbacks->count())
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0">⭐ Reviews</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($event->feedbacks->take(6) as $fb)
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3">
                            <div class="d-flex gap-2 align-items-center mb-2">
                                <img src="{{ $fb->user->avatar_url }}" class="rounded-circle" width="30" height="30">
                                <span class="fw-medium small">{{ $fb->user->name }}</span>
                                <span class="text-warning ms-auto small">
                                    @for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$fb->overall_rating?'-fill':'' }}"></i>@endfor
                                </span>
                            </div>
                            @if($fb->comment)<p class="text-muted small mb-0">{{ Str::limit($fb->comment, 100) }}</p>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Media --}}
    @if($event->media->count())
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0">📸 Media ({{ $event->media->count() }} files)</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($event->media->where('file_type','image')->take(8) as $m)
                    <div class="col-3 col-md-2">
                        <img src="{{ $m->url }}" class="img-fluid rounded-2"
                             style="height:80px;width:100%;object-fit:cover;" alt="">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection