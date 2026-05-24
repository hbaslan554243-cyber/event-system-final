@extends('layouts.admin')
@section('title', 'My Events')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('organizer.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">My Events</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">My Events</h4>
        <p class="text-muted mb-0">{{ $events->total() }} total events</p>
    </div>
    <a href="{{ route('organizer.events.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Create Event</a>
</div>

@if($events->isEmpty())
<div class="card border-0 shadow-sm text-center py-5">
    <div class="text-muted">
        <i class="bi bi-calendar3 d-block fs-1 mb-3"></i>
        <h5>No events yet</h5>
        <p class="mb-3">Create your first event to get started.</p>
        <a href="{{ route('organizer.events.create') }}" class="btn btn-primary">Create Your First Event</a>
    </div>
</div>
@else
<div class="row g-4">
    @foreach($events as $event)
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="row g-0">
                <div class="col-4">
                    <img src="{{ $event->banner_url }}" class="img-fluid rounded-start h-100" style="object-fit:cover;min-height:140px;" alt="{{ $event->title }}">
                </div>
                <div class="col-8">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge rounded-pill {{ match($event->status) {
                                'upcoming'=>'bg-info','ongoing'=>'bg-success','completed'=>'bg-secondary',
                                'cancelled'=>'bg-danger','draft'=>'bg-warning text-dark',default=>'bg-primary'
                            } }}">{{ ucfirst($event->status) }}</span>
                            @if($event->is_featured)<span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i></span>@endif
                        </div>
                        <h6 class="fw-semibold mb-1">{{ Str::limit($event->title, 45) }}</h6>
                        <div class="text-muted small mb-2">
                            <div><i class="bi bi-calendar3 me-1"></i>{{ $event->start_date->format('M d, Y') }}</div>
                            <div><i class="bi bi-people me-1"></i>{{ $event->registrations_count }} registered</div>
                        </div>
                        <div class="d-flex gap-2 mt-auto">
                            <a href="{{ route('organizer.events.show', $event) }}" class="btn btn-primary btn-sm flex-fill">Manage</a>
                            <a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                            @if($event->status === 'draft')
                            <form method="POST" action="{{ route('organizer.events.destroy', $event) }}" onsubmit="return confirm('Delete this event?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="mt-4">{{ $events->links() }}</div>
@endif
@endsection
