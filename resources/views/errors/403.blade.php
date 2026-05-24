{{-- resources/views/errors/403.blade.php --}}
@extends('layouts.app')
@section('title', 'Access Denied')
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="text-center">
        <div style="font-size:6rem">🚫</div>
        <h1 class="display-4 fw-bold text-danger">403</h1>
        <h3 class="fw-semibold mb-3">Access Denied</h3>
        <p class="text-muted mb-4">{{ $message ?? 'You do not have permission to access this page.' }}</p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Go Back</a>
            <a href="{{ url('/') }}" class="btn btn-primary"><i class="bi bi-house me-2"></i>Home</a>
        </div>
    </div>
</div>
@endsection
