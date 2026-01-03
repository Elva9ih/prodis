@extends('layouts.admin')

@section('title', __('admin.reports.daily_report'))
@section('page-title', __('admin.reports.daily_report') . ': ' . $date->format('F d, Y'))

@section('content')
<!-- Date Selector -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row align-items-center">
            <div class="col-auto">
                <label class="form-label mb-0">{{ __('admin.reports.date') }}:</label>
            </div>
            <div class="col-auto">
                <input type="date" name="date" class="form-control form-control-sm"
                       value="{{ $date->format('Y-m-d') }}" onchange="this.form.submit()">
            </div>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h2>{{ $stats['total'] }}</h2>
                <p class="mb-0">{{ __('admin.agents.total') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h2>{{ $stats['clients'] }}</h2>
                <p class="mb-0">{{ __('admin.dashboard.clients') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h2>{{ $stats['fournisseurs'] }}</h2>
                <p class="mb-0">{{ __('admin.dashboard.fournisseurs') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- By Agent -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-transparent">
                <h6 class="mb-0">{{ __('admin.reports.by_agent') }}</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($byAgent as $data)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $data['agent'] }}</span>
                            <div>
                                <span class="badge bg-info">{{ $data['clients'] }}</span>
                                <span class="badge bg-success">{{ $data['fournisseurs'] }}</span>
                                <strong class="{{ $isRtl ? 'me-2' : 'ms-2' }}">{{ $data['count'] }}</strong>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted">{{ __('admin.common.no_data') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Establishments List -->
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-transparent">
                <h6 class="mb-0">{{ __('admin.reports.all_registrations') }}</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>{{ __('admin.reports.time') }}</th>
                                <th>{{ __('admin.establishments.name') }}</th>
                                <th>{{ __('admin.establishments.type') }}</th>
                                <th>{{ __('admin.establishments.agent') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($establishments as $e)
                                <tr>
                                    <td>{{ $e->created_at->format('H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.establishments.show', $e) }}">
                                            {{ $e->name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($e->type === 'client')
                                            <span class="badge bg-info">{{ __('admin.establishments.client') }}</span>
                                        @else
                                            <span class="badge bg-success">{{ __('admin.establishments.fournisseur') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $e->agent->name ?? __('admin.common.na') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">{{ __('admin.reports.no_registrations_day') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
    <i class="bi bi-arrow-{{ $isRtl ? 'right' : 'left' }}"></i> {{ __('admin.reports.back_to_reports') }}
</a>
@endsection
