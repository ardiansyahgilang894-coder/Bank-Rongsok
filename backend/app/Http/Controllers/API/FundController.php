<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Fund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Fund::with('createdBy')
                ->latest()
                ->paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,completed',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['current_amount'] = 0;

        $fund = Fund::create($validated);

        return response()->json($fund->load('createdBy'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Fund $fund)
    {
        return response()->json($fund->load('createdBy'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fund $fund)
    {
        $this->authorize('update', $fund);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'numeric|min:0',
            'current_amount' => 'numeric|min:0',
            'status' => 'in:active,inactive,completed',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $fund->update($validated);

        return response()->json($fund->load('createdBy'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fund $fund)
    {
        $this->authorize('delete', $fund);

        $fund->delete();

        return response()->json(['message' => 'Fund deleted successfully']);
    }

    /**
     * Get fund progress/statistics
     */
    public function getStats()
    {
        $activeFunds = Fund::where('status', 'active')->count();
        $totalRaised = Fund::sum('current_amount');
        $totalTarget = Fund::sum('target_amount');
        $completedFunds = Fund::where('status', 'completed')->count();

        return response()->json([
            'active_funds' => $activeFunds,
            'total_raised' => $totalRaised,
            'total_target' => $totalTarget,
            'completed_funds' => $completedFunds,
            'progress_percentage' => $totalTarget > 0 ? round(($totalRaised / $totalTarget) * 100, 2) : 0,
        ]);
    }
}
