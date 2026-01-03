@extends('layouts.admin')

@section('title', __('admin.mobile_connection.title'))
@section('page-title', __('admin.mobile_connection.title'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-phone"></i> {{ __('admin.mobile_connection.title') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- QR Code Section -->
                    <div class="col-md-6 text-center mb-4 mb-md-0">
                        <h6 class="text-muted mb-3">{{ __('admin.mobile_connection.scan_qr') }}</h6>
                        <div class="qr-container p-4 bg-white rounded shadow-sm d-inline-block">
                            <div id="qrcode"></div>
                        </div>
                        <p class="text-muted small mt-3">
                            {{ __('admin.mobile_connection.scan_instruction') }}
                        </p>
                    </div>

                    <!-- Server Info Section -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">{{ __('admin.mobile_connection.server_info') }}</h6>

                        <div class="mb-3">
                            <label class="form-label small text-muted">{{ __('admin.mobile_connection.server_ip') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $serverIp }}" readonly id="serverIp">
                                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('serverIp')">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted">{{ __('admin.mobile_connection.server_port') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $serverPort }}" readonly id="serverPort">
                                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('serverPort')">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-muted">{{ __('admin.mobile_connection.api_url') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $apiUrl }}" readonly id="apiUrl">
                                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('apiUrl')">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-info small">
                            <i class="bi bi-info-circle"></i>
                            {{ __('admin.mobile_connection.manual_instruction') }}
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Instructions -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-muted mb-3">{{ __('admin.mobile_connection.instructions_title') }}</h6>
                        <ol class="mb-0">
                            <li class="mb-2">{{ __('admin.mobile_connection.step1') }}</li>
                            <li class="mb-2">{{ __('admin.mobile_connection.step2') }}</li>
                            <li class="mb-2">{{ __('admin.mobile_connection.step3') }}</li>
                            <li>{{ __('admin.mobile_connection.step4') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Network Tips -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightbulb"></i> {{ __('admin.mobile_connection.tips_title') }}
                </h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li class="mb-2">{{ __('admin.mobile_connection.tip1') }}</li>
                    <li class="mb-2">{{ __('admin.mobile_connection.tip2') }}</li>
                    <li>{{ __('admin.mobile_connection.tip3') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .qr-container {
        border: 2px dashed #dee2e6;
    }
    #qrcode {
        width: 200px;
        height: 200px;
        margin: 0 auto;
    }
    #qrcode img {
        width: 100% !important;
        height: 100% !important;
    }
</style>
@endpush

@push('scripts')
<!-- QRCode.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Generate QR Code
        const qrData = @json($qrData);

        new QRCode(document.getElementById('qrcode'), {
            text: qrData,
            width: 200,
            height: 200,
            colorDark: '#2c3e50',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    });

    function copyToClipboard(elementId) {
        const input = document.getElementById(elementId);
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(function() {
            // Show feedback
            const btn = input.nextElementSibling;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check"></i>';
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-success');
            setTimeout(function() {
                btn.innerHTML = originalHtml;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-secondary');
            }, 1500);
        });
    }
</script>
@endpush
