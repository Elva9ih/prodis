@extends('layouts.admin')

@section('title', __('admin.reports.agent_report'))
@section('page-title', __('admin.reports.agent_report') . ': ' . $agent->name)

@section('content')
<!-- Period Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row align-items-center">
            <div class="col-auto">
                <label class="form-label mb-0">{{ __('admin.reports.period') }}:</label>
            </div>
            <div class="col-auto">
                <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>{{ __('admin.reports.last_7_days') }}</option>
                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>{{ __('admin.reports.last_30_days') }}</option>
                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>{{ __('admin.reports.last_year') }}</option>
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

<!-- Chart -->
<div class="card mb-4">
    <div class="card-header bg-transparent">
        <h6 class="mb-0">{{ __('admin.reports.daily_activity') }}</h6>
    </div>
    <div class="card-body">
        <canvas id="dailyChart" height="200"></canvas>
    </div>
</div>

<!-- Establishments -->
<div class="card mb-4">
    <div class="card-header bg-transparent">
        <h6 class="mb-0">{{ __('admin.agents.recent_registrations') }}</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-hover mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>{{ __('admin.establishments.date') }}</th>
                        <th>{{ __('admin.establishments.name') }}</th>
                        <th>{{ __('admin.establishments.type') }}</th>
                        <th>{{ __('admin.establishments.owner') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($establishments as $e)
                        <tr>
                            <td>{{ $e->created_at->format('M d, H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.establishments.show', $e) }}">{{ $e->name }}</a>
                            </td>
                            <td>
                                @if($e->type === 'client')
                                    <span class="badge bg-info">{{ __('admin.establishments.client') }}</span>
                                @else
                                    <span class="badge bg-success">{{ __('admin.establishments.fournisseur') }}</span>
                                @endif
                            </td>
                            <td>{{ $e->owner_name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">{{ __('admin.reports.no_registrations_period') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
    <i class="bi bi-arrow-{{ $isRtl ? 'right' : 'left' }}"></i> {{ __('admin.reports.back_to_reports') }}
</a>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dailyData = @json($dailyData);

    const ctx = document.getElementById('dailyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [{
                label: '{{ __('admin.agents.registrations') }}',
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
@endpush
