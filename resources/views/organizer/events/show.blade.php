@extends('layouts.admin')
@section('title', $event->title)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('organizer.events.index') }}">Events</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($event->title, 30) }}</li>
@endsection

@section('content')
{{-- Header --}}
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; overflow:hidden;">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex gap-2 mb-2">
                    <span class="badge bg-white text-primary">{{ $event->category->name }}</span>
                    <span class="badge {{ match($event->status) { 'upcoming'=>'bg-info', 'ongoing'=>'bg-success', 'completed'=>'bg-secondary', 'cancelled'=>'bg-danger', default=>'bg-warning text-dark' } }}">
                        {{ ucfirst($event->status) }}
                    </span>
                    @if($event->is_featured)<span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Featured</span>@endif
                </div>
                <h3 class="fw-bold mb-2">{{ $event->title }}</h3>
                <div class="d-flex flex-wrap gap-3 opacity-90 small">
                    <span><i class="bi bi-calendar3 me-1"></i>{{ $event->start_date->format('M d, Y • g:i A') }}</span>
                    <span><i class="bi bi-geo-alt me-1"></i>{{ $event->is_online ? 'Online Event' : ($event->venue?->name ?? 'TBA') }}</span>
                    <span><i class="bi bi-people me-1"></i>{{ $stats['registrations'] }} / {{ $event->max_attendees ?? '∞' }} registered</span>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    @if($event->status === 'draft')
                        <form method="POST" action="{{ route('organizer.events.submitReview', $event) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-light" {{ !$event->ticketTypes()->where('is_active',true)->exists() ? 'disabled' : '' }}>
                                <i class="bi bi-send me-2"></i>Submit for Admin Review
                            </button>
                        </form>
                    @elseif($event->status === 'pending_review')
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                            <i class="bi bi-hourglass-split me-1"></i>Awaiting Admin Approval
                        </span>
                    @endif
                    <a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-light"><i class="bi bi-pencil me-2"></i>Edit</a>
                    <a href="{{ route('events.show', $event->slug) }}" class="btn btn-outline-light" target="_blank"><i class="bi bi-eye me-2"></i>Preview</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-sm-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-primary">{{ $stats['registrations'] }}</div>
            <div class="small text-muted">Registrations</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-success">₱{{ number_format($stats['revenue'], 0) }}</div>
            <div class="small text-muted">Revenue</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-info" id="checkedInCount">{{ $stats['checked_in'] }}</div>
            <div class="small text-muted">Checked In</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-warning">{{ number_format($stats['avg_rating'] ?? 0, 1) }}</div>
            <div class="small text-muted">Avg Rating</div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4" id="eventTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tickets">🎟 Tickets</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#attendees">👥 Attendees</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#announcements">📢 Announcements</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#media">📸 Media</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#feedback">⭐ Feedback</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#resources">📦 Resources</a></li>
</ul>

