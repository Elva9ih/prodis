@extends('layouts.admin')

@section('title', __('admin.agents.title'))
@section('page-title', __('admin.agents.field_agents'))

@section('content')
<div class="card">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h6 class="mb-0">{{ __('admin.agents.all_agents') }}</h6>
        <a href="{{ route('admin.agents.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> {{ __('admin.agents.add_agent') }}
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('admin.agents.name') }}</th>
                        <th>{{ __('admin.agents.email') }}</th>
                        <th>{{ __('admin.agents.today') }}</th>
                        <th>{{ __('admin.agents.total') }}</th>
                        <th>{{ __('admin.agents.status') }}</th>
                        <th>{{ __('admin.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agents as $agent)
                        <tr>
                            <td>
                                <i class="bi bi-person-circle text-muted {{ $isRtl ? 'ms-2' : 'me-2' }}"></i>
                                <a href="{{ route('admin.agents.show', $agent) }}">{{ $agent->name }}</a>
                            </td>
                            <td>{{ $agent->email }}</td>
                            <td>
                                <span class="badge bg-info">{{ $agent->today_establishments_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $agent->establishments_count }}</span>
                            </td>
                            <td>
                                @if($agent->is_active)
                                    <span class="badge bg-success">{{ __('admin.agents.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('admin.agents.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.agents.show', $agent) }}" class="btn btn-outline-info" title="{{ __('admin.common.view') }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.agents.edit', $agent) }}" class="btn btn-outline-primary" title="{{ __('admin.common.edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.agents.toggle-status', $agent) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-{{ $agent->is_active ? 'warning' : 'success' }}" title="{{ $agent->is_active ? __('admin.agents.deactivate') : __('admin.agents.activate') }}">
                                            <i class="bi bi-{{ $agent->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">{{ __('admin.agents.no_agents') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($agents->hasPages())
        <div class="card-footer bg-transparent">
            {{ $agents->links() }}
        </div>
    @endif
</div>
@endsection
