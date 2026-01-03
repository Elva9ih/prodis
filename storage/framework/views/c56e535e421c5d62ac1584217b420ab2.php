<?php $__env->startSection('title', __('admin.agents.title')); ?>
<?php $__env->startSection('page-title', __('admin.agents.field_agents')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><?php echo e(__('admin.agents.all_agents')); ?></h6>
        <a href="<?php echo e(route('admin.agents.create')); ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> <?php echo e(__('admin.agents.add_agent')); ?>

        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th><?php echo e(__('admin.agents.name')); ?></th>
                        <th><?php echo e(__('admin.agents.email')); ?></th>
                        <th><?php echo e(__('admin.agents.today')); ?></th>
                        <th><?php echo e(__('admin.agents.total')); ?></th>
                        <th><?php echo e(__('admin.agents.status')); ?></th>
                        <th><?php echo e(__('admin.common.actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <i class="bi bi-person-circle text-muted <?php echo e($isRtl ? 'ms-2' : 'me-2'); ?>"></i>
                                <a href="<?php echo e(route('admin.agents.show', $agent)); ?>"><?php echo e($agent->name); ?></a>
                            </td>
                            <td><?php echo e($agent->email); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo e($agent->today_establishments_count); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?php echo e($agent->establishments_count); ?></span>
                            </td>
                            <td>
                                <?php if($agent->is_active): ?>
                                    <span class="badge bg-success"><?php echo e(__('admin.agents.active')); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><?php echo e(__('admin.agents.inactive')); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('admin.agents.show', $agent)); ?>" class="btn btn-outline-info" title="<?php echo e(__('admin.common.view')); ?>">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.agents.edit', $agent)); ?>" class="btn btn-outline-primary" title="<?php echo e(__('admin.common.edit')); ?>">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?php echo e(route('admin.agents.toggle-status', $agent)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-outline-<?php echo e($agent->is_active ? 'warning' : 'success'); ?>" title="<?php echo e($agent->is_active ? __('admin.agents.deactivate') : __('admin.agents.activate')); ?>">
                                            <i class="bi bi-<?php echo e($agent->is_active ? 'pause' : 'play'); ?>"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4"><?php echo e(__('admin.agents.no_agents')); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($agents->hasPages()): ?>
        <div class="card-footer bg-transparent">
            <?php echo e($agents->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projects\backend\resources\views/admin/agents/index.blade.php ENDPATH**/ ?>