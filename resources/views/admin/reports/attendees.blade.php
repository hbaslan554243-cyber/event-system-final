@extends('layouts.admin')
@section('title', 'Attendee Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Attendees</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Attendee Report</h4>
    <a href="{{ route('admin.reports.export', 'registrations') }}" class="btn btn-success btn-sm">
        <i class="bi bi-download me-2"></i>Export CSV
    </a>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-primary">{{ number_format($data['total']) }}</div>
            <div class="small text-muted">Total Registrations</div>
        </div>
    </div>
    @foreach($data['by_status'] as $status => $count)
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold">{{ number_format($count) }}</div>
            <div class="small text-muted">{{ ucfirst($status) }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    {{-- Monthly Chart --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Monthly Registrations ({{ now()->year }})</h6>
                <canvas id="monthlyChart" height="120"></canvas>
            </div>
        </div>
    </div>

    {{-- By Status --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">By Status</h6>
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- Top Events --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0">Top Events by Attendance</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 small text-muted fw-semibold">#</th>
                                <th class="small text-muted fw-semibold">Event</th>
                                <th class="small text-muted fw-semibold">Date</th>
                                <th class="small text-muted fw-semibold">Registrations</th>
                                <th class="small text-muted fw-semibold">Fill Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['top_events'] as $i => $event)
                            <tr>
                                <td class="ps-4 fw-bold text-muted">{{ $i + 1 }}</td>
                                <td class="fw-medium">{{ Str::limit($event->title, 50) }}</td>
                                <td class="small text-muted">{{ $event->start_date->format('M d, Y') }}</td>
                                <td><span class="badge bg-primary">{{ $event->registrations_count }}</span></td>
                                <td style="width:200px">
                                    @php $fill = $event->max_attendees ? min(100, round($event->registrations_count / $event->max_attendees * 100)) : 0; @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:6px">
                                            <div class="progress-bar bg-primary" style="width:{{ $fill }}%"></div>
                                        </div>
                                        <span class="small text-muted">{{ $fill }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const monthly = @json($data['by_month']);
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{ label: 'Registrations', data: months.map((_,i) => monthly[i+1] || 0), backgroundColor: 'rgba(79,70,229,.8)', borderRadius: 6 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

const statusData = @json($data['by_status']);
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
        datasets: [{ data: Object.values(statusData), backgroundColor: ['#22c55e','#3b82f6','#f59e0b','#ef4444','#64748b'], borderWidth: 2, borderColor: '#fff' }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
});
</script>
@endpush
