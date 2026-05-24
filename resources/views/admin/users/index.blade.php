@extends('layouts.admin')
@section('title', 'Users')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">User Management</h4>
        <p class="text-muted mb-0">{{ $users->total() }} total users</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-2"></i>Add User
    </a>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control"
                       placeholder="Search name or email..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="admin"     {{ request('role') == 'admin'     ? 'selected' : '' }}>Admin</option>
                    <option value="organizer" {{ request('role') == 'organizer' ? 'selected' : '' }}>Organizer</option>
                    <option value="attendee"  {{ request('role') == 'attendee'  ? 'selected' : '' }}>Attendee</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 small text-muted fw-semibold" style="width:35%">User</th>
                        <th class="small text-muted fw-semibold">Role</th>
                        <th class="small text-muted fw-semibold">Phone</th>
                        <th class="small text-muted fw-semibold">Events / Regs</th>
                        <th class="small text-muted fw-semibold">Status</th>
                        <th class="small text-muted fw-semibold">Joined</th>
                        <th class="small text-muted fw-semibold pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $user->avatar_url }}"
                                     class="rounded-circle flex-shrink-0"
                                     width="38" height="38"
                                     alt="{{ $user->name }}">
                                <div>
                                    <div class="fw-medium">{{ $user->name }}</div>
                                    <div class="text-muted small">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge rounded-pill
                                {{ match($user->role) {
                                    'admin'     => 'bg-danger',
                                    'organizer' => 'bg-primary',
                                    default     => 'bg-secondary'
                                } }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $user->phone ?? '—' }}</td>
                        <td class="small">
                            @if($user->role === 'organizer')
                                <span class="text-primary">
                                    {{ $user->organized_events_count ?? \App\Models\Event::where('organizer_id',$user->id)->count() }} events
                                </span>
                            @else
                                <span class="text-muted">
                                    {{ $user->registrations_count ?? \App\Models\Registration::where('user_id',$user->id)->count() }} regs
                                </span>
                            @endif
                        </td>
                        <td>
                            <form method="POST"
                                  action="{{ route('admin.users.toggle-status', $user) }}"
                                  class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="badge rounded-pill border-0 {{ $user->is_active ? 'bg-success' : 'bg-danger' }}"
                                        style="cursor:pointer"
                                        title="Click to toggle">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="small text-muted">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="pe-4">
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form method="POST"
                                      action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Delete this user?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people d-block fs-2 mb-2"></i>
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center py-3 px-4">
        <div class="text-muted small">
            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}
        </div>
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection