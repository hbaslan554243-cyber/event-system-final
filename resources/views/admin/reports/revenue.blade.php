{{-- resources/views/admin/reports/revenue.blade.php --}}
@extends('layouts.admin')
@section('title', 'Revenue Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Revenue</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Revenue Report</h4>
    <div class="d-flex gap-2">
        <form method="GET" class="d-flex gap-2">
            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
        <a href="{{ route('admin.reports.export', 'payments') }}" class="btn btn-success btn-sm">
            <i class="bi bi-download me-2"></i>Export CSV
        </a>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-success">₱{{ number_format($data['total_revenue'], 0) }}</div>
            <div class="small text-muted">Total Revenue {{ $year }}</div>
        </div>
    </div>
    @foreach($data['by_gateway'] as $gw)
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-4 fw-bold text-primary">₱{{ number_format($gw->total, 0) }}</div>
            <div class="small text-muted">{{ ucfirst($gw->gateway) }} ({{ $gw->count }} txns)</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    {{-- Monthly Chart --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Monthly Revenue Breakdown</h6>
                <canvas id="monthlyRevChart" height="120"></canvas>
            </div>
        </div>
    </div>

    {{-- Gateway Pie --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Revenue by Gateway</h6>
                <canvas id="gatewayChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const monthly = @json($data['monthly']);
new Chart(document.getElementById('monthlyRevChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Revenue ₱',
            data: months.map((_, i) => monthly[i+1] || 0),
            borderColor: '#4f46e5', backgroundColor: 'rgba(79,70,229,.1)',
            tension: .4, fill: true, pointRadius: 5,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

const gwData = @json($data['by_gateway']);
new Chart(document.getElementById('gatewayChart'), {
    type: 'doughnut',
    data: {
        labels: gwData.map(g => g.gateway.charAt(0).toUpperCase() + g.gateway.slice(1)),
        datasets: [{ data: gwData.map(g => g.total), backgroundColor: ['#4f46e5','#22c55e','#f59e0b','#ef4444','#14b8a6'], borderWidth: 2, borderColor: '#fff' }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
});
</script>
@endpush
