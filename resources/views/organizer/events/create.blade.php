@extends('layouts.admin')
@section('title', isset($event) ? 'Edit Event' : 'Create Event')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('organizer.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('organizer.events.index') }}">Events</a></li>
    <li class="breadcrumb-item active">{{ isset($event) ? 'Edit' : 'Create' }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">{{ isset($event) ? 'Edit Event' : 'Create New Event' }}</h4>
    @isset($event)
        <a href="{{ route('organizer.events.show', $event) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
    @endisset
</div>

<form method="POST" action="{{ isset($event) ? route('organizer.events.update', $event) : route('organizer.events.store') }}" enctype="multipart/form-data">
    @csrf
    @isset($event) @method('PUT') @endisset

    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-8">

            {{-- Basic Info --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3"><h6 class="fw-semibold mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Basic Information</h6></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Event Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror" placeholder="e.g. Annual Tech Conference 2025" value="{{ old('title', $event->title ?? '') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $event->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->icon ?? '' }} {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Max Attendees</label>
                            <input type="number" name="max_attendees" class="form-control" min="1" placeholder="Leave blank for unlimited" value="{{ old('max_attendees', $event->max_attendees ?? '') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Short Description</label>
                        <input type="text" name="short_description" class="form-control" maxlength="500" placeholder="One-line summary (used in cards)" value="{{ old('short_description', $event->short_description ?? '') }}">
                        <div class="form-text">Max 500 characters. Shown in event listing cards.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Full Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="8" placeholder="Describe your event in detail..." required>{{ old('description', $event->description ?? '') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Date & Venue --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3"><h6 class="fw-semibold mb-0"><i class="bi bi-clock me-2 text-primary"></i>Date, Time & Venue</h6></div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Start Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', isset($event) ? $event->start_date->format('Y-m-d\TH:i') : '') }}" required>
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">End Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', isset($event) ? $event->end_date->format('Y-m-d\TH:i') : '') }}" required>
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_online" id="is_online" value="1" {{ old('is_online', $event->is_online ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-medium" for="is_online">This is an online event</label>
                        </div>
                    </div>

                    <div id="online_url_section" class="mb-3 {{ old('is_online', $event->is_online ?? false) ? '' : 'd-none' }}">
                        <label class="form-label fw-medium">Meeting URL</label>
                        <input type="url" name="online_meeting_url" class="form-control" placeholder="https://zoom.us/j/..." value="{{ old('online_meeting_url', $event->online_meeting_url ?? '') }}">
                    </div>

                    <div id="venue_section" class="{{ old('is_online', $event->is_online ?? false) ? 'd-none' : '' }}">
                        <label class="form-label fw-medium">Venue</label>
                        <select name="venue_id" class="form-select @error('venue_id') is-invalid @enderror" id="venue_select">
                            <option value="">— Select Venue (optional) —</option>
                            @foreach($venues as $venue)
                                <option value="{{ $venue->id }}" {{ old('venue_id', $event->venue_id ?? '') == $venue->id ? 'selected' : '' }}
                                    data-capacity="{{ $venue->capacity }}" data-city="{{ $venue->city }}">
                                    {{ $venue->name }} — {{ $venue->city }} ({{ number_format($venue->capacity) }} cap.)
                                </option>
                            @endforeach
                        </select>
                        @error('venue_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="venue_conflict_alert" class="alert alert-warning small mt-2 d-none">
                            <i class="bi bi-exclamation-triangle me-1"></i>This venue may have a scheduling conflict. Please verify availability.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">

            {{-- Banner --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3"><h6 class="fw-semibold mb-0"><i class="bi bi-image me-2 text-primary"></i>Banner Image</h6></div>
                <div class="card-body">
                    @isset($event)
                        @if($event->banner_image)
                            <img src="{{ $event->banner_url }}" class="img-fluid rounded mb-3" alt="Current banner">
                        @endif
                    @endisset
                    <input type="file" name="banner_image" class="form-control @error('banner_image') is-invalid @enderror" accept="image/*" id="bannerInput">
                    <div class="form-text">JPG, PNG or WebP. Max 5MB. Recommended: 1200×630px</div>
                    <img id="bannerPreview" class="img-fluid rounded mt-2 d-none">
                    @error('banner_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Settings --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3"><h6 class="fw-semibold mb-0"><i class="bi bi-gear me-2 text-primary"></i>Settings</h6></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Tags</label>
                        <input type="text" name="tags[]" class="form-control form-control-sm" placeholder="php, laravel, webdev (comma separated)" value="{{ old('tags') ? implode(', ', old('tags')) : (isset($event) ? implode(', ', $event->tags ?? []) : '') }}">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="requirements[]" id="chkEmail" value="email_verified">
                        <label class="form-check-label small" for="chkEmail">Require email verification</label>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2 py-2 fw-semibold">
                        <i class="bi bi-check-lg me-2"></i>{{ isset($event) ? 'Save Changes' : 'Create Event' }}
                    </button>
                    @isset($event)
                    <a href="{{ route('organizer.events.show', $event) }}" class="btn btn-outline-secondary w-100">Cancel</a>
                    @else
                    <a href="{{ route('organizer.events.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                    @endisset
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Online toggle
document.getElementById('is_online').addEventListener('change', function() {
    document.getElementById('online_url_section').classList.toggle('d-none', !this.checked);
    document.getElementById('venue_section').classList.toggle('d-none', this.checked);
});

// Banner preview
document.getElementById('bannerInput').addEventListener('change', function() {
    const reader = new FileReader();
    const preview = document.getElementById('bannerPreview');
    reader.onload = e => { preview.src = e.target.result; preview.classList.remove('d-none'); };
    if (this.files[0]) reader.readAsDataURL(this.files[0]);
});

// Venue conflict check
const startDate = document.querySelector('[name="start_date"]');
const endDate   = document.querySelector('[name="end_date"]');
const venueSelect = document.getElementById('venue_select');

async function checkVenueConflict() {
    const venueId = venueSelect.value;
    if (!venueId || !startDate.value || !endDate.value) return;

    const res = await fetch(`/api/venue-availability?venue_id=${venueId}&start=${startDate.value}&end=${endDate.value}&exclude_event_id={{ $event->id ?? '' }}`, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
    });
    const data = await res.json();
    document.getElementById('venue_conflict_alert').classList.toggle('d-none', data.available);
}

[venueSelect, startDate, endDate].forEach(el => el?.addEventListener('change', checkVenueConflict));
</script>
@endpush
