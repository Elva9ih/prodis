@extends('layouts.admin')

@section('title', __('admin.establishments.details'))
@section('page-title', __('admin.establishments.details'))

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ $establishment->name }}</h6>
                @if($establishment->type === 'client')
                    <span class="badge bg-primary">{{ __('admin.establishments.client') }}</span>
                @else
                    <span class="badge bg-success">{{ __('admin.establishments.fournisseur') }}</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">{{ __('admin.establishments.basic_info') }}</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">{{ __('admin.establishments.uuid') }}</th>
                                <td><code>{{ $establishment->uuid }}</code></td>
                            </tr>
                            <tr>
                                <th>{{ $establishment->type === 'client' ? __('admin.establishments.owner_name_client') : __('admin.establishments.owner_name_fournisseur') }}</th>
                                <td>{{ $establishment->owner_name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.establishments.phone') }}</th>
                                <td>
                                    @if($establishment->phones_json && count($establishment->phones_json) > 0)
                                        <ul class="mb-0">
                                            @foreach($establishment->phones_json as $phone)
                                                <li>{{ $phone['country']['dialCode'] ?? $phone['country'] }} {{ $phone['number'] }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        {{ $establishment->full_phone }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.establishments.whatsapp') }}</th>
                                <td>{{ $establishment->full_whatsapp ?? __('admin.common.na') }}</td>
                            </tr>
                            @if($establishment->city)
                            <tr>
                                <th>{{ __('admin.establishments.city') }}</th>
                                <td>{{ $establishment->city }}</td>
                            </tr>
                            @endif
                            @if($establishment->remarks)
                            <tr>
                                <th>{{ __('admin.establishments.remarks') }}</th>
                                <td>{{ $establishment->remarks }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">{{ __('admin.establishments.registration_info') }}</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">{{ __('admin.establishments.agent') }}</th>
                                <td>
                                    @if($establishment->agent)
                                        <a href="{{ route('admin.agents.show', $establishment->agent) }}">
                                            {{ $establishment->agent->name }}
                                        </a>
                                    @else
                                        {{ __('admin.common.na') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.establishments.synced_at') }}</th>
                                <td>{{ $establishment->synced_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.establishments.created_at') }}</th>
                                <td>{{ $establishment->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($establishment->photos_json && count($establishment->photos_json) > 0)
                    <hr>
                    <h6 class="text-muted mb-3">{{ __('admin.establishments.photo') }}</h6>
                    <div class="row">
                        @foreach($establishment->photos_json as $photo)
                            <div class="col-md-6 mb-4">
                                <div class="photo-card">
                                    <a href="{{ asset('storage/establishments/' . $photo['filename']) }}" target="_blank" class="photo-link">
                                        <img src="{{ asset('storage/establishments/' . $photo['filename']) }}" alt="{{ __('admin.establishments.photo') }}" class="photo-image">
                                    </a>
                                    @if(!empty($photo['label']))
                                        <div class="photo-label-container">
                                            <i class="bi bi-tag-fill me-1"></i>
                                            <span class="photo-label-text">{{ $photo['label'] }}</span>
                                        </div>
                                    @else
                                        <div class="photo-label-container photo-label-empty">
                                            <i class="bi bi-image me-1"></i>
                                            <span class="photo-label-text">{{ __('admin.establishments.photo') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif($establishment->photo)
                    <hr>
                    <h6 class="text-muted mb-3">{{ __('admin.establishments.photo') }}</h6>
                    <div class="text-center">
                        <a href="{{ $establishment->photo_url }}" target="_blank">
                            <img src="{{ $establishment->photo_url }}" alt="{{ __('admin.establishments.photo') }}" class="img-fluid rounded" style="max-height: 300px;">
                        </a>
                    </div>
                @endif

                @if($establishment->answers->count() > 0)
                    <hr>
                    <h6 class="text-muted mb-3">{{ __('admin.establishments.survey_answers') }}</h6>
                    <div class="row">
                        @foreach($establishment->answers->groupBy('question_code') as $questionCode => $answers)
                            <div class="col-md-6 mb-3">
                                <strong>{{ __('admin.questions.' . $questionCode, [], app()->getLocale()) ?: ucwords(str_replace('_', ' ', $questionCode)) }}</strong>
                                <ul class="mb-0">
                                    @foreach($answers as $answer)
                                        <li>{{ __('admin.options.' . $answer->answer_code, [], app()->getLocale()) ?: ucwords(str_replace('_', ' ', $answer->answer_code)) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- QR Code Card -->
        <div class="card mb-4">
            <div class="card-header bg-transparent">
                <h6 class="mb-0"><i class="bi bi-qr-code"></i> {{ __('admin.establishments.qrcode') }}</h6>
            </div>
            <div class="card-body text-center">
                <div id="qrcode"></div>
                <div class="mt-2">
                    <small class="text-muted">{{ __('admin.establishments.qrcode') }}: {{ $establishment->barcode }}</small>
                </div>
            </div>
        </div>

        <!-- Location Card -->
        <div class="card mb-4">
            <div class="card-header bg-transparent">
                <h6 class="mb-0"><i class="bi bi-geo-alt"></i> {{ __('admin.establishments.location') }}</h6>
            </div>
            <div class="card-body">
                <div id="establishmentMap" style="height: 250px; border-radius: 0.5rem;"></div>
                <div class="mt-3">
                    <small class="text-muted d-block">
                        <strong>{{ __('admin.establishments.coordinates') }}:</strong> {{ $establishment->latitude }}, {{ $establishment->longitude }}
                    </small>
                    @if($establishment->location_accuracy)
                        <small class="text-muted d-block">
                            <strong>{{ __('admin.establishments.accuracy') }}:</strong> {{ $establishment->location_accuracy }}m
                        </small>
                    @endif
                    <small class="text-muted d-block">
                        <strong>{{ __('admin.establishments.captured') }}:</strong> {{ $establishment->captured_at->format('Y-m-d H:i:s') }}
                    </small>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.establishments.index') }}" class="btn btn-secondary w-100">
            <i class="bi bi-arrow-{{ $isRtl ? 'right' : 'left' }}"></i> {{ __('admin.common.back_to_list') }}
        </a>
    </div>
</div>
@endsection

@push('scripts')
<!-- QRCode.js Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate QR code
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $establishment->barcode }}",
        width: 200,
        height: 200,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });

    // Initialize map
    const lat = {{ $establishment->latitude }};
    const lng = {{ $establishment->longitude }};
    const type = '{{ $establishment->type }}';

    const map = L.map('establishmentMap').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const iconClass = type === 'client' ? 'bi-wrench' : 'bi-shop';
    const markerClass = type === 'client' ? 'marker-client' : 'marker-fournisseur';

    const icon = L.divIcon({
        html: `<div class="custom-marker ${markerClass}"><i class="bi ${iconClass}"></i></div>`,
        className: 'custom-marker-wrapper',
        iconSize: [36, 36],
        iconAnchor: [18, 18]
    });

    L.marker([lat, lng], { icon: icon }).addTo(map);
});
</script>
<style>
.custom-marker-wrapper {
    background: transparent !important;
    border: none !important;
}

.custom-marker {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.3);
    border: 3px solid white;
}

.marker-client {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
}

.marker-fournisseur {
    background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
}

/* Photo card styles */
.photo-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.photo-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.photo-link {
    display: block;
    overflow: hidden;
    background: #f8f9fa;
    flex: 1;
}

.photo-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.photo-link:hover .photo-image {
    transform: scale(1.05);
}

.photo-label-container {
    padding: 12px 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    border-top: 2px solid rgba(255,255,255,0.2);
}

.photo-label-container.photo-label-empty {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    font-style: italic;
    opacity: 0.8;
}

.photo-label-container i {
    font-size: 14px;
    opacity: 0.9;
}

.photo-label-text {
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
@endpush
