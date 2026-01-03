<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Establishment;
use App\Models\User;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index(Request $request)
    {
        $agents = User::agents()->active()->orderBy('name')->get();
        $types = ['client', 'fournisseur'];

        return view('admin.map.index', compact('agents', 'types'));
    }

    /**
     * Get establishments data for map markers
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
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        // Limit for performance
        $establishments = $query->latest()->take(1000)->get();

        $markers = $establishments->map(function ($e) {
            return [
                'id' => $e->id,
                'uuid' => $e->uuid,
                'type' => $e->type,
                'name' => $e->name,
                'owner_name' => $e->owner_name,
                'phone' => $e->full_phone,
                'city' => $e->city,
                'lat' => (float) $e->latitude,
                'lng' => (float) $e->longitude,
                'created_at' => $e->created_at->format('Y-m-d H:i'),
                'photo_url' => $e->photo_url,
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $markers->count(),
            'markers' => $markers,
        ]);
    }
}
