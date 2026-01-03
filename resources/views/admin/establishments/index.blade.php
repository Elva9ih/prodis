@extends('layouts.admin')

@section('title', __('admin.establishments.title'))
@section('page-title', __('admin.establishments.title'))

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
                        <input type="date" id="filterDateFrom" class="form-control form-control-sm" placeholder="{{ __('admin.common.from') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="filterDateTo" class="form-control form-control-sm" placeholder="{{ __('admin.common.to') }}">
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <button id="btnExport" class="btn btn-sm btn-success">
                    <i class="bi bi-download"></i> {{ __('admin.establishments.export_csv') }}
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="establishmentsTable" class="table table-hover" style="width: 100%">
                <thead>
                    <tr>
                        <th>{{ __('admin.establishments.id') }}</th>
                        <th>{{ __('admin.establishments.qrcode') }}</th>
                        <th>{{ __('admin.establishments.name') }}</th>
                        <th>{{ __('admin.establishments.type') }}</th>
                        <th>{{ __('admin.establishments.owner') }}</th>
                        <th>{{ __('admin.establishments.phone') }}</th>
                        <th>{{ __('admin.establishments.city') }}</th>
                        <th>{{ __('admin.establishments.agent') }}</th>
                        <th>{{ __('admin.establishments.date') }}</th>
                        <th>{{ __('admin.common.actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const table = $('#establishmentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.establishments.data') }}',
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
            processing: '<div class="spinner-border spinner-border-sm text-primary"></div> {{ __('admin.common.loading') }}',
            search: '{{ __('admin.common.search') }}:',
            lengthMenu: '{{ __('admin.common.show') }} _MENU_ {{ __('admin.common.entries') }}',
            info: '{{ __('admin.common.showing') }} _START_ {{ __('admin.common.to') }} _END_ {{ __('admin.common.of') }} _TOTAL_ {{ __('admin.common.entries') }}',
            infoEmpty: '{{ __('admin.common.no_entries') }}',
            infoFiltered: '({{ __('admin.common.filtered') }} _MAX_ {{ __('admin.common.entries') }})',
            paginate: {
                first: '{{ __('admin.common.first') }}',
                last: '{{ __('admin.common.last') }}',
                next: '{{ __('admin.common.next') }}',
                previous: '{{ __('admin.common.previous') }}'
            },
            emptyTable: '{{ __('admin.establishments.no_establishments') }}'
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
        window.location.href = '{{ route('admin.establishments.export') }}?' + params.toString();
    });
});
</script>
@endpush
