@extends('layouts.app')

@section('title', 'Login')


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
<div class="min-vh-100 d-flex align-items-center py-5" style="background: linear-gradient(135deg,#f0f4ff,#faf5ff);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4">
                    <a href="{{ url('/') }}" class="text-decoration-none">
                        <h2 class="fw-bold" style="color:var(--ep-primary)">
                            <i class="bi bi-calendar-event-fill me-2"></i>EventPro
                        </h2>
                    </a>
                    <p class="text-muted">Welcome back! Sign in to continue.</p>
                </div>
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Sign In</h4>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label small" for="remember">Remember me</label>
                                </div>
                                <a href="#" class="small text-decoration-none" style="color:var(--ep-primary)">Forgot password?</a>
                            </div>
                            <button type="submit" class="btn w-100 py-2 fw-semibold text-white" style="background:var(--ep-primary)">Sign In</button>
                        </form>
                        <hr class="my-4">
                        <p class="text-center text-muted small mb-0">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="fw-semibold text-decoration-none" style="color:var(--ep-primary)">Create one free</a>
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