<?php $__env->startSection('title', __('admin.reports.agent_report')); ?>
<?php $__env->startSection('page-title', __('admin.reports.agent_report') . ': ' . $agent->name); ?>

<?php $__env->startSection('content'); ?>
<!-- Period Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row align-items-center">
            <div class="col-auto">
                <label class="form-label mb-0"><?php echo e(__('admin.reports.period')); ?>:</label>
            </div>
            <div class="col-auto">
                <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="week" <?php echo e($period === 'week' ? 'selected' : ''); ?>><?php echo e(__('admin.reports.last_7_days')); ?></option>
                    <option value="month" <?php echo e($period === 'month' ? 'selected' : ''); ?>><?php echo e(__('admin.reports.last_30_days')); ?></option>
                    <option value="year" <?php echo e($period === 'year' ? 'selected' : ''); ?>><?php echo e(__('admin.reports.last_year')); ?></option>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h2><?php echo e($stats['total']); ?></h2>
                <p class="mb-0"><?php echo e(__('admin.agents.total')); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h2><?php echo e($stats['clients']); ?></h2>
                <p class="mb-0"><?php echo e(__('admin.dashboard.clients')); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h2><?php echo e($stats['fournisseurs']); ?></h2>
                <p class="mb-0"><?php echo e(__('admin.dashboard.fournisseurs')); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="card mb-4">
    <div class="card-header bg-transparent">
        <h6 class="mb-0"><?php echo e(__('admin.reports.daily_activity')); ?></h6>
    </div>
    <div class="card-body">
        <canvas id="dailyChart" height="200"></canvas>
    </div>
</div>

<!-- Establishments -->
<div class="card mb-4">
    <div class="card-header bg-transparent">
        <h6 class="mb-0"><?php echo e(__('admin.agents.recent_registrations')); ?></h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-hover mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th><?php echo e(__('admin.establishments.date')); ?></th>
                        <th><?php echo e(__('admin.establishments.name')); ?></th>
                        <th><?php echo e(__('admin.establishments.type')); ?></th>
                        <th><?php echo e(__('admin.establishments.owner')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $establishments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($e->created_at->format('M d, H:i')); ?></td>
                            <td>
                                <a href="<?php echo e(route('admin.establishments.show', $e)); ?>"><?php echo e($e->name); ?></a>
                            </td>
                            <td>
                                <?php if($e->type === 'client'): ?>
                                    <span class="badge bg-info"><?php echo e(__('admin.establishments.client')); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?php echo e(__('admin.establishments.fournisseur')); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($e->owner_name); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted"><?php echo e(__('admin.reports.no_registrations_period')); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<a href="<?php echo e(route('admin.reports.index')); ?>" class="btn btn-secondary">
    <i class="bi bi-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?>"></i> <?php echo e(__('admin.reports.back_to_reports')); ?>

</a>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dailyData = <?php echo json_encode($dailyData, 15, 512) ?>;

    const ctx = document.getElementById('dailyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [{
                label: '<?php echo e(__('admin.agents.registrations')); ?>',
                data: dailyData.map(d => d.count),
                backgroundColor: 'rgba(52, 152, 219, 0.8)',
                borderRadius: 4
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projects\backend\resources\views/admin/reports/agent.blade.php ENDPATH**/ ?>