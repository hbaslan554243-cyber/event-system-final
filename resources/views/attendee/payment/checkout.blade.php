@extends('layouts.app')
@section('title', 'Payment')

@section('hide_hero', '1')


@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h3 class="fw-bold mb-4"><i class="bi bi-credit-card me-2 text-primary"></i>Complete Payment</h3>

            <div class="row g-4">
                <div class="col-lg-7">
                    <form method="POST" action="{{ route('attendee.payment.process', $registration) }}" id="paymentForm">
                        @csrf

                        {{-- Order Summary --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <h6 class="fw-semibold mb-3">Order Summary</h6>
                                <div class="d-flex gap-3">
                                    <img src="{{ $registration->event->banner_url }}" class="rounded-3" style="width:80px;height:60px;object-fit:cover;">
                                    <div>
                                        <div class="fw-semibold">{{ $registration->event->title }}</div>
                                        <div class="text-muted small">{{ $registration->ticketType->name }} × {{ $registration->quantity }}</div>
                                        <div class="text-muted small">{{ $registration->event->start_date->format('M d, Y') }}</div>
                                    </div>
                                    <div class="ms-auto fw-bold text-primary">₱{{ number_format($registration->final_amount, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-0 py-3"><h6 class="fw-semibold mb-0">Select Payment Method</h6></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="gateway" id="gwStripe" value="stripe" checked>
                                        <label class="btn btn-outline-secondary w-100 py-3" for="gwStripe">
                                            <i class="bi bi-credit-card-2-front d-block fs-3 mb-1"></i>
                                            <div class="fw-semibold small">Credit/Debit Card</div>
                                            <div style="font-size:.7rem" class="text-muted">Visa, Mastercard</div>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="gateway" id="gwPaypal" value="paypal">
                                        <label class="btn btn-outline-secondary w-100 py-3" for="gwPaypal">
                                            <i class="bi bi-paypal d-block fs-3 mb-1 text-primary"></i>
                                            <div class="fw-semibold small">PayPal</div>
                                            <div style="font-size:.7rem" class="text-muted">International</div>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="gateway" id="gwGcash" value="gcash">
                                        <label class="btn btn-outline-secondary w-100 py-3" for="gwGcash">
                                            <div class="fw-bold text-blue d-block fs-5 mb-1" style="color:#007bff">G</div>
                                            <div class="fw-semibold small">GCash</div>
                                            <div style="font-size:.7rem" class="text-muted">Philippines</div>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="gateway" id="gwBank" value="bank_transfer">
                                        <label class="btn btn-outline-secondary w-100 py-3" for="gwBank">
                                            <i class="bi bi-bank d-block fs-3 mb-1 text-success"></i>
                                            <div class="fw-semibold small">Bank Transfer</div>
                                            <div style="font-size:.7rem" class="text-muted">Manual</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card Fields (shown for Stripe) --}}
                        <div id="cardFields" class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-0 py-3"><h6 class="fw-semibold mb-0">Card Details</h6></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-medium small">Card Number</label>
                                    <input type="text" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19" id="cardNumber">
                                </div>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label fw-medium small">Expiry</label>
                                        <input type="text" class="form-control" placeholder="MM/YY" maxlength="5">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-medium small">CVV</label>
                                        <input type="text" class="form-control" placeholder="123" maxlength="4">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-3 fw-bold fs-5">
                            <i class="bi bi-lock-fill me-2"></i>Pay ₱{{ number_format($registration->final_amount, 2) }} Securely
                        </button>
                        <p class="text-center text-muted small mt-2">
                            <i class="bi bi-shield-check me-1"></i>Secured by SSL encryption
                        </p>
                    </form>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">Payment Details</h6>
                            <ul class="list-unstyled small">
                                <li class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal</span>
                                    <span>₱{{ number_format($registration->total_amount, 2) }}</span>
                                </li>
                                @if($registration->discount_amount > 0)
                                <li class="d-flex justify-content-between mb-2 text-success">
                                    <span>Discount</span>
                                    <span>-₱{{ number_format($registration->discount_amount, 2) }}</span>
                                </li>
                                @endif
                                <li class="d-flex justify-content-between fw-bold border-top pt-2 mt-2">
                                    <span>Total</span>
                                    <span class="text-primary fs-5">₱{{ number_format($registration->final_amount, 2) }}</span>
                                </li>
                            </ul>
                            <hr>
                            <div class="small text-muted">
                                <div class="mb-2"><strong>Reg #:</strong> {{ $registration->registration_number }}</div>
                                <div class="mb-2"><strong>Tickets:</strong> {{ $registration->quantity }} × {{ $registration->ticketType->name }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm mt-3 bg-light">
                        <div class="card-body small text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            After successful payment, you'll receive a confirmation email with your QR code tickets.
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
document.querySelectorAll('[name=gateway]').forEach(r => r.addEventListener('change', function() {
    document.getElementById('cardFields').style.display = this.value === 'stripe' ? '' : 'none';
}));

// Card number formatting
document.getElementById('cardNumber').addEventListener('input', function() {
    this.value = this.value.replace(/\D/g,'').replace(/(.{4})/g,'$1 ').trim().substring(0,19);
});
</script>
@endpush
