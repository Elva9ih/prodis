<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Establishment;
use App\Models\User;
use App\Models\SyncLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Summary stats
        $stats = [
            'total_establishments' => Establishment::count(),
            'total_clients' => Establishment::clients()->count(),
            'total_fournisseurs' => Establishment::fournisseurs()->count(),
            'today_registrations' => Establishment::today()->count(),
            'total_agents' => User::agents()->count(),
            'active_agents' => User::agents()->active()->count(),
        ];

        // Recent activity
        $recentEstablishments = Establishment::with('agent')
            ->latest()
            ->take(10)
            ->get();

        // Agent performance today
        $agentPerformance = User::agents()
            ->active()
            ->withCount(['establishments as today_count' => function ($query) {
                $query->whereDate('created_at', today());
            }])
            ->orderByDesc('today_count')
            ->take(5)
            ->get();

        // Chart data - last 7 days
        $chartData = $this->getWeeklyChartData();

        return view('admin.dashboard.index', compact(
            'stats',
            'recentEstablishments',
            'agentPerformance',
            'chartData'
        ));
    }

    public function stats(Request $request)
    {
        $period = $request->input('period', 'week');

        $startDate = match ($period) {
            'today' => today(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subWeek(),
        };

        $stats = [
            'total' => Establishment::where('created_at', '>=', $startDate)->count(),
            'clients' => Establishment::clients()->where('created_at', '>=', $startDate)->count(),
            'fournisseurs' => Establishment::fournisseurs()->where('created_at', '>=', $startDate)->count(),
        ];

        return response()->json($stats);
    }

    private function getWeeklyChartData(): array
    {
        $days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $days->push(Carbon::today()->subDays($i));
        }

        $clients = [];
        $fournisseurs = [];
        $labels = [];

        foreach ($days as $day) {
            $labels[] = $day->format('M d');
            $clients[] = Establishment::clients()->whereDate('created_at', $day)->count();
            $fournisseurs[] = Establishment::fournisseurs()->whereDate('created_at', $day)->count();
        }

        return [
            'labels' => $labels,
            'clients' => $clients,
            'fournisseurs' => $fournisseurs,
        ];
    }
}
