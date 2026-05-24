{{-- resources/views/attendee/feedback/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Review Event')
@section('hide_hero', '1')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="{{ $event->banner_url }}" class="rounded-3" style="width:80px;height:60px;object-fit:cover;">
                        <div>
                            <h5 class="fw-bold mb-1">Rate Your Experience</h5>
                            <div class="text-muted small">{{ $event->title }}</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('attendee.feedback.store', $event) }}">
                        @csrf

                        {{-- Star Ratings --}}
                        @foreach([
                            ['name' => 'overall_rating',      'label' => 'Overall Experience', 'required' => true],
                            ['name' => 'content_rating',      'label' => 'Content / Program',  'required' => false],
                            ['name' => 'organization_rating', 'label' => 'Organization',        'required' => false],
                            ['name' => 'venue_rating',        'label' => 'Venue / Location',    'required' => false],
                        ] as $rating)
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                {{ $rating['label'] }}
                                @if($rating['required'])<span class="text-danger">*</span>@endif
                            </label>
                            <div class="star-rating d-flex gap-2">
                                @for($i = 5; $i >= 1; $i--)
                                <input type="radio" class="d-none" name="{{ $rating['name'] }}" id="{{ $rating['name'] }}_{{ $i }}" value="{{ $i }}" {{ $rating['required'] && $i == 5 ? 'checked' : '' }}>
                                <label for="{{ $rating['name'] }}_{{ $i }}" class="fs-2" style="cursor:pointer;filter:grayscale(1);transition:filter .1s" data-star="{{ $i }}">⭐</label>
                                @endfor
                            </div>
                            @error($rating['name'])<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        @endforeach

                        <div class="mb-3">
                            <label class="form-label fw-medium">Your Review</label>
                            <textarea name="comment" class="form-control" rows="4" placeholder="Share your experience with others...">{{ old('comment') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Suggestions for Improvement</label>
                            <textarea name="suggestions" class="form-control" rows="3" placeholder="Any suggestions for the organizer?">{{ old('suggestions') }}</textarea>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="would_recommend" id="recommend" value="1" checked>
                            <label class="form-check-label" for="recommend">👍 I would recommend this event to others</label>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold">
                                <i class="bi bi-send me-2"></i>Submit Review
                            </button>
                            <a href="{{ route('attendee.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.star-rating').forEach(group => {
    const labels = [...group.querySelectorAll('label')].reverse();
    labels.forEach((label, i) => {
        label.addEventListener('mouseover', () => labels.forEach((l, j) => l.style.filter = j <= i ? 'none' : 'grayscale(1)'));
        label.addEventListener('mouseout',  () => {
            const checked = group.querySelector('input:checked');
            if (checked) {
                const val = parseInt(checked.value) - 1;
                labels.forEach((l, j) => l.style.filter = j <= val ? 'none' : 'grayscale(1)');
            }
        });
    });
});
</script>
@endpush