{{-- resources/views/auth/profile.blade.php --}}
@extends('layouts.app')
@section('title', 'My Profile')

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
    .ep-avatar-btn {
        background: rgba(0,0,0,.06) !important;
        border-color: rgba(0,0,0,.15) !important;
        color: var(--ep-text) !important;
    }
    .ep-avatar-btn:hover {
        background: rgba(0,0,0,.1) !important;
        color: var(--ep-text) !important;
    }
    .ep-avatar-name { color: var(--ep-text) !important; }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row g-4">
        {{-- Sidebar --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm text-center p-4">
                <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle mx-auto mb-3" width="90" height="90" alt="Avatar">
                <h5 class="fw-bold mb-1">{{ auth()->user()->name }}</h5>
                <span class="badge {{ match(auth()->user()->role) {'admin'=>'bg-danger','organizer'=>'bg-primary',default=>'bg-secondary'} }} mb-2">{{ ucfirst(auth()->user()->role) }}</span>
                <p class="text-muted small">{{ auth()->user()->email }}</p>
                <div class="d-grid gap-2 mt-2">
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm">Admin Panel</a>
                    @elseif(auth()->user()->isOrganizer())
                        <a href="{{ route('organizer.dashboard') }}" class="btn btn-outline-primary btn-sm">Organizer Panel</a>
                    @else
                        <a href="{{ route('attendee.dashboard') }}" class="btn btn-outline-primary btn-sm">My Events</a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Profile Form --}}
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3"><h5 class="fw-bold mb-0">Edit Profile</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Full Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Bio</label>
                                <textarea name="bio" class="form-control" rows="3">{{ old('bio', auth()->user()->bio) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', auth()->user()->address) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Profile Photo</label>
                                <input type="file" name="avatar" class="form-control" accept="image/*" id="avatarInput">
                                <img id="avatarPreview" class="rounded-circle mt-2 d-none" width="60" height="60" style="object-fit:cover">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4"><i class="bi bi-check-lg me-2"></i>Save Changes</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3"><h5 class="fw-bold mb-0">Change Password</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-medium">Current Password</label>
                                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">New Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning mt-4"><i class="bi bi-shield-lock me-2"></i>Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('avatarInput').addEventListener('change', function() {
    const reader = new FileReader();
    const preview = document.getElementById('avatarPreview');
    reader.onload = e => { preview.src = e.target.result; preview.classList.remove('d-none'); };
    if (this.files[0]) reader.readAsDataURL(this.files[0]);
});
</script>
@endpush