{{-- Ticket Selection --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="fw-semibold mb-0">1. Select Ticket</h6>
    </div>
    <div class="card-body">
        @if($ticketTypes->isEmpty())
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                No tickets available for this event yet.
            </div>
        @else
            @foreach($ticketTypes as $tt)
            <div class="mb-3">
                <input type="radio" class="btn-check ticket-radio"
                       name="ticket_type_id"
                       id="tt_{{ $tt->id }}"
                       value="{{ $tt->id }}"
                       data-price="{{ $tt->price }}"
                       data-type="{{ $tt->type }}"
                       {{ $loop->first ? 'checked' : '' }}
                       required>
                <label class="btn btn-outline-primary w-100 text-start p-3" for="tt_{{ $tt->id }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">
                                {{ $tt->name }}
                                <span class="badge ms-2
                                    {{ match($tt->type) {
                                        'vip'  => 'bg-warning text-dark',
                                        'free' => 'bg-success',
                                        default => 'bg-primary'
                                    } }}">
                                    {{ strtoupper($tt->type) }}
                                </span>
                            </div>
                            @if($tt->description)
                                <div class="small opacity-75 mt-1">{{ $tt->description }}</div>
                            @endif
                            <div class="small opacity-75 mt-1">
                                {{ $tt->quantity_remaining }} tickets remaining
                            </div>
                        </div>
                        <div class="text-end ms-3">
                            <div class="fw-bold fs-5">
                                {{ $tt->price == 0 ? 'FREE' : '₱' . number_format($tt->price, 2) }}
                            </div>
                            <div class="small opacity-75">per ticket</div>
                        </div>
                    </div>
                </label>
            </div>
            @endforeach
        @endif
        @error('ticket_type_id')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror
    </div>
</div>
