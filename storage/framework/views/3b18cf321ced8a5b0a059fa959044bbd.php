<?php $__env->startSection('title', __('admin.agents.agent_details')); ?>
<?php $__env->startSection('page-title', __('admin.agents.agent') . ': ' . $agent->name); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-4">
        <!-- Agent Info Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-person-circle" style="font-size: 4rem; color: #6c757d;"></i>
                </div>
                <h5><?php echo e($agent->name); ?></h5>
                <p class="text-muted mb-2"><?php echo e($agent->email); ?></p>
                <?php if($agent->is_active): ?>
                    <span class="badge bg-success"><?php echo e(__('admin.agents.active')); ?></span>
                <?php else: ?>
                    <span class="badge bg-danger"><?php echo e(__('admin.agents.inactive')); ?></span>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-transparent">
                <div class="row text-center">
                    <div class="col-6 <?php echo e($isRtl ? 'border-start' : 'border-end'); ?>">
                        <h4 class="mb-0"><?php echo e($agent->establishments_count); ?></h4>
                        <small class="text-muted"><?php echo e(__('admin.agents.total')); ?></small>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-0"><?php echo e($agent->today_establishments_count); ?></h4>
                        <small class="text-muted"><?php echo e(__('admin.agents.today')); ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="d-grid gap-2 mb-4">
            <a href="<?php echo e(route('admin.agents.edit', $agent)); ?>" class="btn btn-primary">
                <i class="bi bi-pencil"></i> <?php echo e(__('admin.agents.edit_agent')); ?>

            </a>
            <form action="<?php echo e(route('admin.agents.toggle-status', $agent)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-<?php echo e($agent->is_active ? 'warning' : 'success'); ?> w-100">
                    <i class="bi bi-<?php echo e($agent->is_active ? 'pause' : 'play'); ?>"></i>
                    <?php echo e($agent->is_active ? __('admin.agents.deactivate') : __('admin.agents.activate')); ?>

                </button>
            </form>
            <a href="<?php echo e(route('admin.agents.index')); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?>"></i> <?php echo e(__('admin.common.back_to_list')); ?>

            </a>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Weekly Chart -->
        <div class="card mb-4">
            <div class="card-header bg-transparent">
                <h6 class="mb-0"><?php echo e(__('admin.agents.registrations_last_7_days')); ?></h6>
            </div>
            <div class="card-body">
                <canvas id="weeklyChart" height="200"></canvas>
            </div>
        </div>

        <!-- Recent Establishments -->
        <div class="card mb-4">
            <div class="card-header bg-transparent">
                <h6 class="mb-0"><?php echo e(__('admin.agents.recent_registrations')); ?></h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><?php echo e(__('admin.establishments.name')); ?></th>
                                <th><?php echo e(__('admin.establishments.type')); ?></th>
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
                                    <td><?php echo e($establishment->created_at->format('M d, H:i')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted"><?php echo e(__('admin.agents.no_registrations')); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sync Logs -->
        <div class="card">
            <div class="card-header bg-transparent">
                <h6 class="mb-0"><?php echo e(__('admin.agents.recent_sync_activity')); ?></h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><?php echo e(__('admin.agents.date')); ?></th>
                                <th><?php echo e(__('admin.agents.total')); ?></th>
                                <th><?php echo e(__('admin.agents.success')); ?></th>
                                <th><?php echo e(__('admin.agents.failed')); ?></th>
                                <th><?php echo e(__('admin.agents.ip')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $syncLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($log->created_at->format('M d, H:i')); ?></td>
                                    <td><?php echo e($log->establishments_count); ?></td>
                                    <td><span class="text-success"><?php echo e($log->success_count); ?></span></td>
                                    <td><span class="text-danger"><?php echo e($log->failed_count); ?></span></td>
                                    <td><code><?php echo e($log->ip_address); ?></code></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted"><?php echo e(__('admin.agents.no_sync_activity')); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const weeklyStats = <?php echo json_encode($weeklyStats, 15, 512) ?>;

    const ctx = document.getElementById('weeklyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: weeklyStats.map(s => s.date),
            datasets: [{
                label: '<?php echo e(__('admin.agents.registrations')); ?>',
                data: weeklyStats.map(s => s.count),
                borderColor: 'rgba(52, 152, 219, 1)',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projects\backend\resources\views/admin/agents/show.blade.php ENDPATH**/ ?>