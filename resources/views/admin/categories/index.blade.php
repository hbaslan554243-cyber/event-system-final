@extends('layouts.admin')
@section('title', 'Categories')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Categories</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Event Categories</h4>
</div>

<div class="row g-4">
    {{-- Category List --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 small text-muted fw-semibold">Category</th>
                                <th class="small text-muted fw-semibold">Icon</th>
                                <th class="small text-muted fw-semibold">Color</th>
                                <th class="small text-muted fw-semibold">Events</th>
                                <th class="small text-muted fw-semibold pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-medium">{{ $category->name }}</div>
                                    @if($category->description)
                                        <div class="text-muted small">{{ Str::limit($category->description, 40) }}</div>
                                    @endif
                                </td>
                                <td class="fs-5">{{ $category->icon ?? '📅' }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle" style="width:20px;height:20px;background:{{ $category->color }}"></div>
                                        <span class="small text-muted">{{ $category->color }}</span>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark">{{ $category->events_count }}</span></td>
                                <td class="pe-4">
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline-primary"
                                                onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->icon }}', '{{ $category->color }}', '{{ $category->description }}')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @if($category->events_count == 0)
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                              onsubmit="return confirm('Delete this category?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No categories yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Add / Edit Form --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0" id="formTitle">Add New Category</h6>
            </div>
            <div class="card-body">
                <form method="POST" id="categoryForm" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="category_id" id="categoryId">

                    <div class="mb-3">
                        <label class="form-label fw-medium small">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="catName" class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Tech Conference" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-medium small">Icon (Emoji)</label>
                            <input type="text" name="icon" id="catIcon" class="form-control" placeholder="💻">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-medium small">Color</label>
                            <input type="color" name="color" id="catColor" class="form-control form-control-color w-100"
                                   value="#6366f1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small">Description</label>
                        <textarea name="description" id="catDesc" class="form-control" rows="2"
                                  placeholder="Brief description..."></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-check-lg me-1"></i><span id="submitBtn">Add Category</span>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editCategory(id, name, icon, color, description) {
    document.getElementById('formTitle').textContent = 'Edit Category';
    document.getElementById('submitBtn').textContent = 'Save Changes';
    document.getElementById('categoryId').value = id;
    document.getElementById('catName').value = name;
    document.getElementById('catIcon').value = icon;
    document.getElementById('catColor').value = color || '#6366f1';
    document.getElementById('catDesc').value = description;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('categoryForm').action = '/admin/categories/' + id;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('formTitle').textContent = 'Add New Category';
    document.getElementById('submitBtn').textContent = 'Add Category';
    document.getElementById('categoryForm').reset();
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('categoryForm').action = '{{ route("admin.categories.store") }}';
    document.getElementById('catColor').value = '#6366f1';
}
</script>
@endpush
