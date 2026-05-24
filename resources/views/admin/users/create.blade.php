@extends('layouts.admin')
@section('title', isset($user) ? 'Edit User' : 'Create User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">{{ isset($user) ? 'Edit' : 'Create' }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">{{ isset($user) ? 'Edit User' : 'Create New User' }}</h4>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
                    @csrf
                    @isset($user) @method('PUT') @endisset

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name ?? '') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email ?? '') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">
                                Password {{ isset($user) ? '(leave blank to keep)' : '' }}
                                @if(!isset($user))<span class="text-danger">*</span>@endif
                            </label>
                            <input type="password" name="password" class="form-control"
                                   {{ !isset($user) ? 'required' : '' }} minlength="8"
                                   placeholder="{{ isset($user) ? 'Leave blank to keep current' : 'Min. 8 characters' }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="attendee"  {{ old('role', $user->role ?? '') == 'attendee'  ? 'selected' : '' }}>Attendee</option>
                                <option value="organizer" {{ old('role', $user->role ?? '') == 'organizer' ? 'selected' : '' }}>Organizer</option>
                                <option value="admin"     {{ old('role', $user->role ?? '') == 'admin'     ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', $user->phone ?? '') }}">
                        </div>

                        @isset($user)
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                       value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">Active Account</label>
                            </div>
                        </div>
                        @endisset
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-2"></i>{{ isset($user) ? 'Save Changes' : 'Create User' }}
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
