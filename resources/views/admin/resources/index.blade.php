@extends('layouts.admin')
@section('title', 'Resources')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Resources</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Equipment & Resources</h4>
</div>

<div class="row g-4">
    {{-- Resource List --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 small text-muted fw-semibold">Resource</th>
                                <th class="small text-muted fw-semibold">Category</th>
                                <th class="small text-muted fw-semibold">Total</th>
                                <th class="small text-muted fw-semibold">Available</th>
                                <th class="small text-muted fw-semibold">Cost/Unit</th>
                                <th class="small text-muted fw-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($resources as $resource)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-medium">{{ $resource->name }}</div>
                                    @if($resource->description)
                                        <div class="text-muted small">{{ Str::limit($resource->description, 40) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $resource->category)) }}</span>
                                </td>
                                <td class="small">{{ number_format($resource->quantity_total) }} {{ $resource->unit }}</td>
                                <td class="small">
                                    <span class="{{ $resource->quantity_available < 5 ? 'text-danger fw-semibold' : 'text-success' }}">
                                        {{ number_format($resource->quantity_available) }}
                                    </span>
                                </td>
                                <td class="small">₱{{ number_format($resource->cost_per_unit, 2) }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ $resource->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $resource->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-box-seam d-block fs-2 mb-2"></i>No resources yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($resources->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $resources->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Add Resource Form --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0">Add Resource</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.resources.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" required
                               placeholder="e.g. Folding Chairs">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select form-select-sm" required>
                            <option value="">Select...</option>
                            <option value="furniture">Furniture</option>
                            <option value="av_equipment">AV Equipment</option>
                            <option value="staging">Staging</option>
                            <option value="electrical">Electrical</option>
                            <option value="catering">Catering</option>
                            <option value="decoration">Decoration</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-medium small">Total Qty <span class="text-danger">*</span></label>
                            <input type="number" name="quantity_total" class="form-control form-control-sm" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-medium small">Available <span class="text-danger">*</span></label>
                            <input type="number" name="quantity_available" class="form-control form-control-sm" min="0" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-medium small">Unit</label>
                            <input type="text" name="unit" class="form-control form-control-sm" placeholder="piece, set...">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-medium small">Cost/Unit (₱)</label>
                            <input type="number" name="cost_per_unit" class="form-control form-control-sm" step="0.01" min="0">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Add Resource</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
