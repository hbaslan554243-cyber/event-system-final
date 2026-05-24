@extends('layouts.admin')
@section('title', 'Reports')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<h4 class="fw-bold mb-4">Reports & Analytics</h4>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="p-3 rounded-3 bg-success bg-opacity-10 d-inline-block mb-3">
                    <i class="bi bi-currency-dollar fs-2 text-success"></i>
                </div>
                <h5 class="fw-bold">Revenue Report</h5>
                <p class="text-muted small">Monthly revenue breakdown, payment gateway analysis, and top-earning events.</p>
                <a href="{{ route('admin.reports.revenue') }}" class="btn btn-success w-100">View Report</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="p-3 rounded-3 bg-primary bg-opacity-10 d-inline-block mb-3">
                    <i class="bi bi-people fs-2 text-primary"></i>
                </div>
                <h5 class="fw-bold">Attendee Report</h5>
                <p class="text-muted small">Registration statistics, top events by attendance, and monthly trends.</p>
                <a href="{{ route('admin.reports.attendees') }}" class="btn btn-primary w-100">View Report</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="p-3 rounded-3 bg-warning bg-opacity-10 d-inline-block mb-3">
                    <i class="bi bi-star fs-2 text-warning"></i>
                </div>
                <h5 class="fw-bold">Feedback Report</h5>
                <p class="text-muted small">Average ratings, review breakdown by stars, and top-rated events.</p>
                <a href="{{ route('admin.reports.feedback') }}" class="btn btn-warning w-100">View Report</a>
            </div>
        </div>
    </div>

    {{-- Quick Export --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-download me-2"></i>Quick Export</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('admin.reports.export', 'registrations') }}"
                           class="btn btn-outline-primary w-100">
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export Registrations CSV
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('admin.reports.export', 'payments') }}"
                           class="btn btn-outline-success w-100">
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export Payments CSV
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('admin.reports.export', 'events') }}"
                           class="btn btn-outline-secondary w-100">
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export Events CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
