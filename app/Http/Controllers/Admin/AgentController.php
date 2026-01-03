<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AgentController extends Controller
{
    public function index()
    {
        $agents = User::agents()
            ->withCount('establishments')
            ->withCount(['establishments as today_establishments_count' => function ($query) {
                $query->whereDate('created_at', today());
            }])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.agents.index', compact('agents'));
    }

    public function create()
    {
        return view('admin.agents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'agent',
            'is_active' => true,
        ]);

        return redirect()->route('admin.agents.index')
            ->with('success', 'Agent created successfully.');
    }

    public function show(User $agent)
    {
        if (!$agent->isAgent()) {
            abort(404);
        }

        $agent->loadCount('establishments');

        $recentEstablishments = $agent->establishments()
            ->latest()
            ->take(10)
            ->get();

        $syncLogs = $agent->syncLogs()
            ->latest()
            ->take(10)
            ->get();

        // Stats for chart
        $weeklyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyStats[] = [
                'date' => $date->format('M d'),
                'count' => $agent->establishments()->whereDate('created_at', $date)->count(),
            ];
        }

        return view('admin.agents.show', compact('agent', 'recentEstablishments', 'syncLogs', 'weeklyStats'));
    }

    public function edit(User $agent)
    {
        if (!$agent->isAgent()) {
            abort(404);
        }

        return view('admin.agents.edit', compact('agent'));
    }

    public function update(Request $request, User $agent)
    {
        if (!$agent->isAgent()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($agent->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $agent->name = $validated['name'];
        $agent->email = $validated['email'];

        if (!empty($validated['password'])) {
            $agent->password = Hash::make($validated['password']);
        }

        $agent->save();

        return redirect()->route('admin.agents.index')
            ->with('success', 'Agent updated successfully.');
    }

    public function destroy(User $agent)
    {
        if (!$agent->isAgent()) {
            abort(404);
        }

        // Soft delete - just deactivate
        $agent->update(['is_active' => false]);

        return redirect()->route('admin.agents.index')
            ->with('success', 'Agent deactivated successfully.');
    }

    public function toggleStatus(User $agent)
    {
        if (!$agent->isAgent()) {
            abort(404);
        }

        $agent->update(['is_active' => !$agent->is_active]);

        $status = $agent->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Agent {$status} successfully.");
    }
}
