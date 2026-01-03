@extends('layouts.admin')

@section('title', __('admin.agents.agent_details'))
@section('page-title', __('admin.agents.agent') . ': ' . $agent->name)

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Agent Info Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-person-circle" style="font-size: 4rem; color: #6c757d;"></i>
                </div>
                <h5>{{ $agent->name }}</h5>
                <p class="text-muted mb-2">{{ $agent->email }}</p>
                @if($agent->is_active)
                    <span class="badge bg-success">{{ __('admin.agents.active') }}</span>
                @else
                    <span class="badge bg-danger">{{ __('admin.agents.inactive') }}</span>
                @endif
            </div>
            <div class="card-footer bg-transparent">
                <div class="row text-center">
                    <div class="col-6 {{ $isRtl ? 'border-start' : 'border-end' }}">
                        <h4 class="mb-0">{{ $agent->establishments_count }}</h4>
                        <small class="text-muted">{{ __('admin.agents.total') }}</small>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-0">{{ $agent->today_establishments_count }}</h4>
                        <small class="text-muted">{{ __('admin.agents.today') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="d-grid gap-2 mb-4">
            <a href="{{ route('admin.agents.edit', $agent) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> {{ __('admin.agents.edit_agent') }}
            </a>
            <form action="{{ route('admin.agents.toggle-status', $agent) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-{{ $agent->is_active ? 'warning' : 'success' }} w-100">
                    <i class="bi bi-{{ $agent->is_active ? 'pause' : 'play' }}"></i>
                    {{ $agent->is_active ? __('admin.agents.deactivate') : __('admin.agents.activate') }}
                </button>
            </form>
            <a href="{{ route('admin.agents.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-{{ $isRtl ? 'right' : 'left' }}"></i> {{ __('admin.common.back_to_list') }}
            </a>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Weekly Chart -->
        <div class="card mb-4">
            <div class="card-header bg-transparent">
                <h6 class="mb-0">{{ __('admin.agents.registrations_last_7_days') }}</h6>
            </div>
            <div class="card-body">
                <canvas id="weeklyChart" height="200"></canvas>
            </div>
        </div>

        <!-- Recent Establishments -->
        <div class="card mb-4">
            <div class="card-header bg-transparent">
                <h6 class="mb-0">{{ __('admin.agents.recent_registrations') }}</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('admin.establishments.name') }}</th>
                                <th>{{ __('admin.establishments.type') }}</th>
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
                                    <td>{{ $establishment->created_at->format('M d, H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">{{ __('admin.agents.no_registrations') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sync Logs -->
        <div class="card">
            <div class="card-header bg-transparent">
                <h6 class="mb-0">{{ __('admin.agents.recent_sync_activity') }}</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('admin.agents.date') }}</th>
                                <th>{{ __('admin.agents.total') }}</th>
                                <th>{{ __('admin.agents.success') }}</th>
                                <th>{{ __('admin.agents.failed') }}</th>
                                <th>{{ __('admin.agents.ip') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($syncLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('M d, H:i') }}</td>
                                    <td>{{ $log->establishments_count }}</td>
                                    <td><span class="text-success">{{ $log->success_count }}</span></td>
                                    <td><span class="text-danger">{{ $log->failed_count }}</span></td>
                                    <td><code>{{ $log->ip_address }}</code></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">{{ __('admin.agents.no_sync_activity') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const weeklyStats = @json($weeklyStats);

    const ctx = document.getElementById('weeklyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: weeklyStats.map(s => s.date),
            datasets: [{
                label: '{{ __('admin.agents.registrations') }}',
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
@endpush
