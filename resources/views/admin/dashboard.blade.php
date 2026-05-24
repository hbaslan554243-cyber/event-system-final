@extends('layouts.admin')
@section('title', 'Admin Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Admin Dashboard</h4>
        <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }} — {{ now()->format('l, F j, Y') }}</p>
    </div>
    <a href="{{ route('admin.events.index') }}" class="btn btn-primary"><i class="bi bi-calendar3 me-2"></i>Manage Events</a>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card border-0 shadow-sm p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small mb-1">Total Users</div>
                    <div class="fs-3 fw-bold">{{ number_format($stats['total_users']) }}</div>
                    <div class="small text-success mt-1"><i class="bi bi-arrow-up-short"></i>+12% this month</div>
                </div>
                <div class="p-3 rounded-3" style="background:#ede9fe"><i class="bi bi-people fs-4 text-purple" style="color:#7c3aed"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card border-0 shadow-sm p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small mb-1">Total Events</div>
                    <div class="fs-3 fw-bold">{{ number_format($stats['total_events']) }}</div>
                    <div class="small text-info mt-1"><i class="bi bi-calendar-check"></i> {{ $stats['active_events'] }} active</div>
                </div>
                <div class="p-3 rounded-3 bg-primary bg-opacity-10"><i class="bi bi-calendar3 fs-4 text-primary"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card border-0 shadow-sm p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small mb-1">Registrations</div>
                    <div class="fs-3 fw-bold">{{ number_format($stats['total_registrations']) }}</div>
                    <div class="small text-warning mt-1"><i class="bi bi-ticket"></i> all time</div>
                </div>
                <div class="p-3 rounded-3" style="background:#fef3c7"><i class="bi bi-ticket-perforated fs-4" style="color:#d97706"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card border-0 shadow-sm p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small mb-1">Total Revenue</div>
                    <div class="fs-3 fw-bold">₱{{ number_format($stats['total_revenue'], 0) }}</div>
                    <div class="small text-success mt-1"><i class="bi bi-currency-dollar"></i> completed payments</div>
                </div>
                <div class="p-3 rounded-3" style="background:#dcfce7"><i class="bi bi-cash-stack fs-4" style="color:#16a34a"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Revenue Chart --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Monthly Revenue ({{ now()->year }})</h6>
                    <a href="{{ route('admin.reports.revenue') }}" class="btn btn-sm btn-outline-primary">Full Report</a>
                </div>
                <canvas id="revenueChart" height="120"></canvas>
            </div>
        </div>
    </div>

    {{-- Category Stats --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Events by Category</h6>
                <canvas id="categoryChart" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Events --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between">
                <h6 class="fw-semibold mb-0">Recent Events</h6>
                <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="small text-muted fw-semibold ps-3">Event</th>
                                <th class="small text-muted fw-semibold">Organizer</th>
                                <th class="small text-muted fw-semibold">Date</th>
                                <th class="small text-muted fw-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_events as $event)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-medium small">{{ Str::limit($event->title, 35) }}</div>
                                    <div class="text-muted" style="font-size:.75rem">{{ $event->category->name }}</div>
                                </td>
                                <td class="small">{{ $event->organizer->name }}</td>
                                <td class="small text-muted">{{ \Carbon\Carbon::parse($event->start_date)->format('M j, Y') }}

                                <td>
                                    <span class="badge rounded-pill {{ match($event->status) {
                                        'upcoming' => 'bg-info',
                                        'ongoing' => 'bg-success',
                                        'completed' => 'bg-secondary',
                                        'cancelled' => 'bg-danger',
                                        'draft' => 'bg-light text-dark',
                                        default => 'bg-primary',
                                    } }}">{{ ucfirst($event->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No events yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Events --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0">Top Events by Registrations</h6>
            </div>
            <div class="card-body">
                @forelse($top_events as $i => $event)
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="fw-bold text-muted" style="width:20px">{{ $i+1 }}</div>
                    <div class="flex-grow-1">
                        <div class="fw-medium small">{{ Str::limit($event->title, 40) }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $event->registrations_count }} registrations</div>
                    </div>
                    <div style="width:80px">
                        <div class="progress" style="height:6px">
                            <div class="progress-bar bg-primary" style="width:{{ $top_events->max('registrations_count') > 0 ? ($event->registrations_count / $top_events->max('registrations_count') * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-3 small">No registrations yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Revenue Chart
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const revenueData = @json($monthly_revenue);
const revenueValues = months.map((_, i) => revenueData[i+1] || 0);

new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Revenue (₱)',
            data: revenueValues,
            backgroundColor: 'rgba(99,102,241,0.8)',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
    }
});

// Category Chart
const catData = @json($category_stats);
new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: catData.map(c => c.name),
        datasets: [{ data: catData.map(c => c.events_count), backgroundColor: catData.map(c => c.color || '#6366f1'), borderWidth: 2, borderColor: '#fff' }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }, cutout: '65%' }
});
</script>
@endpush
