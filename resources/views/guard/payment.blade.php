@extends('layouts.guard-sidebar')

@section('title', 'Process Payment')
@section('panel_class', 'guard-panel-shell')
@section('breadcrumbs')
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <a href="{{ route('guard.exit.form') }}" class="text-gray-500 hover:text-blue-600">Vehicle Exit</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-800 font-medium">Process Payment</span>
@endsection

@section('content')
<div class="page-stack guard-workflow">
    <header class="guard-page-header text-center sm:text-left">
        <h1>Process Payment</h1>
        <p>Review parking fees and confirm payment before opening the exit gate.</p>
    </header>

    <div class="guard-receipt">
        <div class="guard-receipt__header">
            <h2>MKKK Mall Parking</h2>
            <p>Exit checkout · Ticket #{{ Str::limit($ticket->qr_code, 16) }}</p>
        </div>
        <div class="guard-receipt__body">
            <div class="guard-receipt__row">
                <span class="guard-receipt__label">Plate Number</span>
                <span class="guard-receipt__value">{{ $ticket->plate_number }}</span>
            </div>
            @if($ticket->parkingSlot)
            <div class="guard-receipt__row">
                <span class="guard-receipt__label">Parking Slot</span>
                <span class="guard-receipt__value">{{ $ticket->parkingSlot->slot_number }}</span>
            </div>
            @endif
            <div class="guard-receipt__row">
                <span class="guard-receipt__label">Entry Time</span>
                <span class="guard-receipt__value">{{ $ticket->entry_time->format('M d, Y · H:i:s') }}</span>
            </div>
            <div class="guard-receipt__row">
                <span class="guard-receipt__label">Current Time</span>
                <span class="guard-receipt__value">{{ now()->format('M d, Y · H:i:s') }}</span>
            </div>
            <div class="guard-receipt__row">
                <span class="guard-receipt__label">Grace Period</span>
                <span class="guard-receipt__value">{{ $ticket->grace_period_at_entry }} minutes</span>
            </div>
            <div class="guard-receipt__row">
                <span class="guard-receipt__label">Hourly Rate</span>
                <span class="guard-receipt__value">₱{{ number_format($ticket->rate_at_entry, 2) }}</span>
            </div>
            @if($ticket->is_delivery)
            <div class="guard-receipt__row">
                <span class="guard-receipt__label">Parking Type</span>
                <span class="guard-badge guard-badge--free">Free Parking</span>
            </div>
            @endif
        </div>
        <div class="guard-receipt__total">
            <span class="guard-receipt__total-label">Total Fee</span>
            <span class="guard-receipt__total-value">₱{{ number_format($fee, 2) }}</span>
        </div>
    </div>

    <div class="guard-panel">
        <div class="guard-panel__head">
            <h2 class="guard-panel__title">Payment Method</h2>
            <p class="guard-panel__subtitle">Select how the customer will pay before releasing the gate</p>
        </div>

        <form action="{{ route('guard.exit.payment') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
            <input type="hidden" name="amount" value="{{ $fee }}">

            <div class="form-group mb-0">
                <label class="form-label" for="payment_method">Payment Method</label>
                <div class="guard-input-wrap">
                    <i class="fas fa-wallet guard-input-icon" aria-hidden="true"></i>
                    <select name="payment_method" id="payment_method" required class="form-control">
                        <option value="cash">Cash</option>
                        <option value="card">Card (Payment Gateway)</option>
                        <option value="e_wallet">E-Wallet Mobile Payment</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2">
                <a href="{{ route('guard.exit.form') }}" class="btn-secondary flex-1 text-center inline-flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Cancel
                </a>
                <button type="submit" class="btn-guard-pay">
                    <i class="fas fa-door-open"></i>
                    Confirm Payment &amp; Open Gate
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