<div class="tab-content">
    {{-- Ticket Types --}}
    <div class="tab-pane fade show active" id="tickets">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3"><h6 class="fw-semibold mb-0">Ticket Types</h6></div>
                    <div class="card-body p-0">
                        @forelse($event->ticketTypes as $tt)
                        <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $tt->name }}
                                    <span class="badge ms-2 {{ match($tt->type) { 'vip'=>'bg-warning text-dark','free'=>'bg-success','paid'=>'bg-primary',default=>'bg-secondary' } }}">{{ strtoupper($tt->type) }}</span>
                                </div>
                                <div class="text-muted small">₱{{ number_format($tt->price, 2) }} · {{ $tt->quantity_remaining }}/{{ $tt->quantity_available }} remaining · Max {{ $tt->max_per_person }}/person</div>
                            </div>
                            <div class="d-flex gap-1">
                                <span class="badge bg-light text-dark">{{ $tt->quantity_sold }} sold</span>
                                <form method="POST" action="{{ route('organizer.ticket-types.destroy', $tt) }}" onsubmit="return confirm('Delete this ticket type?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted small">No ticket types yet. Add one below.</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3"><h6 class="fw-semibold mb-0">Add Ticket Type</h6></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('organizer.ticket-types.store', $event) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium small">Name</label>
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. VIP, General, Early Bird" required>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-medium small">Type</label>
                                    <select name="type" class="form-select form-select-sm" id="ttType">
                                        <option value="paid">Paid</option>
                                        <option value="free">Free</option>
                                        <option value="vip">VIP</option>
                                    </select>
                                </div>
                                <div class="col-6" id="priceField">
                                    <label class="form-label fw-medium small">Price (₱)</label>
                                    <input type="number" name="price" class="form-control form-control-sm" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-medium small">Quantity</label>
                                    <input type="number" name="quantity_available" class="form-control form-control-sm" min="1" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-medium small">Max/Person</label>
                                    <input type="number" name="max_per_person" class="form-control form-control-sm" min="1" value="5">
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-medium small">Sale Start</label>
                                    <input type="datetime-local" name="sale_start" class="form-control form-control-sm">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-medium small">Sale End</label>
                                    <input type="datetime-local" name="sale_end" class="form-control form-control-sm">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Add Ticket Type</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Attendees --}}
    <div class="tab-pane fade" id="attendees">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between">
                <h6 class="fw-semibold mb-0">Attendee List</h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#scanModal">
                        <i class="bi bi-qr-code-scan me-1"></i>Scan QR
                    </button>
                    <a href="{{ route('organizer.attendees.index', $event) }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light"><tr>
                            <th class="ps-4 small text-muted fw-semibold">Attendee</th>
                            <th class="small text-muted fw-semibold">Ticket</th>
                            <th class="small text-muted fw-semibold">Payment</th>
                            <th class="small text-muted fw-semibold">Status</th>
                            <th class="small text-muted fw-semibold">Checked In</th>
                            <th class="small text-muted fw-semibold">Action</th>
                        </tr></thead>
                        <tbody>
                            @forelse($event->registrations->take(10) as $reg)
                            <tr id="reg-row-{{ $reg->id }}">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $reg->user->avatar_url }}" class="rounded-circle" width="30" height="30">
                                        <div>
                                            <div class="fw-medium small">{{ $reg->user->name }}</div>
                                            <div class="text-muted" style="font-size:.7rem">{{ $reg->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">{{ $reg->ticketType->name }}</td>
                                <td><span class="badge rounded-pill {{ $reg->payment_status === 'paid' || $reg->payment_status === 'free' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($reg->payment_status) }}</span></td>
                                <td>
                                    <span class="badge rounded-pill {{ $reg->status === 'attended' ? 'bg-success' : 'bg-info' }}"
                                          id="status-{{ $reg->id }}">
                                        {{ ucfirst($reg->status) }}
                                    </span>
                                </td>
                                <td class="small text-muted" id="checkin-{{ $reg->id }}">
                                    {{ $reg->checked_in_at?->format('M d • g:i A') ?? '—' }}
                                </td>
                                <td id="action-{{ $reg->id }}">
                                    @if($reg->status === 'confirmed')
                                        <button class="btn btn-sm btn-success"
                                                onclick="checkIn({{ $reg->id }}, this)">
                                            <i class="bi bi-check-lg me-1"></i>Check In
                                        </button>
                                    @elseif($reg->status === 'attended')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-all me-1"></i>Done
                                        </span>
                                    @else
                                        <span class="text-muted small">{{ ucfirst($reg->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No registrations yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Announcements --}}
    <div class="tab-pane fade" id="announcements">
        <div class="row g-4">
            <div class="col-lg-7">
                @forelse($event->announcements as $ann)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex gap-2 align-items-start">
                            <span class="badge {{ match($ann->type) {'urgent'=>'bg-danger','warning'=>'bg-warning text-dark','update'=>'bg-info',default=>'bg-primary'} }} mt-1">{{ ucfirst($ann->type) }}</span>
                            <div>
                                <div class="fw-semibold">{{ $ann->title }}</div>
                                <div class="text-muted small mt-1">{{ $ann->message }}</div>
                                <div class="text-muted mt-2" style="font-size:.7rem">Sent {{ $ann->sent_at?->diffForHumans() ?? 'Scheduled' }} by {{ $ann->creator->name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">No announcements sent yet.</div>
                @endforelse
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3"><h6 class="fw-semibold mb-0">Send Announcement</h6></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('organizer.announcements.store', $event) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium small">Type</label>
                                <select name="type" class="form-select form-select-sm">
                                    <option value="info">📋 Info</option>
                                    <option value="update">🔄 Update</option>
                                    <option value="warning">⚠️ Warning</option>
                                    <option value="urgent">🚨 Urgent</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium small">Title</label>
                                <input type="text" name="title" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium small">Message</label>
                                <textarea name="message" class="form-control form-control-sm" rows="4" required></textarea>
                            </div>
                            <div class="d-flex gap-3 mb-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="send_email" id="sendEmail" value="1" checked>
                                    <label class="form-check-label small" for="sendEmail">Send Email</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Send to All Attendees</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Media --}}
    <div class="tab-pane fade" id="media">
        <div class="row g-3 mb-4">
            @forelse($event->media as $media)
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm overflow-hidden">
                    @if($media->file_type === 'image')
                        <img src="{{ $media->url }}" class="card-img-top" style="height:160px;object-fit:cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height:160px">
                            <i class="bi bi-file-earmark-{{ $media->file_type === 'video' ? 'play' : 'text' }} fs-1 text-muted"></i>
                        </div>
                    @endif
                    <div class="card-body p-2">
                        <div class="small fw-medium text-truncate">{{ $media->title ?? $media->file_name }}</div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="badge bg-light text-muted small">{{ ucfirst($media->category) }}</span>
                            <form method="POST" action="{{ route('organizer.media.destroy', $media) }}" onsubmit="return confirm('Delete this media?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger py-0 px-1"><i class="bi bi-trash" style="font-size:.7rem"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-4">No media uploaded yet.</div>
            @endforelse
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3"><h6 class="fw-semibold mb-0">Upload Media</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('organizer.media.store', $event) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Files (max 10)</label>
                            <input type="file" name="files[]" class="form-control form-control-sm" multiple accept="image/*,video/*,.pdf,.doc,.docx">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium small">Category</label>
                            <select name="category" class="form-select form-select-sm">
                                <option value="gallery">Gallery</option>
                                <option value="banner">Banner</option>
                                <option value="promotional">Promotional</option>
                                <option value="document">Document</option>
                                <option value="video">Video</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium small">&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-sm d-block w-100">Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Feedback --}}
    <div class="tab-pane fade" id="feedback">
        @forelse($event->feedbacks as $fb)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex align-items-start gap-3">
                    <img src="{{ $fb->user->avatar_url }}" class="rounded-circle" width="40" height="40">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <div class="fw-semibold">{{ $fb->user->name }}</div>
                            <div class="text-warning">{{ str_repeat('★', $fb->overall_rating) }}{{ str_repeat('☆', 5 - $fb->overall_rating) }}</div>
                        </div>
                        @if($fb->comment)<p class="text-muted small mb-1 mt-1">{{ $fb->comment }}</p>@endif
                        @if($fb->suggestions)<p class="text-info small mb-1"><i class="bi bi-lightbulb me-1"></i>{{ $fb->suggestions }}</p>@endif
                        <div class="small text-muted mt-1">{{ $fb->created_at->diffForHumans() }} · {{ $fb->would_recommend ? '👍 Would recommend' : '👎 Would not recommend' }}</div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-5"><i class="bi bi-star d-block fs-2 mb-2"></i>No feedback yet.</div>
        @endforelse
    </div>

    {{-- Resources --}}
    <div class="tab-pane fade" id="resources">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted small">Manage equipment and resources assigned to this event. Contact admin to assign or modify resources.</p>
                <div class="table-responsive">
                    <table class="table align-middle small">
                        <thead class="bg-light"><tr>
                            <th>Resource</th><th>Category</th><th>Needed</th><th>Assigned</th><th>Status</th>
                        </tr></thead>
                        <tbody>
                            @forelse($event->resources as $res)
                            <tr>
                                <td class="fw-medium">{{ $res->name }}</td>
                                <td>{{ $res->category }}</td>
                                <td>{{ $res->pivot->quantity_needed }} {{ $res->unit }}</td>
                                <td>{{ $res->pivot->quantity_assigned }} {{ $res->unit }}</td>
                                <td><span class="badge bg-{{ $res->pivot->status === 'assigned' ? 'success' : 'warning text-dark' }}">{{ ucfirst($res->pivot->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No resources assigned.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- QR Scanner Modal --}}
<div class="modal fade" id="scanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">QR Code Scanner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="qrResult" class="alert d-none mb-3"></div>
                <div class="mb-3">
                    <label class="form-label fw-medium small">Enter Ticket Number or QR Code</label>
                    <input type="text" id="qrInput" class="form-control"
                           placeholder="e.g. TKT-SETL0HYR2K"
                           onkeydown="if(event.key==='Enter') submitQr()">
                </div>
                <button onclick="submitQr()" class="btn btn-success w-100">
                    <i class="bi bi-qr-code-scan me-2"></i>Validate Ticket
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('ttType').addEventListener('change', function() {
    document.getElementById('priceField').style.display = this.value === 'free' ? 'none' : '';
});

async function submitQr() {
    const input = document.getElementById('qrInput');
    const data  = input.value.trim();
    const resultEl = document.getElementById('qrResult');

    if (!data) {
        resultEl.className = 'alert alert-warning d-block';
        resultEl.innerHTML = '<strong>Please enter a ticket number.</strong>';
        return;
    }

    resultEl.className = 'alert alert-secondary d-block';
    resultEl.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Validating...';

    const res  = await fetch('{{ route("organizer.attendees.scan-qr") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ qr_data: data })
    });

    const json = await res.json();

    resultEl.className = `alert ${json.success ? 'alert-success' : 'alert-danger'} d-block`;
    resultEl.innerHTML = `<strong>${json.message}</strong>`;
    input.value = '';

    // Update the attendee table row if visible
    if (json.success && json.registration_id) {
        const statusEl  = document.getElementById(`status-${json.registration_id}`);
        const checkinEl = document.getElementById(`checkin-${json.registration_id}`);
        const actionEl  = document.getElementById(`action-${json.registration_id}`);

        if (statusEl) {
            statusEl.className = 'badge rounded-pill bg-success';
            statusEl.textContent = 'Attended';
        }
        if (checkinEl) {
            checkinEl.textContent = 'Just now';
        }
        if (actionEl) {
            actionEl.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-all me-1"></i>Done</span>';
        }

        // Update checked-in counter
        const counter = document.getElementById('checkedInCount');
        if (counter) counter.textContent = parseInt(counter.textContent) + 1;
    }

    input.focus();
}

async function checkIn(regId, btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    try {
        const res  = await fetch(`/organizer/registrations/${regId}/check-in`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        const data = await res.json();

        if (res.ok) {
            document.getElementById(`status-${regId}`).className  = 'badge rounded-pill bg-success';
            document.getElementById(`status-${regId}`).textContent = 'Attended';
            document.getElementById(`checkin-${regId}`).textContent = 'Just now';
            document.getElementById(`action-${regId}`).innerHTML =
                '<span class="badge bg-success"><i class="bi bi-check-all me-1"></i>Done</span>';

            // Update checked-in counter
            const counter = document.getElementById('checkedInCount');
            if (counter) counter.textContent = parseInt(counter.textContent) + 1;
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Check In';
            alert(data.message || 'Check-in failed');
        }
    } catch (e) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Check In';
        alert('Something went wrong');
    }
}
</script>
@endpush