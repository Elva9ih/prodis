<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Establishment;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EstablishmentController extends Controller
{
    public function index(Request $request)
    {
        $agents = User::agents()->active()->orderBy('name')->get();
        $types = ['client', 'fournisseur'];

        return view('admin.establishments.index', compact('agents', 'types'));
    }

    /**
     * DataTables server-side data
     */
    public function data(Request $request)
    {
        $query = Establishment::with('agent');

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhereHas('agent', function ($aq) use ($search) {
                        $aq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Total count
        $totalCount = Establishment::count();
        $filteredCount = $query->count();

        // Sorting
        $columns = ['id', 'name', 'type', 'owner_name', 'phone_number', 'city', 'agent_id', 'created_at'];
        $orderColumn = $columns[$request->input('order.0.column', 7)] ?? 'created_at';
        $orderDir = $request->input('order.0.dir', 'desc');
        $query->orderBy($orderColumn, $orderDir);

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $establishments = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filteredCount,
            'data' => $establishments->map(function ($e) {
                $nameHtml = '<div class="d-flex align-items-center gap-2">';
                // if ($e->photo_url) {
                //     $nameHtml .= '<img src="' . $e->photo_url . '" alt="" class="rounded" style="width: 36px; height: 36px; object-fit: cover;">';
                // }
                $nameHtml .= '<span>' . e($e->name) . '</span></div>';

                return [
                    'id' => $e->id,
                    'barcode' => $e->barcode ?? '-',
                    'name' => $nameHtml,
                    'type' => $e->type,
                    'type_badge' => $e->type === 'client'
                        ? '<span class="badge bg-primary">' . __('admin.establishments.client') . '</span>'
                        : '<span class="badge bg-success">' . __('admin.establishments.fournisseur') . '</span>',
                    'owner_name' => $e->owner_name,
                    'phone' => $e->full_phone,
                    'city' => $e->city ?? '-',
                    'agent' => $e->agent->name ?? __('admin.common.na'),
                    'location' => number_format($e->latitude, 6) . ', ' . number_format($e->longitude, 6),
                    'created_at' => $e->created_at->format('Y-m-d H:i'),
                    'actions' => '<a href="' . route('admin.establishments.show', $e) . '" class="btn btn-sm btn-info">' . __('admin.common.view') . '</a>',
                ];
            }),
        ]);
    }

    public function show(Establishment $establishment)
    {
        $establishment->load(['agent', 'answers']);

        return view('admin.establishments.show', compact('establishment'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Establishment::with('agent');

        // Apply same filters as data()
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $establishments = $query->orderBy('created_at', 'desc')->get();

        $filename = 'establishments_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($establishments) {
            $handle = fopen('php://output', 'w');

            // CSV header
            fputcsv($handle, [
                __('admin.establishments.id'),
                __('admin.establishments.barcode'),
                __('admin.establishments.uuid'),
                __('admin.establishments.type'),
                __('admin.establishments.name'),
                __('admin.establishments.owner_name'),
                __('admin.establishments.phone'),
                __('admin.establishments.whatsapp'),
                'Bankili',
                'Latitude',
                'Longitude',
                __('admin.establishments.agent'),
                __('admin.establishments.synced_at'),
                __('admin.establishments.created_at'),
            ]);

            foreach ($establishments as $e) {
                fputcsv($handle, [
                    $e->id,
                    $e->barcode ?? '',
                    $e->uuid,
                    $e->type,
                    $e->name,
                    $e->owner_name,
                    $e->full_phone,
                    $e->full_whatsapp,
                    $e->bankili_number,
                    $e->latitude,
                    $e->longitude,
                    $e->agent->name ?? __('admin.common.na'),
                    $e->synced_at?->format('Y-m-d H:i:s'),
                    $e->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
