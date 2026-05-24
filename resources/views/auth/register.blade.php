@extends('layouts.app')

@section('title', 'Register')

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

    /* Fix navbar colors on light background */
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
</style>
@endpush

@section('content')
<div class="min-vh-100 d-flex align-items-center py-5" style="background: linear-gradient(135deg,#f0f4ff,#faf5ff)">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="text-center mb-4">
                    <a href="{{ url('/') }}" class="text-decoration-none">
                        <h2 class="fw-bold" style="color:var(--ep-primary)">
                            <i class="bi bi-calendar-event-fill me-2"></i>EventPro
                        </h2>
                    </a>
                    <p class="text-muted">Create your free account to get started.</p>
                </div>
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-1">Create Account</h4>
                        <p class="text-muted small mb-4">Join thousands of event creators and attendees.</p>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            {{-- Role Selection --}}
                            <div class="mb-4">
                                <label class="form-label fw-medium">I want to...</label>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="role" id="role_attendee" value="attendee" checked>
                                        <label class="btn btn-outline-primary w-100 py-3 text-start" for="role_attendee">
                                            <i class="bi bi-ticket-perforated fs-4 d-block mb-1"></i>
                                            <span class="fw-semibold">Attend Events</span>
                                            <span class="d-block text-muted small">Buy tickets & join events</span>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="role" id="role_organizer" value="organizer">
                                        <label class="btn btn-outline-primary w-100 py-3 text-start" for="role_organizer">
                                            <i class="bi bi-megaphone fs-4 d-block mb-1"></i>
                                            <span class="fw-semibold">Host Events</span>
                                            <span class="d-block text-muted small">Create & manage events</span>
                                        </label>
                                    </div>
                                </div>
                                @error('role')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-medium">Full Name</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Juan Dela Cruz" value="{{ old('name') }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">Email Address</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="juan@example.com" value="{{ old('email') }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Password</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 8 characters" required>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">Phone Number <span class="text-muted fw-normal">(optional)</span></label>
                                    <input type="text" name="phone" class="form-control" placeholder="+63 9XX XXX XXXX" value="{{ old('phone') }}">
                                </div>
                            </div>

                            <div class="form-check my-4">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label small" for="terms">
                                    I agree to the <a href="#" style="color:var(--ep-primary)">Terms of Service</a> and <a href="#" style="color:var(--ep-primary)">Privacy Policy</a>
                                </label>
                            </div>

                            <button type="submit" class="btn w-100 py-2 fw-semibold text-white" style="background:var(--ep-primary)">
                                Create Account
                            </button>
                        </form>

                        <hr class="my-4">
                        <p class="text-center text-muted small mb-0">
                            Already have an account?
                            <a href="{{ route('login') }}" class="fw-semibold text-decoration-none" style="color:var(--ep-primary)">Sign in</a>
                        </p>
                    </div>
                </div>

                <p class="text-center text-muted small mt-3">
                    <a href="{{ url('/') }}" class="text-muted text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Back to Home
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection