@extends('layouts.admin')
@section('title', 'Feedback Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Feedback</li>
@endsection

@section('content')
<h4 class="fw-bold mb-4">Feedback & Reviews Report</h4>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-warning">{{ number_format($data['avg_rating'] ?? 0, 1) }}</div>
            <div class="text-warning mb-1">
                @for($i = 1; $i <= 5; $i++)
                    <i class="bi bi-star{{ $i <= round($data['avg_rating'] ?? 0) ? '-fill' : '' }}"></i>
                @endfor
            </div>
            <div class="small text-muted">Average Rating</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-primary">{{ number_format($data['total_reviews']) }}</div>
            <div class="small text-muted">Total Reviews</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-success">{{ $data['by_rating'][5] ?? 0 }}</div>
            <div class="small text-muted">5-Star Reviews</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-danger">{{ $data['by_rating'][1] ?? 0 }}</div>
            <div class="small text-muted">1-Star Reviews</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Rating Distribution --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Rating Distribution</h6>
                @foreach([5,4,3,2,1] as $star)
                @php $count = $data['by_rating'][$star] ?? 0; $pct = $data['total_reviews'] > 0 ? round($count / $data['total_reviews'] * 100) : 0; @endphp
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="small text-muted" style="width:15px">{{ $star }}</span>
                    <i class="bi bi-star-fill text-warning small"></i>
                    <div class="progress flex-grow-1" style="height:10px">
                        <div class="progress-bar bg-warning" style="width:{{ $pct }}%"></div>
                    </div>
                    <span class="small text-muted" style="width:30px">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Top Rated Events --}}
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0">Top Rated Events</h6>
            </div>
            <div class="card-body p-0">
                @forelse($data['top_rated'] as $event)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div class="flex-grow-1">
                        <div class="fw-medium small">{{ Str::limit($event->title, 40) }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $event->total_reviews }} reviews</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-warning">{{ number_format($event->avg_rating, 1) }}</div>
                        <div class="text-warning" style="font-size:.65rem">
                            @for($i = 1; $i <= 5; $i++)<i class="bi bi-star{{ $i <= round($event->avg_rating) ? '-fill' : '' }}"></i>@endfor
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted small">No rated events yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Reviews --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0">Recent Reviews</h6>
            </div>
            <div class="card-body" style="max-height:600px;overflow-y:auto">
                @forelse($data['recent'] as $fb)
                <div class="d-flex gap-3 mb-4 pb-3 border-bottom">
                    <img src="{{ $fb->user->avatar_url }}" class="rounded-circle flex-shrink-0" width="40" height="40">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fw-semibold small">{{ $fb->user->name }}</span>
                                <span class="text-muted small ms-2">on {{ Str::limit($fb->event->title, 30) }}</span>
                            </div>
                            <div class="text-warning small">
                                @for($i = 1; $i <= 5; $i++)<i class="bi bi-star{{ $i <= $fb->overall_rating ? '-fill' : '' }}"></i>@endfor
                            </div>
                        </div>
                        @if($fb->comment)
                            <p class="text-muted small mb-1 mt-1">{{ $fb->comment }}</p>
                        @endif
                        <div class="text-muted" style="font-size:.7rem">
                            {{ $fb->created_at->diffForHumans() }}
                            @if($fb->would_recommend) · 👍 Would recommend @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">No reviews yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
