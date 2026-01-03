<?php $__env->startSection('title', __('admin.dashboard.title')); ?>
<?php $__env->startSection('page-title', __('admin.dashboard.title')); ?>

<?php $__env->startSection('content'); ?>
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary <?php echo e($isRtl ? 'me-3' : 'me-3'); ?>">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?php echo e(number_format($stats['total_establishments'])); ?></h3>
                    <small class="text-muted"><?php echo e(__('admin.dashboard.total_establishments')); ?></small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info <?php echo e($isRtl ? 'me-3' : 'me-3'); ?>">
                    <i class="bi bi-wrench"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?php echo e(number_format($stats['total_clients'])); ?></h3>
                    <small class="text-muted"><?php echo e(__('admin.dashboard.clients')); ?></small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success <?php echo e($isRtl ? 'me-3' : 'me-3'); ?>">
                    <i class="bi bi-shop"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?php echo e(number_format($stats['total_fournisseurs'])); ?></h3>
                    <small class="text-muted"><?php echo e(__('admin.dashboard.fournisseurs')); ?></small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning <?php echo e($isRtl ? 'me-3' : 'me-3'); ?>">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?php echo e(number_format($stats['today_registrations'])); ?></h3>
                    <small class="text-muted"><?php echo e(__('admin.dashboard.today_registrations')); ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart -->
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-transparent">
                <h6 class="mb-0"><?php echo e(__('admin.dashboard.registrations_last_7_days')); ?></h6>
            </div>
            <div class="card-body">
                <canvas id="registrationsChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Agents -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><?php echo e(__('admin.dashboard.top_agents_today')); ?></h6>
                <a href="<?php echo e(route('admin.agents.index')); ?>" class="btn btn-sm btn-outline-primary"><?php echo e(__('admin.common.view_all')); ?></a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $agentPerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-person-circle text-muted <?php echo e($isRtl ? 'ms-2' : 'me-2'); ?>"></i>
                                <?php echo e($agent->name); ?>

                            </div>
                            <span class="badge bg-primary rounded-pill"><?php echo e($agent->today_count); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item text-muted text-center"><?php echo e(__('admin.dashboard.no_registrations_today')); ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="card">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><?php echo e(__('admin.dashboard.recent_registrations')); ?></h6>
        <a href="<?php echo e(route('admin.establishments.index')); ?>" class="btn btn-sm btn-outline-primary"><?php echo e(__('admin.common.view_all')); ?></a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th><?php echo e(__('admin.establishments.name')); ?></th>
                        <th><?php echo e(__('admin.establishments.type')); ?></th>
                        <th><?php echo e(__('admin.establishments.owner')); ?></th>
                        <th><?php echo e(__('admin.establishments.agent')); ?></th>
                        <th><?php echo e(__('admin.establishments.date')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentEstablishments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $establishment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(route('admin.establishments.show', $establishment)); ?>">
                                    <?php echo e($establishment->name); ?>

                                </a>
                            </td>
                            <td>
                                <?php if($establishment->type === 'client'): ?>
                                    <span class="badge bg-primary"><?php echo e(__('admin.establishments.client')); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?php echo e(__('admin.establishments.fournisseur')); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($establishment->owner_name); ?></td>
                            <td><?php echo e($establishment->agent->name ?? __('admin.common.na')); ?></td>
                            <td><?php echo e($establishment->created_at->format('M d, H:i')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted"><?php echo e(__('admin.establishments.no_establishments')); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('registrationsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chartData['labels']); ?>,
            datasets: [
                {
                    label: '<?php echo e(__('admin.dashboard.clients')); ?>',
                    data: <?php echo json_encode($chartData['clients']); ?>,
                    backgroundColor: 'rgba(52, 152, 219, 0.8)',
                    borderRadius: 4,
                },
                {
                    label: '<?php echo e(__('admin.dashboard.fournisseurs')); ?>',
                    data: <?php echo json_encode($chartData['fournisseurs']); ?>,
                    backgroundColor: 'rgba(46, 204, 113, 0.8)',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projects\backend\resources\views/admin/dashboard/index.blade.php ENDPATH**/ ?>