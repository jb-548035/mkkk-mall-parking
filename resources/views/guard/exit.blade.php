@extends('layouts.guard-sidebar')

@section('title', 'Vehicle Exit')
@section('panel_class', 'guard-panel-shell')
@section('breadcrumbs')
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <a href="{{ route('guard.exit.form') }}" class="text-gray-800 font-medium hover:text-blue-600">Vehicle Exit</a>
@endsection

@section('content')
<div class="page-stack guard-workflow">
    <header class="guard-page-header text-center sm:text-left">
        <h1>Vehicle Exit</h1>
        <p>Scan or enter ticket details to calculate fees and process checkout.</p>
    </header>

    @if($errors->any())
        <div class="alert alert-error mb-4">
            <i class="fas fa-exclamation-circle alert-icon"></i>
            <div class="alert-content">{{ $errors->first() }}</div>
        </div>
    @endif

    <div class="guard-panel">
        <div class="guard-panel__head">
            <h2 class="guard-panel__title">Find Active Ticket</h2>
            <p class="guard-panel__subtitle">Choose how to identify the vehicle ticket</p>
        </div>

        <div class="guard-tabs" role="tablist">
            <button type="button" role="tab" id="tab-scan" class="guard-tab is-active" onclick="switchTab('scan')" aria-selected="true">
                <i class="fas fa-qrcode"></i> Scan QR Code
            </button>
            <button type="button" role="tab" id="tab-upload" class="guard-tab" onclick="switchTab('upload')" aria-selected="false">
                <i class="fas fa-upload"></i> Upload QR Image
            </button>
            <button type="button" role="tab" id="tab-manual" class="guard-tab" onclick="switchTab('manual')" aria-selected="false">
                <i class="fas fa-keyboard"></i> Manual Entry
            </button>
        </div>

        <div id="tab-scan-content" class="tab-content" role="tabpanel">
            <form action="{{ route('guard.exit.scan') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="qr_code">QR Code Value</label>
                    <div class="guard-input-wrap guard-input-wrap--mono">
                        <i class="fas fa-barcode guard-input-icon" aria-hidden="true"></i>
                        <input type="text" name="qr_code" id="qr_code" required
                            class="form-control"
                            placeholder="Paste QR code from ticket"
                            autocomplete="off">
                    </div>
                    <p class="caption mt-2">Paste the full QR string from the entry ticket or customer phone.</p>
                </div>
                <button type="submit" class="btn-guard-exit">
                    <i class="fas fa-calculator"></i>
                    Find Ticket &amp; Calculate Fee
                </button>
            </form>
        </div>

        <div id="tab-upload-content" class="tab-content hidden" role="tabpanel">
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Upload QR Code Image</label>
                    <div class="guard-upload-zone"
                         onclick="document.getElementById('qr_image').click()"
                         onkeydown="if(event.key==='Enter')document.getElementById('qr_image').click()"
                         tabindex="0"
                         role="button">
                        <input type="file" id="qr_image" name="qr_image" accept="image/png,image/jpeg,image/jpg" class="hidden" onchange="previewImage(this)">
                        <i class="fas fa-cloud-arrow-up text-3xl text-gray-400 mb-3"></i>
                        <p class="font-medium text-gray-700">Click or drag to upload</p>
                        <p class="caption mt-1">PNG or JPG, up to 5MB</p>
                    </div>
                    <div id="image-preview" class="mt-4 hidden text-center">
                        <img id="preview-img" src="#" alt="QR Preview" class="mx-auto max-w-[160px] border rounded-lg p-2 bg-white shadow-sm">
                    </div>
                </div>

                <div id="upload-status" class="mb-4 hidden">
                    <div class="alert alert-warning mb-0">
                        <svg class="animate-spin h-5 w-5 text-amber-600 mr-2 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="upload-message">Processing QR code…</span>
                    </div>
                </div>

                <button type="button" onclick="uploadQRCode()" class="btn-guard-exit">
                    <i class="fas fa-magnifying-glass"></i>
                    Upload &amp; Scan QR
                </button>
            </form>
        </div>

        <div id="tab-manual-content" class="tab-content hidden" role="tabpanel">
            <div class="alert alert-warning mb-4">
                <i class="fas fa-triangle-exclamation alert-icon"></i>
                <div class="alert-content">
                    <strong>Lost ticket?</strong> Search by plate number if the customer cannot present their QR code.
                </div>
            </div>
            <form action="{{ route('guard.exit.search') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="plate_number_exit">Plate Number</label>
                    <div class="guard-input-wrap">
                        <i class="fas fa-car guard-input-icon" aria-hidden="true"></i>
                        <input type="text" name="plate_number" id="plate_number_exit" required
                            class="form-control uppercase"
                            placeholder="Enter plate number">
                    </div>
                </div>
                <button type="submit" class="btn-secondary w-full">
                    <i class="fas fa-search mr-1"></i> Search Active Ticket
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function switchTab(tab) {
        ['scan', 'upload', 'manual'].forEach(function (name) {
            const btn = document.getElementById('tab-' + name);
            const panel = document.getElementById('tab-' + name + '-content');
            const active = name === tab;
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-selected', active ? 'true' : 'false');
            panel.classList.toggle('hidden', !active);
        });
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('image-preview').classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function uploadQRCode() {
        const fileInput = document.getElementById('qr_image');
        const file = fileInput.files[0];

        if (!file) {
            alert('Please select a QR code image first.');
            return;
        }

        const formData = new FormData();
        formData.append('qr_image', file);
        formData.append('_token', document.querySelector('#uploadForm input[name="_token"]').value);

        const statusDiv = document.getElementById('upload-status');
        const messageSpan = document.getElementById('upload-message');

        statusDiv.classList.remove('hidden');
        messageSpan.textContent = 'Processing QR code image…';

        fetch(@json(route('guard.exit.upload-qr')), {
            method: 'POST',
            body: formData,
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success) {
                    messageSpan.textContent = 'QR code detected. Redirecting…';
                    setTimeout(function () {
                        window.location.href = data.redirect_url;
                    }, 1000);
                } else {
                    messageSpan.textContent = data.message || 'Could not read QR code.';
                    setTimeout(function () { statusDiv.classList.add('hidden'); }, 3000);
                }
            })
            .catch(function () {
                messageSpan.textContent = 'Error processing image. Try manual entry.';
                setTimeout(function () { statusDiv.classList.add('hidden'); }, 3000);
            });
    }
</script>
@endsection
