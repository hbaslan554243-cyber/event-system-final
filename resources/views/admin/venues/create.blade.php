@extends('layouts.admin')
@section('title', isset($venue) ? 'Edit Venue' : 'Add Venue')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.venues.index') }}">Venues</a></li>
    <li class="breadcrumb-item active">{{ isset($venue) ? 'Edit' : 'Add' }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">{{ isset($venue) ? 'Edit Venue' : 'Add New Venue' }}</h4>
    <a href="{{ route('admin.venues.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ isset($venue) ? route('admin.venues.update', $venue) : route('admin.venues.store') }}"
                      enctype="multipart/form-data">
                    @csrf
                    @isset($venue) @method('PUT') @endisset

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium">Venue Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $venue->name ?? '') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Address <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                                   value="{{ old('address', $venue->address ?? '') }}" required>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                                   value="{{ old('city', $venue->city ?? '') }}" required>
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium">State / Province</label>
                            <input type="text" name="state" class="form-control"
                                   value="{{ old('state', $venue->state ?? '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium">Country</label>
                            <input type="text" name="country" class="form-control"
                                   value="{{ old('country', $venue->country ?? 'Philippines') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Capacity <span class="text-danger">*</span></label>
                            <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror"
                                   min="1" value="{{ old('capacity', $venue->capacity ?? '') }}" required>
                            @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Website</label>
                            <input type="url" name="website" class="form-control"
                                   value="{{ old('website', $venue->website ?? '') }}" placeholder="https://">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium">Contact Person</label>
                            <input type="text" name="contact_person" class="form-control"
                                   value="{{ old('contact_person', $venue->contact_person ?? '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium">Contact Phone</label>
                            <input type="text" name="contact_phone" class="form-control"
                                   value="{{ old('contact_phone', $venue->contact_phone ?? '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium">Contact Email</label>
                            <input type="email" name="contact_email" class="form-control"
                                   value="{{ old('contact_email', $venue->contact_email ?? '') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="Brief description of the venue...">{{ old('description', $venue->description ?? '') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Amenities</label>
                            <div class="row g-2">
                                @foreach(['WiFi', 'Parking', 'Air Conditioning', 'Projector', 'Sound System', 'Stage', 'Catering', 'Security', 'Restrooms', 'Wheelchair Access'] as $amenity)
                                <div class="col-md-3 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="amenities[]"
                                               value="{{ $amenity }}" id="am_{{ Str::slug($amenity) }}"
                                               {{ in_array($amenity, old('amenities', $venue->amenities ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="am_{{ Str::slug($amenity) }}">
                                            {{ $amenity }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        @isset($venue)
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                                       {{ old('is_active', $venue->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="isActive">Active</label>
                            </div>
                        </div>
                        @endisset
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-2"></i>{{ isset($venue) ? 'Save Changes' : 'Create Venue' }}
                        </button>
                        <a href="{{ route('admin.venues.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
