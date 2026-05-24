@extends('layouts.admin')
@section('title', 'User Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4">
            <img src="{{ $user->avatar_url }}" class="rounded-circle mx-auto mb-3" width="90" height="90">
            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
            <span class="badge {{ match($user->role) {'admin'=>'bg-danger','organizer'=>'bg-primary',default=>'bg-secondary'} }} mb-2">
                {{ ucfirst($user->role) }}
            </span>
            <p class="text-muted small">{{ $user->email }}</p>
            @if($user->phone)<p class="text-muted small">{{ $user->phone }}</p>@endif
            <span class="badge rounded-pill {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                {{ $user->is_active ? 'Active' : 'Inactive' }}
            </span>
            <div class="mt-3 d-flex gap-2 justify-content-center">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row g-3 mb-4">
            <div class="col-sm-4">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div class="fs-3 fw-bold text-primary">{{ $stats['total_registrations'] }}</div>
                    <div class="small text-muted">Registrations</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div class="fs-3 fw-bold text-success">₱{{ number_format($stats['total_spent'], 0) }}</div>
                    <div class="small text-muted">Total Spent</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div class="fs-3 fw-bold text-info">{{ $stats['events_organized'] }}</div>
                    <div class="small text-muted">Events Organized</div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0">Recent Registrations</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 small">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Event</th>
                                <th>Ticket</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->registrations->take(10) as $reg)
                            <tr>
                                <td class="ps-4 fw-medium">{{ Str::limit($reg->event->title, 35) }}</td>
                                <td>{{ $reg->ticketType->name }}</td>
                                <td>{{ $reg->final_amount == 0 ? 'Free' : '₱'.number_format($reg->final_amount, 2) }}</td>
                                <td><span class="badge bg-{{ $reg->status === 'confirmed' ? 'success' : 'secondary' }}">{{ ucfirst($reg->status) }}</span></td>
                                <td class="text-muted">{{ $reg->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-3 text-muted">No registrations.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
