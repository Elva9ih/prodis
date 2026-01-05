@extends('layouts.admin')

@section('title', __('admin.map.title'))
@section('page-title', __('admin.map.title'))

@section('content')
<div class="card">
    <div class="card-header bg-transparent">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="row g-2">
                    <div class="col-md-3">
                        <select id="filterType" class="form-select form-select-sm">
                            <option value="">{{ __('admin.establishments.all_types') }}</option>
                            <option value="client">{{ __('admin.establishments.client') }}</option>
                            <option value="fournisseur">{{ __('admin.establishments.fournisseur') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterAgent" class="form-select form-select-sm">
                            <option value="">{{ __('admin.establishments.all_agents') }}</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="date-input-wrapper">
                            <input type="datetime-local" id="filterDateFrom" class="form-control form-control-sm date-input" title="{{ __('admin.common.from') }}">
                            <span class="date-placeholder">{{ __('admin.common.from') }}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="date-input-wrapper">
                            <input type="datetime-local" id="filterDateTo" class="form-control form-control-sm date-input" title="{{ __('admin.common.to') }}">
                            <span class="date-placeholder">{{ __('admin.common.to') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group btn-group-sm {{ $isRtl ? 'ms-2' : 'me-2' }}" role="group">
                    <button type="button" class="btn btn-outline-secondary active" id="btnLeaflet" title="OpenStreetMap">
                        <i class="bi bi-map"></i> OSM
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btnGoogle" title="Google Maps">
                        <i class="bi bi-google"></i> Google
                    </button>
                </div>
                <span id="markerCount" class="badge bg-secondary">0 {{ __('admin.map.markers') }}</span>
                <button id="btnRefresh" class="btn btn-sm btn-primary {{ $isRtl ? 'me-2' : 'ms-2' }}">
                    <i class="bi bi-arrow-clockwise"></i> {{ __('admin.map.refresh') }}
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="leafletMap" style="height: 600px;"></div>
        <div id="googleMap" style="height: 600px; display: none;"></div>
    </div>
</div>

<!-- Legend -->
<div class="card mt-3">
    <div class="card-body py-2">
        <div class="d-flex justify-content-center gap-4 align-items-center">
            <span class="d-flex align-items-center gap-2">
                <span class="marker-legend marker-client"><i class="bi bi-wrench"></i></span>
                {{ __('admin.establishments.client') }}
            </span>
            <span class="d-flex align-items-center gap-2">
                <span class="marker-legend marker-fournisseur"><i class="bi bi-shop"></i></span>
                {{ __('admin.establishments.fournisseur') }}
            </span>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Marker with label on top */
.marker-with-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
}

.marker-label-top {
    background: #2c3e50;
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    margin-bottom: 0;
    position: relative;
}

.marker-label-top::after {
    content: '';
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-top: 6px solid #2c3e50;
}

.marker-label-top.client {
    background: #2980b9;
}
.marker-label-top.client::after {
    border-top-color: #2980b9;
}

.marker-label-top.fournisseur {
    background: #1e8449;
}
.marker-label-top.fournisseur::after {
    border-top-color: #1e8449;
}

.custom-marker {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 13px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    border: 2px solid white;
    cursor: pointer;
    transition: transform 0.2s ease;
    margin-top: 4px;
}

.marker-with-label:hover .custom-marker {
    transform: scale(1.1);
}

.marker-client {
    background: #3498db;
}

.marker-fournisseur {
    background: #27ae60;
}

/* Legend markers */
.marker-legend {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

/* Popup styles */
.leaflet-popup-content-wrapper {
    background: white;
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    padding: 0;
}

.leaflet-popup-content {
    margin: 0;
    min-width: 220px;
}

.leaflet-popup-tip {
    background: white;
}

.popup-content {
    min-width: 200px;
    padding: 12px;
}

.popup-content .popup-image {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 6px;
    margin-bottom: 10px;
}

.popup-content .popup-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.popup-content .popup-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.popup-content .popup-icon.client {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
}

.popup-content .popup-icon.fournisseur {
    background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
}

.popup-content .popup-title {
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
    margin-bottom: 2px;
}

.popup-content .popup-type {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 500;
}

.popup-content .popup-type.client {
    background: #ebf5fb;
    color: #2980b9;
}

.popup-content .popup-type.fournisseur {
    background: #e8f8f5;
    color: #1e8449;
}

.popup-content .popup-details {
    margin-bottom: 10px;
}

.popup-content .popup-info {
    color: #555;
    font-size: 12px;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.popup-content .popup-info i {
    color: #95a5a6;
    width: 14px;
}

.popup-content .popup-btn {
    display: block;
    width: 100%;
    padding: 8px 12px;
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
    text-align: center;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.2s;
}

.popup-content .popup-btn:hover {
    background: linear-gradient(135deg, #2980b9 0%, #1f6dad 100%);
    color: white;
}

/* Cluster styles */
.marker-cluster-small,
.marker-cluster-medium,
.marker-cluster-large {
    background: rgba(52, 152, 219, 0.2);
}

.marker-cluster-small div,
.marker-cluster-medium div,
.marker-cluster-large div {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
    font-weight: 600;
}

/* Google Maps InfoWindow styles */
.gm-style .gm-style-iw-c {
    padding: 0 !important;
    border-radius: 8px !important;
}
.gm-style .gm-style-iw-d {
    overflow: hidden !important;
}
/* Google Maps close button */
.gm-style .gm-ui-hover-effect {
    top: 4px !important;
    right: 4px !important;
    width: 28px !important;
    height: 28px !important;
    background: white !important;
    border-radius: 50% !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3) !important;
    opacity: 1 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
.gm-style .gm-ui-hover-effect > span {
    background-color: #333 !important;
    width: 14px !important;
    height: 14px !important;
    margin: 0 !important;
}

/* Leaflet popup close button */
.leaflet-popup-close-button {
    top: 8px !important;
    right: 8px !important;
    width: 24px !important;
    height: 24px !important;
    background: white !important;
    border-radius: 50% !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3) !important;
    color: #333 !important;
    font-size: 18px !important;
    font-weight: bold !important;
    line-height: 22px !important;
    text-align: center !important;
    z-index: 1000 !important;
}

/* Date input with placeholder */
.date-input-wrapper {
    position: relative;
}
.date-input-wrapper .date-placeholder {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    font-size: 0.875rem;
    pointer-events: none;
    transition: opacity 0.2s;
}
.date-input-wrapper .date-input:valid + .date-placeholder,
.date-input-wrapper .date-input:focus + .date-placeholder {
    opacity: 0;
}
.date-input-wrapper .date-input.has-value + .date-placeholder {
    display: none;
}
</style>
@endpush

@push('scripts')
<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key', '') }}&libraries=marker&callback=Function.prototype" async defer></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentMapType = 'leaflet';
    let leafletMap, googleMap;
    let leafletMarkers, googleMarkers = [];
    let markersData = [];

    // Initialize Leaflet map centered on Mauritania
    leafletMap = L.map('leafletMap').setView([18.0735, -15.9582], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(leafletMap);

    // Marker cluster group for Leaflet
    leafletMarkers = L.markerClusterGroup({
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        maxClusterRadius: 50
    });
    leafletMap.addLayer(leafletMarkers);

    // Translations
    const translations = {
        type: '{{ __('admin.establishments.type') }}',
        client: '{{ __('admin.establishments.client') }}',
        fournisseur: '{{ __('admin.establishments.fournisseur') }}',
        owner: '{{ __('admin.establishments.owner') }}',
        phone: '{{ __('admin.establishments.phone') }}',
        city: '{{ __('admin.establishments.city') }}',
        date: '{{ __('admin.establishments.date') }}',
        viewDetails: '{{ __('admin.common.view_details') }}'
    };

    // Create Leaflet custom icon with name label on top
    function createLeafletIcon(type, name) {
        const iconClass = type === 'client' ? 'bi-wrench' : 'bi-shop';
        const markerClass = type === 'client' ? 'marker-client' : 'marker-fournisseur';
        const shortName = name.length > 15 ? name.substring(0, 15) + '...' : name;

        return L.divIcon({
            html: `<div class="marker-with-label">
                <div class="marker-label-top ${type}">${shortName}</div>
                <div class="custom-marker ${markerClass}"><i class="bi ${iconClass}"></i></div>
            </div>`,
            className: 'custom-marker-wrapper',
            iconSize: [120, 50],
            iconAnchor: [60, 50],
            popupAnchor: [0, -50]
        });
    }

    // Create popup content
    function createPopupContent(m) {
        const typeLabel = m.type === 'client' ? translations.client : translations.fournisseur;
        const iconClass = m.type === 'client' ? 'bi-wrench' : 'bi-shop';
        const imageHtml = m.photo_url
            ? `<img src="${m.photo_url}" alt="" class="popup-image" onerror="this.style.display='none'">`
            : '';
        return `
            <div class="popup-content">
                ${imageHtml}
                <div class="popup-header">
                    <div class="popup-icon ${m.type}">
                        <i class="bi ${iconClass}"></i>
                    </div>
                    <div>
                        <div class="popup-title">${m.name}</div>
                        <span class="popup-type ${m.type}">${typeLabel}</span>
                    </div>
                </div>
                <div class="popup-details">
                    <div class="popup-info"><i class="bi bi-person"></i> ${m.owner_name}</div>
                    <div class="popup-info"><i class="bi bi-telephone"></i> ${m.phone}</div>
                    ${m.city ? `<div class="popup-info"><i class="bi bi-geo-alt"></i> ${m.city}</div>` : ''}
                    <div class="popup-info"><i class="bi bi-calendar"></i> ${m.created_at}</div>
                </div>
                <a href="/admin/establishments/${m.id}" class="popup-btn">
                    <i class="bi bi-eye"></i> ${translations.viewDetails}
                </a>
            </div>
        `;
    }

    // Initialize Google Map
    function initGoogleMap() {
        if (googleMap) return;

        googleMap = new google.maps.Map(document.getElementById('googleMap'), {
            center: { lat: 18.0735, lng: -15.9582 },
            zoom: 6,
            mapTypeId: 'roadmap',
            mapTypeControl: true,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                position: google.maps.ControlPosition.TOP_RIGHT
            }
        });

        // Load markers on Google Map
        updateGoogleMarkers();
    }

    // Update Leaflet markers
    function updateLeafletMarkers() {
        leafletMarkers.clearLayers();

        markersData.forEach(m => {
            const marker = L.marker([m.lat, m.lng], {
                icon: createLeafletIcon(m.type, m.name)
            });

            marker.bindPopup(createPopupContent(m), {
                closeButton: true,
                autoClose: true,
                className: 'establishment-popup'
            });

            marker.on('mouseover', function() {
                this.openPopup();
            });

            leafletMarkers.addLayer(marker);
        });

        if (markersData.length > 0) {
            leafletMap.fitBounds(leafletMarkers.getBounds(), { padding: [50, 50] });
        }
    }

    // Custom overlay class for Google Maps HTML markers
    class CustomMarkerOverlay extends google.maps.OverlayView {
        constructor(position, type, name, content, infoWindow) {
            super();
            this.position = position;
            this.type = type;
            this.name = name;
            this.content = content;
            this.infoWindow = infoWindow;
            this.div = null;
        }

        onAdd() {
            this.div = document.createElement('div');
            this.div.style.position = 'absolute';
            this.div.style.cursor = 'pointer';
            this.div.style.transform = 'translate(-50%, -100%)';

            const iconClass = this.type === 'client' ? 'bi-wrench' : 'bi-shop';
            const markerClass = this.type === 'client' ? 'marker-client' : 'marker-fournisseur';
            const shortName = this.name.length > 15 ? this.name.substring(0, 15) + '...' : this.name;

            this.div.innerHTML = `
                <div class="marker-with-label">
                    <div class="marker-label-top ${this.type}">${shortName}</div>
                    <div class="custom-marker ${markerClass}">
                        <i class="bi ${iconClass}"></i>
                    </div>
                </div>
            `;

            const self = this;
            this.div.addEventListener('click', function() {
                self.infoWindow.setContent(self.content);
                self.infoWindow.setPosition(self.position);
                self.infoWindow.open(self.getMap());
            });

            this.div.addEventListener('mouseover', function() {
                self.infoWindow.setContent(self.content);
                self.infoWindow.setPosition(self.position);
                self.infoWindow.open(self.getMap());
            });

            const panes = this.getPanes();
            panes.overlayMouseTarget.appendChild(this.div);
        }

        draw() {
            const overlayProjection = this.getProjection();
            const pos = overlayProjection.fromLatLngToDivPixel(this.position);
            if (this.div) {
                this.div.style.left = pos.x + 'px';
                this.div.style.top = pos.y + 'px';
            }
        }

        onRemove() {
            if (this.div) {
                this.div.parentNode.removeChild(this.div);
                this.div = null;
            }
        }

        setMap(map) {
            super.setMap(map);
        }
    }

    // Update Google markers
    function updateGoogleMarkers() {
        if (!googleMap) return;

        // Clear existing markers
        googleMarkers.forEach(marker => marker.setMap(null));
        googleMarkers = [];

        const bounds = new google.maps.LatLngBounds();
        let infoWindow = new google.maps.InfoWindow();

        markersData.forEach(m => {
            const position = new google.maps.LatLng(m.lat, m.lng);

            // Create custom HTML marker overlay
            const overlay = new CustomMarkerOverlay(
                position,
                m.type,
                m.name,
                createPopupContent(m),
                infoWindow
            );
            overlay.setMap(googleMap);

            googleMarkers.push(overlay);
            bounds.extend(position);
        });

        if (markersData.length > 0) {
            googleMap.fitBounds(bounds);
        }
    }

    // Load markers
    function loadMarkers() {
        const params = new URLSearchParams({
            type: document.getElementById('filterType').value,
            agent_id: document.getElementById('filterAgent').value,
            date_from: document.getElementById('filterDateFrom').value,
            date_to: document.getElementById('filterDateTo').value
        });

        fetch('{{ route('admin.map.data') }}?' + params.toString())
            .then(response => response.json())
            .then(data => {
                markersData = data.markers;
                document.getElementById('markerCount').textContent = data.count + ' {{ __('admin.map.markers') }}';

                if (currentMapType === 'leaflet') {
                    updateLeafletMarkers();
                } else {
                    updateGoogleMarkers();
                }
            })
            .catch(error => {
                console.error('Error loading markers:', error);
            });
    }

    // Switch map type
    function switchToLeaflet() {
        currentMapType = 'leaflet';
        document.getElementById('leafletMap').style.display = 'block';
        document.getElementById('googleMap').style.display = 'none';
        document.getElementById('btnLeaflet').classList.add('active');
        document.getElementById('btnGoogle').classList.remove('active');

        setTimeout(() => {
            leafletMap.invalidateSize();
            updateLeafletMarkers();
        }, 100);
    }

    function switchToGoogle() {
        currentMapType = 'google';
        document.getElementById('leafletMap').style.display = 'none';
        document.getElementById('googleMap').style.display = 'block';
        document.getElementById('btnLeaflet').classList.remove('active');
        document.getElementById('btnGoogle').classList.add('active');

        initGoogleMap();
        setTimeout(() => {
            google.maps.event.trigger(googleMap, 'resize');
            updateGoogleMarkers();
        }, 100);
    }

    // Initial load
    loadMarkers();

    // Event listeners
    document.getElementById('filterType').addEventListener('change', loadMarkers);
    document.getElementById('filterAgent').addEventListener('change', loadMarkers);

    // Date inputs with placeholder handling
    const dateFrom = document.getElementById('filterDateFrom');
    const dateTo = document.getElementById('filterDateTo');

    dateFrom.addEventListener('change', function() {
        this.classList.toggle('has-value', this.value !== '');
        loadMarkers();
    });
    dateTo.addEventListener('change', function() {
        this.classList.toggle('has-value', this.value !== '');
        loadMarkers();
    });

    document.getElementById('btnRefresh').addEventListener('click', loadMarkers);
    document.getElementById('btnLeaflet').addEventListener('click', switchToLeaflet);
    document.getElementById('btnGoogle').addEventListener('click', switchToGoogle);
});
</script>
@endpush
