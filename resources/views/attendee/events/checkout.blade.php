@extends('layouts.app')
@section('title', 'Register — ' . $event->title)
@section('hide_hero', '1')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Events</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('events.show', $event->slug) }}">{{ Str::limit($event->title, 30) }}</a></li>
                    <li class="breadcrumb-item active">Register</li>
                </ol>
            </nav>

            <h3 class="fw-bold mb-4">Complete Your Registration</h3>

            <div class="row g-4">
                {{-- Form --}}
                <div class="col-lg-7">
                    <form method="POST" action="{{ route('attendee.events.register', $event) }}" id="checkoutForm">
                        @csrf

                        {{-- Ticket Selection --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-0 py-3"><h6 class="fw-semibold mb-0">1. Select Ticket</h6></div>
                            <div class="card-body">
                                @foreach($ticketTypes as $tt)
                                <div class="mb-3">
                                    <input type="radio" class="btn-check ticket-radio" name="ticket_type_id" id="tt_{{ $tt->id }}" value="{{ $tt->id }}"
                                        data-price="{{ $tt->price }}" data-type="{{ $tt->type }}" required {{ $loop->first ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100 text-start p-3" for="tt_{{ $tt->id }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-semibold">{{ $tt->name }}
                                                    <span class="badge ms-2 {{ match($tt->type){ 'vip'=>'bg-warning text-dark','free'=>'bg-success',default=>'bg-primary'} }}">{{ strtoupper($tt->type) }}</span>
                                                </div>
                                                @if($tt->description)<div class="small opacity-75">{{ $tt->description }}</div>@endif
                                                <div class="small opacity-75">{{ $tt->quantity_remaining }} remaining</div>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold fs-5">{{ $tt->price == 0 ? 'FREE' : '₱' . number_format($tt->price, 2) }}</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                                @error('ticket_type_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Quantity --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-0 py-3"><h6 class="fw-semibold mb-0">2. Quantity</h6></div>
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3">
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQty(-1)">−</button>
                                    <input type="number" name="quantity" id="qty" class="form-control text-center" value="1" min="1" max="10" style="width:80px" readonly>
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQty(1)">+</button>
                                    <span class="text-muted small">per person max: 10</span>
                                </div>
                            </div>
                        </div>

                        {{-- Coupon --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-0 py-3"><h6 class="fw-semibold mb-0">3. Coupon Code (optional)</h6></div>
                            <div class="card-body">
                                <div class="input-group">
                                    <input type="text" name="coupon_code" id="couponInput" class="form-control" placeholder="Enter coupon code">
                                    <button type="button" class="btn btn-outline-primary" onclick="applyCoupon()">Apply</button>
                                </div>
                                <div id="couponMsg" class="small mt-2"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-5">
                            <i class="bi bi-arrow-right-circle me-2"></i>Proceed to Payment
                        </button>
                    </form>
                </div>

                {{-- Order Summary --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm sticky-top" style="top:80px">
                        <div class="card-body">
                            <img src="{{ $event->banner_url }}" class="img-fluid rounded-3 mb-3" style="height:140px;width:100%;object-fit:cover;">
                            <h6 class="fw-bold">{{ $event->title }}</h6>
                            <div class="text-muted small mb-3">
                                <div><i class="bi bi-calendar3 me-1"></i>{{ $event->start_date->format('M d, Y • g:i A') }}</div>
                                <div><i class="bi bi-geo-alt me-1"></i>{{ $event->is_online ? 'Online' : ($event->venue?->name ?? 'TBA') }}</div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Ticket price</span>
                                <span id="unitPrice">—</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Quantity</span>
                                <span id="summaryQty">1</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2 text-success" id="discountRow" style="display:none!important">
                                <span>Discount</span>
                                <span id="discountAmt">—</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total</span>
                                <span id="totalPrice">—</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let ticketPrices = {};
@foreach($ticketTypes as $tt)
    ticketPrices['{{ $tt->id }}'] = {{ $tt->price }};
@endforeach
let discount = 0;

function updateSummary() {
    const selectedId = document.querySelector('[name=ticket_type_id]:checked')?.value;
    const qty = parseInt(document.getElementById('qty').value) || 1;
    const price = ticketPrices[selectedId] ?? 0;
    const subtotal = price * qty;
    const total = Math.max(0, subtotal - discount);

    document.getElementById('unitPrice').textContent = price === 0 ? 'FREE' : '₱' + price.toFixed(2);
    document.getElementById('summaryQty').textContent = qty;
    document.getElementById('totalPrice').textContent = total === 0 ? 'FREE' : '₱' + total.toFixed(2);
    document.getElementById('discountRow').style.display = discount > 0 ? '' : 'none';
    document.getElementById('discountAmt').textContent = '-₱' + discount.toFixed(2);
}

function changeQty(delta) {
    const el = document.getElementById('qty');
    el.value = Math.max(1, Math.min(10, parseInt(el.value) + delta));
    document.getElementById('summaryQty').textContent = el.value;
    updateSummary();
}

document.querySelectorAll('.ticket-radio').forEach(r => r.addEventListener('change', updateSummary));

async function applyCoupon() {
    const code    = document.getElementById('couponInput').value;
    const eventId = {{ $event->id }};
    const selectedId = document.querySelector('[name=ticket_type_id]:checked')?.value;
    const qty = parseInt(document.getElementById('qty').value) || 1;
    const price = ticketPrices[selectedId] ?? 0;

    const res  = await fetch('/api/validate-coupon', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ event_id: eventId, code, amount: price * qty })
    });
    const data = await res.json();
    const msg  = document.getElementById('couponMsg');
    if (data.valid) {
        discount = data.discount;
        msg.innerHTML = `<span class="text-success"><i class="bi bi-check-circle me-1"></i>Coupon applied! You save ₱${data.discount.toFixed(2)}</span>`;
    } else {
        discount = 0;
        msg.innerHTML = `<span class="text-danger"><i class="bi bi-x-circle me-1"></i>${data.message}</span>`;
    }
    updateSummary();
}

updateSummary();
</script>
@endpush
