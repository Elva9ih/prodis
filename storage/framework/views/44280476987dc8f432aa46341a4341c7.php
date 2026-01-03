<?php $__env->startSection('title', __('admin.map.title')); ?>
<?php $__env->startSection('page-title', __('admin.map.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header bg-transparent">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="row g-2">
                    <div class="col-md-3">
                        <select id="filterType" class="form-select form-select-sm">
                            <option value=""><?php echo e(__('admin.establishments.all_types')); ?></option>
                            <option value="client"><?php echo e(__('admin.establishments.client')); ?></option>
                            <option value="fournisseur"><?php echo e(__('admin.establishments.fournisseur')); ?></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterAgent" class="form-select form-select-sm">
                            <option value=""><?php echo e(__('admin.establishments.all_agents')); ?></option>
                            <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($agent->id); ?>"><?php echo e($agent->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="datetime-local" id="filterDateFrom" class="form-control form-control-sm" placeholder="<?php echo e(__('admin.common.from')); ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="datetime-local" id="filterDateTo" class="form-control form-control-sm" placeholder="<?php echo e(__('admin.common.to')); ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <span id="markerCount" class="badge bg-secondary">0 <?php echo e(__('admin.map.markers')); ?></span>
                <button id="btnRefresh" class="btn btn-sm btn-primary <?php echo e($isRtl ? 'me-2' : 'ms-2'); ?>">
                    <i class="bi bi-arrow-clockwise"></i> <?php echo e(__('admin.map.refresh')); ?>

                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="map" style="height: 600px;"></div>
    </div>
</div>

<!-- Legend -->
<div class="card mt-3">
    <div class="card-body py-2">
        <div class="d-flex justify-content-center gap-4 align-items-center">
            <span class="d-flex align-items-center gap-2">
                <span class="marker-legend marker-client"><i class="bi bi-wrench"></i></span>
                <?php echo e(__('admin.establishments.client')); ?>

            </span>
            <span class="d-flex align-items-center gap-2">
                <span class="marker-legend marker-fournisseur"><i class="bi bi-shop"></i></span>
                <?php echo e(__('admin.establishments.fournisseur')); ?>

            </span>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Marker styles */
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
    cursor: pointer;
    transition: transform 0.2s ease;
}

.custom-marker:hover {
    transform: scale(1.15);
}

.marker-client {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
}

.marker-fournisseur {
    background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
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
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Mauritania
    const map = L.map('map').setView([18.0735, -15.9582], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Marker cluster group
    const markers = L.markerClusterGroup({
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        maxClusterRadius: 50
    });
    map.addLayer(markers);

    // Translations
    const translations = {
        type: '<?php echo e(__('admin.establishments.type')); ?>',
        client: '<?php echo e(__('admin.establishments.client')); ?>',
        fournisseur: '<?php echo e(__('admin.establishments.fournisseur')); ?>',
        owner: '<?php echo e(__('admin.establishments.owner')); ?>',
        phone: '<?php echo e(__('admin.establishments.phone')); ?>',
        city: '<?php echo e(__('admin.establishments.city')); ?>',
        date: '<?php echo e(__('admin.establishments.date')); ?>',
        viewDetails: '<?php echo e(__('admin.common.view_details')); ?>'
    };

    // Create custom icon
    function createIcon(type) {
        const iconClass = type === 'client' ? 'bi-wrench' : 'bi-shop';
        const markerClass = type === 'client' ? 'marker-client' : 'marker-fournisseur';

        return L.divIcon({
            html: `<div class="custom-marker ${markerClass}"><i class="bi ${iconClass}"></i></div>`,
            className: 'custom-marker-wrapper',
            iconSize: [36, 36],
            iconAnchor: [18, 18],
            popupAnchor: [0, -20],
            tooltipAnchor: [18, 0]
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

    // Load markers
    function loadMarkers() {
        const params = new URLSearchParams({
            type: document.getElementById('filterType').value,
            agent_id: document.getElementById('filterAgent').value,
            date_from: document.getElementById('filterDateFrom').value,
            date_to: document.getElementById('filterDateTo').value
        });

        fetch('<?php echo e(route('admin.map.data')); ?>?' + params.toString())
            .then(response => response.json())
            .then(data => {
                markers.clearLayers();

                data.markers.forEach(m => {
                    const marker = L.marker([m.lat, m.lng], {
                        icon: createIcon(m.type)
                    });

                    // Bind popup (stays open until clicked elsewhere)
                    marker.bindPopup(createPopupContent(m), {
                        closeButton: true,
                        autoClose: true,
                        className: 'establishment-popup'
                    });

                    // Open popup on hover
                    marker.on('mouseover', function() {
                        this.openPopup();
                    });

                    markers.addLayer(marker);
                });

                document.getElementById('markerCount').textContent = data.count + ' <?php echo e(__('admin.map.markers')); ?>';

                // Fit bounds if markers exist
                if (data.markers.length > 0) {
                    map.fitBounds(markers.getBounds(), { padding: [50, 50] });
                }
            })
            .catch(error => {
                console.error('Error loading markers:', error);
            });
    }

    // Initial load
    loadMarkers();

    // Filter handlers
    document.getElementById('filterType').addEventListener('change', loadMarkers);
    document.getElementById('filterAgent').addEventListener('change', loadMarkers);
    document.getElementById('filterDateFrom').addEventListener('change', loadMarkers);
    document.getElementById('filterDateTo').addEventListener('change', loadMarkers);
    document.getElementById('btnRefresh').addEventListener('click', loadMarkers);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projects\backend\resources\views/admin/map/index.blade.php ENDPATH**/ ?>