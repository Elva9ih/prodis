@extends('layouts.admin')

@section('title', __('admin.dashboard.title'))
@section('page-title', __('admin.dashboard.title'))

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary {{ $isRtl ? 'me-3' : 'me-3' }}">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ number_format($stats['total_establishments']) }}</h3>
                    <small class="text-muted">{{ __('admin.dashboard.total_establishments') }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info {{ $isRtl ? 'me-3' : 'me-3' }}">
                    <i class="bi bi-wrench"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ number_format($stats['total_clients']) }}</h3>
                    <small class="text-muted">{{ __('admin.dashboard.clients') }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success {{ $isRtl ? 'me-3' : 'me-3' }}">
                    <i class="bi bi-shop"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ number_format($stats['total_fournisseurs']) }}</h3>
                    <small class="text-muted">{{ __('admin.dashboard.fournisseurs') }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning {{ $isRtl ? 'me-3' : 'me-3' }}">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ number_format($stats['today_registrations']) }}</h3>
                    <small class="text-muted">{{ __('admin.dashboard.today_registrations') }}</small>
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
                <h6 class="mb-0">{{ __('admin.dashboard.registrations_last_7_days') }}</h6>
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
                <h6 class="mb-0">{{ __('admin.dashboard.top_agents_today') }}</h6>
                <a href="{{ route('admin.agents.index') }}" class="btn btn-sm btn-outline-primary">{{ __('admin.common.view_all') }}</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($agentPerformance as $agent)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-person-circle text-muted {{ $isRtl ? 'ms-2' : 'me-2' }}"></i>
                                {{ $agent->name }}
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $agent->today_count }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center">{{ __('admin.dashboard.no_registrations_today') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="card">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h6 class="mb-0">{{ __('admin.dashboard.recent_registrations') }}</h6>
        <a href="{{ route('admin.establishments.index') }}" class="btn btn-sm btn-outline-primary">{{ __('admin.common.view_all') }}</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('admin.establishments.name') }}</th>
                        <th>{{ __('admin.establishments.type') }}</th>
                        <th>{{ __('admin.establishments.owner') }}</th>
                        <th>{{ __('admin.establishments.agent') }}</th>
                        <th>{{ __('admin.establishments.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentEstablishments as $establishment)
                        <tr>
                            <td>
                                <a href="{{ route('admin.establishments.show', $establishment) }}">
                                    {{ $establishment->name }}
                                </a>
                            </td>
                            <td>
                                @if($establishment->type === 'client')
                                    <span class="badge bg-primary">{{ __('admin.establishments.client') }}</span>
                                @else
                                    <span class="badge bg-success">{{ __('admin.establishments.fournisseur') }}</span>
                                @endif
                            </td>
                            <td>{{ $establishment->owner_name }}</td>
                            <td>{{ $establishment->agent->name ?? __('admin.common.na') }}</td>
                            <td>{{ $establishment->created_at->format('M d, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">{{ __('admin.establishments.no_establishments') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('registrationsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [
                {
                    label: '{{ __('admin.dashboard.clients') }}',
                    data: {!! json_encode($chartData['clients']) !!},
                    backgroundColor: 'rgba(52, 152, 219, 0.8)',
                    borderRadius: 4,
                },
                {
                    label: '{{ __('admin.dashboard.fournisseurs') }}',
                    data: {!! json_encode($chartData['fournisseurs']) !!},
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
@endpush
