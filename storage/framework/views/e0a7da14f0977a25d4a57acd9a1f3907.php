<?php $__env->startSection('title', __('admin.reports.title')); ?>
<?php $__env->startSection('page-title', __('admin.reports.statistics')); ?>

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
                    <option value="today" <?php echo e($period === 'today' ? 'selected' : ''); ?>><?php echo e(__('admin.reports.today')); ?></option>
                    <option value="week" <?php echo e($period === 'week' ? 'selected' : ''); ?>><?php echo e(__('admin.reports.last_7_days')); ?></option>
                    <option value="month" <?php echo e($period === 'month' ? 'selected' : ''); ?>><?php echo e(__('admin.reports.last_30_days')); ?></option>
                    <option value="year" <?php echo e($period === 'year' ? 'selected' : ''); ?>><?php echo e(__('admin.reports.last_year')); ?></option>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h3><?php echo e(number_format($stats['total'])); ?></h3>
                <p class="mb-0"><?php echo e(__('admin.reports.total_registrations')); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h3><?php echo e(number_format($stats['clients'])); ?></h3>
                <p class="mb-0"><?php echo e(__('admin.dashboard.clients')); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h3><?php echo e(number_format($stats['fournisseurs'])); ?></h3>
                <p class="mb-0"><?php echo e(__('admin.dashboard.fournisseurs')); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Daily Chart -->
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-transparent">
                <h6 class="mb-0"><?php echo e(__('admin.reports.daily_registrations')); ?></h6>
            </div>
            <div class="card-body">
                <canvas id="dailyChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- By Agent -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-transparent">
                <h6 class="mb-0"><?php echo e(__('admin.reports.by_agent')); ?></h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><?php echo e(__('admin.establishments.agent')); ?></th>
                                <th class="text-center">G</th>
                                <th class="text-center">F</th>
                                <th class="text-center"><?php echo e(__('admin.agents.total')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $agentStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e(route('admin.reports.agent', $agent)); ?>">
                                            <?php echo e($agent->name); ?>

                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info"><?php echo e($agent->clients_count); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success"><?php echo e($agent->fournisseurs_count); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <strong><?php echo e($agent->establishments_count); ?></strong>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted"><?php echo e(__('admin.common.no_data')); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Type Distribution -->
<div class="card">
    <div class="card-header bg-transparent">
        <h6 class="mb-0"><?php echo e(__('admin.reports.type_distribution')); ?></h6>
    </div>
    <div class="card-body">
        <canvas id="typeChart" height="200"></canvas>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dailyStats = <?php echo json_encode($dailyStats, 15, 512) ?>;

    // Daily Chart
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'bar',
        data: {
            labels: dailyStats.map(s => s.label),
            datasets: [{
                label: '<?php echo e(__('admin.agents.total')); ?>',
                data: dailyStats.map(s => s.total),
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

    // Type Distribution Chart
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'line',
        data: {
            labels: dailyStats.map(s => s.label),
            datasets: [
                {
                    label: '<?php echo e(__('admin.dashboard.clients')); ?>',
                    data: dailyStats.map(s => s.clients),
                    borderColor: 'rgba(52, 152, 219, 1)',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: '<?php echo e(__('admin.dashboard.fournisseurs')); ?>',
                    data: dailyStats.map(s => s.fournisseurs),
                    borderColor: 'rgba(46, 204, 113, 1)',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projects\backend\resources\views/admin/reports/index.blade.php ENDPATH**/ ?>