<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Establishment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'week');

        $startDate = match ($period) {
            'today' => today(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subWeek(),
        };

        // Overall stats
        $stats = [
            'total' => Establishment::where('created_at', '>=', $startDate)->count(),
            'clients' => Establishment::clients()->where('created_at', '>=', $startDate)->count(),
            'fournisseurs' => Establishment::fournisseurs()->where('created_at', '>=', $startDate)->count(),
        ];

        // By agent
        $agentStats = User::agents()
            ->withCount(['establishments' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->withCount(['establishments as clients_count' => function ($query) use ($startDate) {
                $query->where('type', 'client')->where('created_at', '>=', $startDate);
            }])
            ->withCount(['establishments as fournisseurs_count' => function ($query) use ($startDate) {
                $query->where('type', 'fournisseur')->where('created_at', '>=', $startDate);
            }])
            ->having('establishments_count', '>', 0)
            ->orderByDesc('establishments_count')
            ->get();

        // Daily breakdown
        $dailyStats = $this->getDailyStats($startDate);

        return view('admin.reports.index', compact('stats', 'agentStats', 'dailyStats', 'period'));
    }

    public function daily(Request $request)
    {
        $date = $request->input('date', today()->toDateString());
        $date = Carbon::parse($date);

        $establishments = Establishment::with('agent')
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        $stats = [
            'total' => $establishments->count(),
            'clients' => $establishments->where('type', 'client')->count(),
            'fournisseurs' => $establishments->where('type', 'fournisseur')->count(),
        ];

        $byAgent = $establishments->groupBy('agent_id')->map(function ($items) {
            return [
                'agent' => $items->first()->agent->name ?? __('admin.common.na'),
                'count' => $items->count(),
                'clients' => $items->where('type', 'client')->count(),
                'fournisseurs' => $items->where('type', 'fournisseur')->count(),
            ];
        })->values();

        return view('admin.reports.daily', compact('date', 'establishments', 'stats', 'byAgent'));
    }

    public function byAgent(User $agent, Request $request)
    {
        if (!$agent->isAgent()) {
            abort(404);
        }

        $period = $request->input('period', 'month');

        $startDate = match ($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };

        $establishments = $agent->establishments()
            ->where('created_at', '>=', $startDate)
            ->latest()
            ->get();

        $stats = [
            'total' => $establishments->count(),
            'clients' => $establishments->where('type', 'client')->count(),
            'fournisseurs' => $establishments->where('type', 'fournisseur')->count(),
        ];

        // Daily chart data
        $dailyData = [];
        $days = $period === 'week' ? 7 : ($period === 'month' ? 30 : 365);

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyData[] = [
                'date' => $date->format('M d'),
                'count' => $agent->establishments()->whereDate('created_at', $date)->count(),
            ];
        }

        return view('admin.reports.agent', compact('agent', 'establishments', 'stats', 'dailyData', 'period'));
    }

    private function getDailyStats(Carbon $startDate): array
    {
        $stats = [];
        $current = $startDate->copy();
        $end = now();

        while ($current <= $end) {
            $stats[] = [
                'date' => $current->format('Y-m-d'),
                'label' => $current->format('M d'),
                'total' => Establishment::whereDate('created_at', $current)->count(),
                'clients' => Establishment::clients()->whereDate('created_at', $current)->count(),
                'fournisseurs' => Establishment::fournisseurs()->whereDate('created_at', $current)->count(),
            ];
            $current->addDay();
        }

        return $stats;
    }
}
