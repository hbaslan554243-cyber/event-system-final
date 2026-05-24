{{-- resources/views/admin/events/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Manage Events')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Events</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Event Management</h4>
        <p class="text-muted mb-0">Review, approve, and manage all platform events</p>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search events..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    @foreach(['draft','pending_review','upcoming','ongoing','completed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                            {{ $s === 'pending_review' ? 'Pending Review' : ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary ms-1">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- Pending Review Alert --}}
@php $pendingCount = \App\Models\Event::where('status','pending_review')->count(); @endphp
@if($pendingCount)
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-hourglass-split fs-5"></i>
    <span>You have <strong>{{ $pendingCount }} event(s)</strong> waiting for your approval.</span>
    <a href="{{ route('admin.events.index', ['status' => 'pending_review']) }}" class="btn btn-warning btn-sm ms-auto">
        Review Now
    </a>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 small text-muted fw-semibold">Event</th>
                        <th class="small text-muted fw-semibold">Organizer</th>
                        <th class="small text-muted fw-semibold">Date</th>
                        <th class="small text-muted fw-semibold">Registrations</th>
                        <th class="small text-muted fw-semibold">Status</th>
                        <th class="small text-muted fw-semibold pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr {{ $event->status === 'pending_review' ? 'class=table-warning' : '' }}>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $event->banner_url }}" class="rounded-2" width="50" height="38" style="object-fit:cover;" alt="">
                                <div>
                                    <div class="fw-medium">{{ Str::limit($event->title, 45) }}</div>
                                    <div class="text-muted small">
                                        <span class="badge rounded-pill" style="background:{{ $event->category->color }}">{{ $event->category->name }}</span>
                                        @if($event->is_featured)<span class="badge bg-warning text-dark ms-1">⭐ Featured</span>@endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="small">{{ $event->organizer->name }}</td>
                        <td class="small text-muted">{{ $event->start_date->format('M d, Y') }}</td>
                        <td class="small"><span class="badge bg-light text-dark">{{ $event->registrations_count }}</span></td>
                        <td>
                            <span class="badge rounded-pill {{ match($event->status) {
                                'upcoming'       => 'bg-info',
                                'ongoing'        => 'bg-success',
                                'completed'      => 'bg-secondary',
                                'cancelled'      => 'bg-danger',
                                'draft'          => 'bg-warning text-dark',
                                'pending_review' => 'bg-orange text-dark',
                                default          => 'bg-primary'
                            } }}" style="{{ $event->status === 'pending_review' ? 'background:#f97316!important;color:#fff!important;' : '' }}">
                                {{ $event->status === 'pending_review' ? 'Pending Review' : ucfirst($event->status) }}
                            </span>
                        </td>
                        <td class="pe-4">
                            <div class="d-flex gap-1 flex-wrap">
                                <a href="{{ route('admin.events.show', $event) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(in_array($event->status, ['draft', 'pending_review']))
                                <form method="POST" action="{{ route('admin.events.approve', $event) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-success" title="Approve">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('admin.events.feature', $event) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm {{ $event->is_featured ? 'btn-warning' : 'btn-outline-warning' }}" title="Feature/Unfeature">
                                        <i class="bi bi-star"></i>
                                    </button>
                                </form>
                                @if(!in_array($event->status, ['cancelled','completed']))
                                <form method="POST" action="{{ route('admin.events.cancel', $event) }}" onsubmit="return confirm('Cancel this event?')">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-danger" title="Cancel">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-calendar3 d-block fs-2 mb-2"></i>No events found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($events->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center py-3">
        <div class="text-muted small">Showing {{ $events->firstItem() }}–{{ $events->lastItem() }} of {{ $events->total() }}</div>
        {{ $events->links() }}
    </div>
    @endif
</div>
@endsection