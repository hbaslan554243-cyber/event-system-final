@extends('layouts.admin')
@section('title', 'Venues')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Venues</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Venue Management</h4>
        <p class="text-muted mb-0">{{ $venues->total() }} total venues</p>
    </div>
    <a href="{{ route('admin.venues.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Add Venue
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 small text-muted fw-semibold">Venue</th>
                        <th class="small text-muted fw-semibold">Location</th>
                        <th class="small text-muted fw-semibold">Capacity</th>
                        <th class="small text-muted fw-semibold">Events</th>
                        <th class="small text-muted fw-semibold">Status</th>
                        <th class="small text-muted fw-semibold pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($venues as $venue)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-medium">{{ $venue->name }}</div>
                            @if($venue->contact_person)
                                <div class="text-muted small">{{ $venue->contact_person }}</div>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $venue->city }}, {{ $venue->country }}</td>
                        <td class="small">{{ number_format($venue->capacity) }} pax</td>
                        <td><span class="badge bg-light text-dark">{{ $venue->events_count }} events</span></td>
                        <td>
                            <span class="badge rounded-pill {{ $venue->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $venue->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="pe-4">
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.venues.edit', $venue) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.venues.destroy', $venue) }}"
                                      onsubmit="return confirm('Delete this venue?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-geo-alt d-block fs-2 mb-2"></i>No venues found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($venues->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $venues->links() }}
    </div>
    @endif
</div>
@endsection
