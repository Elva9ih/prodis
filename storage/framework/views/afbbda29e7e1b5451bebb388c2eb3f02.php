<?php $__env->startSection('title', __('admin.establishments.title')); ?>
<?php $__env->startSection('page-title', __('admin.establishments.title')); ?>

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
                        <input type="date" id="filterDateFrom" class="form-control form-control-sm" placeholder="<?php echo e(__('admin.common.from')); ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="filterDateTo" class="form-control form-control-sm" placeholder="<?php echo e(__('admin.common.to')); ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <button id="btnExport" class="btn btn-sm btn-success">
                    <i class="bi bi-download"></i> <?php echo e(__('admin.establishments.export_csv')); ?>

                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="establishmentsTable" class="table table-hover" style="width: 100%">
                <thead>
                    <tr>
                        <th><?php echo e(__('admin.establishments.id')); ?></th>
                        <th><?php echo e(__('admin.establishments.qrcode')); ?></th>
                        <th><?php echo e(__('admin.establishments.name')); ?></th>
                        <th><?php echo e(__('admin.establishments.type')); ?></th>
                        <th><?php echo e(__('admin.establishments.owner')); ?></th>
                        <th><?php echo e(__('admin.establishments.phone')); ?></th>
                        <th><?php echo e(__('admin.establishments.city')); ?></th>
                        <th><?php echo e(__('admin.establishments.agent')); ?></th>
                        <th><?php echo e(__('admin.establishments.date')); ?></th>
                        <th><?php echo e(__('admin.common.actions')); ?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    const table = $('#establishmentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?php echo e(route('admin.establishments.data')); ?>',
            data: function(d) {
                d.type = $('#filterType').val();
                d.agent_id = $('#filterAgent').val();
                d.date_from = $('#filterDateFrom').val();
                d.date_to = $('#filterDateTo').val();
            }
        },
        columns: [
            { data: 'id' },
            { data: 'barcode' },
            { data: 'name' },
            { data: 'type_badge', orderable: false },
            { data: 'owner_name' },
            { data: 'phone' },
            { data: 'city' },
            { data: 'agent' },
            { data: 'created_at' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        order: [[8, 'desc']],
        pageLength: 25,
        language: {
            processing: '<div class="spinner-border spinner-border-sm text-primary"></div> <?php echo e(__('admin.common.loading')); ?>',
            search: '<?php echo e(__('admin.common.search')); ?>:',
            lengthMenu: '<?php echo e(__('admin.common.show')); ?> _MENU_ <?php echo e(__('admin.common.entries')); ?>',
            info: '<?php echo e(__('admin.common.showing')); ?> _START_ <?php echo e(__('admin.common.to')); ?> _END_ <?php echo e(__('admin.common.of')); ?> _TOTAL_ <?php echo e(__('admin.common.entries')); ?>',
            infoEmpty: '<?php echo e(__('admin.common.no_entries')); ?>',
            infoFiltered: '(<?php echo e(__('admin.common.filtered')); ?> _MAX_ <?php echo e(__('admin.common.entries')); ?>)',
            paginate: {
                first: '<?php echo e(__('admin.common.first')); ?>',
                last: '<?php echo e(__('admin.common.last')); ?>',
                next: '<?php echo e(__('admin.common.next')); ?>',
                previous: '<?php echo e(__('admin.common.previous')); ?>'
            },
            emptyTable: '<?php echo e(__('admin.establishments.no_establishments')); ?>'
        }
    });

    // Filter handlers
    $('#filterType, #filterAgent, #filterDateFrom, #filterDateTo').on('change', function() {
        table.ajax.reload();
    });

    // Export handler
    $('#btnExport').on('click', function() {
        const params = new URLSearchParams({
            type: $('#filterType').val(),
            agent_id: $('#filterAgent').val(),
            date_from: $('#filterDateFrom').val(),
            date_to: $('#filterDateTo').val()
        });
        window.location.href = '<?php echo e(route('admin.establishments.export')); ?>?' + params.toString();
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projects\backend\resources\views/admin/establishments/index.blade.php ENDPATH**/ ?>