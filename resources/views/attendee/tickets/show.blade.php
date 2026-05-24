{{-- resources/views/attendee/tickets/show.blade.php --}}
@extends('layouts.app')
@section('title', 'My Ticket')
@section('hide_hero', '1')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            @if(session('success'))
            <div class="alert alert-success mb-4"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
            @endif

            @foreach($registration->tickets as $ticket)
            <div class="card border-0 shadow-lg mb-4 overflow-hidden" style="border-radius:16px">

                {{-- Header --}}
                <div class="p-4 text-white" style="background: linear-gradient(135deg, {{ $registration->event->category->color ?? '#4f46e5' }}, #7c3aed)">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="opacity-75 small mb-1">{{ $registration->event->category->name }}</div>
                            <h4 class="fw-bold mb-1">{{ $registration->event->title }}</h4>
                            <div class="opacity-90 small">{{ $registration->ticketType->name }}</div>
                        </div>
                        <span class="badge bg-white text-primary fs-6">
                            {{ $ticket->status === 'valid' ? '✓ Valid' : ucfirst($ticket->status) }}
                        </span>
                    </div>
                </div>

                {{-- Dashed separator --}}
                <div class="position-relative" style="height:2px;background:repeating-linear-gradient(90deg,#e2e8f0 0,#e2e8f0 10px,transparent 10px,transparent 20px)">
                    <div class="position-absolute bg-light rounded-circle" style="width:20px;height:20px;left:-10px;top:-9px"></div>
                    <div class="position-absolute bg-light rounded-circle" style="width:20px;height:20px;right:-10px;top:-9px"></div>
                </div>

                {{-- Body --}}
                <div class="p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="text-muted small">Date</div>
                            <div class="fw-semibold">{{ $registration->event->start_date->format('M d, Y') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Time</div>
                            <div class="fw-semibold">{{ $registration->event->start_date->format('g:i A') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Venue</div>
                            <div class="fw-semibold">{{ $registration->event->is_online ? 'Online Event' : ($registration->event->venue?->name ?? 'TBA') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Ticket #</div>
                            <div class="fw-semibold font-monospace small">{{ $ticket->ticket_number }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Attendee</div>
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Registration #</div>
                            <div class="fw-semibold font-monospace small">{{ $registration->registration_number }}</div>
                        </div>
                    </div>

                    {{-- QR Code --}}
                    <div class="text-center py-3 border-top border-bottom mb-4">
                        @if($ticket->qr_code_path && file_exists(storage_path('app/public/' . $ticket->qr_code_path)))
                            <img src="{{ asset('storage/' . $ticket->qr_code_path) }}" alt="QR Code" class="img-fluid" style="max-width:180px">
                        @else
                            <div class="bg-light d-inline-flex align-items-center justify-content-center rounded" style="width:180px;height:180px">
                                <i class="bi bi-qr-code fs-1 text-muted"></i>
                            </div>
                        @endif
                        <div class="text-muted small mt-2 font-monospace">{{ $ticket->qr_code_data ?? $ticket->ticket_number }}</div>
                    </div>

                    {{-- Actions --}}
                    @php
                        $hasReviewed = \App\Models\Feedback::where('event_id', $registration->event_id)
                            ->where('user_id', auth()->id())->exists();
                    @endphp
                    <div class="d-flex gap-2">
                        <a href="{{ route('attendee.tickets.download', $ticket) }}" class="btn btn-primary flex-fill">
                            <i class="bi bi-download me-2"></i>Download PDF
                        </a>
                        @if(!$hasReviewed)
                            <a href="{{ route('attendee.feedback.create', $registration->event) }}"
                               class="btn btn-outline-warning">
                                <i class="bi bi-star me-2"></i>Review
                            </a>
                        @else
                            <span class="btn btn-warning disabled">
                                <i class="bi bi-star-fill me-2"></i>Reviewed
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            <a href="{{ route('attendee.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection