<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DonationDistribution;
use App\Models\DistributionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationDistributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            DonationDistribution::with('items', 'createdBy')
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
            'location' => 'required|string|max:255',
            'distribution_date' => 'required|date',
            'recipient_count' => 'required|integer|min:0',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.item_name' => 'required_with:items|string|max:255',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $items = $validated['items'] ?? [];
        unset($validated['items']);

        $distribution = DonationDistribution::create($validated);

        foreach ($items as $item) {
            DistributionItem::create([
                'distribution_id' => $distribution->id,
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        return response()->json($distribution->load('items', 'createdBy'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DonationDistribution $donationDistribution)
    {
        return response()->json($donationDistribution->load('items', 'createdBy'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DonationDistribution $donationDistribution)
    {
        $this->authorize('update', $donationDistribution);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'location' => 'string|max:255',
            'distribution_date' => 'date',
            'recipient_count' => 'integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $donationDistribution->update($validated);

        return response()->json($donationDistribution->load('items', 'createdBy'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DonationDistribution $donationDistribution)
    {
        $this->authorize('delete', $donationDistribution);

        $donationDistribution->delete();

        return response()->json(['message' => 'Distribution deleted successfully']);
    }

    /**
     * Add item to distribution
     */
    public function addItem(Request $request, DonationDistribution $donationDistribution)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $item = DistributionItem::create([
            'distribution_id' => $donationDistribution->id,
            'item_name' => $validated['item_name'],
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json($item, 201);
    }
}
