@extends('layouts.guard-sidebar')

@section('title', 'Vehicle Entry')
@section('panel_class', 'guard-panel-shell')
@section('breadcrumbs')
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <a href="{{ route('guard.entry.form') }}" class="text-gray-800 font-medium hover:text-blue-600">Vehicle Entry</a>
@endsection

@section('content')
<div class="page-stack guard-workflow">
    <header class="guard-page-header text-center sm:text-left">
        <h1>Vehicle Entry</h1>
        <p>Register incoming vehicles and issue a parking ticket with QR code.</p>
    </header>

    @if(session('ticket_data'))
        @php
            $ticket = session('ticket_data')['ticket'];
            $slot = session('ticket_data')['slot'];
        @endphp
        <div class="guard-ticket-success">
            <div class="text-center space-y-4">
                <div class="inline-flex items-center gap-2 text-emerald-700 font-bold text-lg">
                    <i class="fas fa-circle-check"></i>
                    Ticket Issued Successfully
                </div>

                <div class="bg-white p-4 rounded-xl inline-block shadow-sm border border-gray-100">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($ticket->qr_code) }}"
                         alt="QR Code"
                         id="qr-image"
                         class="mx-auto">
                </div>

                <div class="guard-receipt guard-receipt--compact text-left max-w-md mx-auto">
                    <div class="guard-receipt__body py-3">
                        <div class="guard-receipt__row">
                            <span class="guard-receipt__label">Ticket ID</span>
                            <span class="guard-receipt__value font-mono text-xs break-all max-w-[55%]">{{ $ticket->qr_code }}</span>
                        </div>
                        <div class="guard-receipt__row">
                            <span class="guard-receipt__label">Parking Slot</span>
                            <span class="guard-receipt__value">{{ $slot->slot_number }} ({{ ucfirst($slot->type) }})</span>
                        </div>
                        <div class="guard-receipt__row">
                            <span class="guard-receipt__label">Plate Number</span>
                            <span class="guard-receipt__value">{{ $ticket->plate_number }}</span>
                        </div>
                        <div class="guard-receipt__row">
                            <span class="guard-receipt__label">Entry Time</span>
                            <span class="guard-receipt__value">{{ now()->format('M d, Y · H:i:s') }}</span>
                        </div>
                        <div class="guard-receipt__row">
                            <span class="guard-receipt__label">Hourly Rate</span>
                            <span class="guard-receipt__value">₱{{ number_format($ticket->rate_at_entry, 2) }}</span>
                        </div>
                        <div class="guard-receipt__row">
                            <span class="guard-receipt__label">Grace Period</span>
                            <span class="guard-receipt__value">{{ $ticket->grace_period_at_entry }} min</span>
                        </div>
                        @if($ticket->is_delivery)
                        <div class="guard-receipt__row">
                            <span class="guard-receipt__label">Delivery</span>
                            <span class="guard-badge guard-badge--free">Free Parking</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <button type="button" onclick="downloadQRCode()" class="btn-secondary flex-1">
                        <i class="fas fa-download mr-1"></i> Download QR (PNG)
                    </button>
                    <button type="button" onclick="downloadTicketPDF()" class="btn-primary flex-1">
                        <i class="fas fa-file-pdf mr-1"></i> Download Ticket (PDF)
                    </button>
                </div>

                <div class="pt-3 border-t border-blue-100 text-left max-w-md mx-auto">
                    <p class="caption mb-2">QR code value (for exit testing)</p>
                    <code class="block bg-white border border-gray-200 p-3 rounded-lg text-xs break-all select-all font-mono">{{ $ticket->qr_code }}</code>
                </div>
            </div>
        </div>

        <script>
            const qrCodeValue = @json($ticket->qr_code);
            const ticketId = @json($ticket->id);
            const slotNumber = @json($slot->slot_number);
            const slotType = @json(ucfirst($slot->type));
            const plateNumber = @json($ticket->plate_number);
            const entryDate = @json(now()->format('F d, Y'));
            const entryTime = @json(now()->format('h:i:s A'));
            const hourlyRate = @json(number_format($ticket->rate_at_entry, 2));
            const gracePeriod = @json($ticket->grace_period_at_entry);
            const isDelivery = @json((bool) $ticket->is_delivery);
            const mallName = @json(App\Models\Setting::get('mall_name', 'MKKK Mall'));
            const mallAddress = @json(App\Models\Setting::get('mall_address', 'MacArthur Highway, Davao City'));

            function downloadQRCode() {
                const qrImageUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrCodeValue)}`;
                fetch(qrImageUrl)
                    .then(response => response.blob())
                    .then(blob => {
                        const link = document.createElement('a');
                        const url = URL.createObjectURL(blob);
                        link.href = url;
                        link.download = `parking_qrcode_${ticketId}.png`;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        URL.revokeObjectURL(url);
                    })
                    .catch(() => alert('Failed to download QR code. Please try again.'));
            }

            function downloadTicketPDF() {
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Parking Ticket - MKKK Mall</title>
                        <meta charset="utf-8">
                        <style>
                            * { margin: 0; padding: 0; box-sizing: border-box; }
                            body { font-family: 'Segoe UI', Arial, sans-serif; background: #e5e7eb; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
                            .ticket { max-width: 450px; background: white; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; }
                            .header { background: #2d3092; color: white; padding: 20px; text-align: center; }
                            .header h1 { font-size: 24px; margin-bottom: 5px; }
                            .qr-section { text-align: center; padding: 20px; border-bottom: 1px dashed #e5e7eb; }
                            .details { padding: 20px; }
                            .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
                            .detail-label { font-weight: 600; color: #4b5563; }
                            .detail-value { color: #1f2937; font-family: monospace; }
                            .delivery-badge { background: #dcfce7; color: #166534; padding: 8px; text-align: center; border-radius: 8px; margin-top: 15px; font-weight: 600; }
                            .footer { background: #f9fafb; padding: 15px; text-align: center; font-size: 10px; color: #6b7280; }
                            .print-btn { display: block; width: calc(100% - 40px); margin: 20px; padding: 12px; background: #2d3092; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; }
                            @media print { body { background: white; padding: 0; } .print-btn { display: none; } .ticket { box-shadow: none; } }
                        </style>
                    </head>
                    <body>
                        <div class="ticket">
                            <div class="header"><h1>${mallName}</h1><p>Parking Ticket</p></div>
                            <div class="qr-section">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(qrCodeValue)}" alt="QR Code" style="margin: 0 auto;">
                                <p style="font-size: 10px; color: #6b7280; margin-top: 8px;">Scan at exit gate</p>
                            </div>
                            <div class="details">
                                <div class="detail-row"><span class="detail-label">Ticket ID:</span><span class="detail-value" style="font-size: 11px;">${qrCodeValue}</span></div>
                                <div class="detail-row"><span class="detail-label">Plate Number:</span><span class="detail-value">${plateNumber}</span></div>
                                <div class="detail-row"><span class="detail-label">Parking Slot:</span><span class="detail-value">${slotNumber} (${slotType})</span></div>
                                <div class="detail-row"><span class="detail-label">Entry Date:</span><span class="detail-value">${entryDate}</span></div>
                                <div class="detail-row"><span class="detail-label">Entry Time:</span><span class="detail-value">${entryTime}</span></div>
                                <div class="detail-row"><span class="detail-label">Grace Period:</span><span class="detail-value">${gracePeriod} minutes</span></div>
                                <div class="detail-row"><span class="detail-label">Hourly Rate:</span><span class="detail-value">₱${hourlyRate}</span></div>
                                ${isDelivery ? '<div class="delivery-badge">DELIVERY VEHICLE — FREE PARKING</div>' : ''}
                            </div>
                            <div class="footer"><p>${mallAddress}</p><p style="margin-top: 5px;">Present this ticket at exit gate</p></div>
                            <button class="print-btn" onclick="window.print()">Print Ticket</button>
                        </div>
                        <script>setTimeout(() => { window.print(); setTimeout(() => window.close(), 1000); }, 500);<\/script>
                    </body>
                    </html>
                `);
                printWindow.document.close();
            }
        </script>
    @endif

    @if($errors->any())
        <div class="alert alert-error mb-4">
            <i class="fas fa-exclamation-circle alert-icon"></i>
            <div class="alert-content">{{ $errors->first() }}</div>
        </div>
    @endif

    <div class="guard-panel">
        <div class="guard-panel__head">
            <h2 class="guard-panel__title">Issue Parking Ticket</h2>
            <p class="guard-panel__subtitle">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-square-parking text-emerald-600"></i>
                    <strong>{{ $availableSlots->count() }}</strong> slots available
                </span>
            </p>
        </div>

        <form action="{{ route('guard.entry.process') }}" method="POST" class="space-y-5">
            @csrf

            <div class="form-group mb-0">
                <label class="form-label" for="plate_number">Plate Number</label>
                <div class="guard-input-wrap">
                    <i class="fas fa-car guard-input-icon" aria-hidden="true"></i>
                    <input type="text" name="plate_number" id="plate_number" required
                        class="form-control uppercase"
                        value="{{ old('plate_number') }}"
                        placeholder="e.g. ABC 1234"
                        autocomplete="off">
                </div>
            </div>

            <label class="flex items-start gap-3 cursor-pointer p-4 rounded-lg border border-gray-200 bg-gray-50 hover:border-emerald-300 transition-colors">
                <input type="checkbox" name="is_delivery" value="1" class="mt-1 w-4 h-4 text-emerald-600 rounded"
                    {{ old('is_delivery') ? 'checked' : '' }}>
                <span>
                    <span class="font-semibold text-gray-900 block">Delivery Vehicle</span>
                    <span class="text-sm text-gray-600">Mark for free parking — no hourly fee at exit</span>
                </span>
            </label>

            <div class="alert alert-warning mb-0">
                <i class="fas fa-info-circle alert-icon"></i>
                <div class="alert-content text-sm">
                    Gate will open after ticket is issued. Ensure plate number matches the vehicle.
                </div>
            </div>

            <button type="submit" class="btn-guard-entry">
                <i class="fas fa-ticket"></i>
                Issue Ticket &amp; Open Gate
            </button>
        </form>
    </div>
</div>
@endsection
