<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ScrapSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScrapSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ScrapSale::with('createdBy');

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('sale_date', [$request->date_from, $request->date_to]);
        }

        return response()->json(
            $query->latest('sale_date')->paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date',
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'weight_kg' => 'nullable|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['total_price'] = $validated['quantity'] * $validated['price_per_unit'];

        $sale = ScrapSale::create($validated);

        return response()->json($sale->load('createdBy'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ScrapSale $scrapSale)
    {
        return response()->json($scrapSale->load('createdBy'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ScrapSale $scrapSale)
    {
        $this->authorize('update', $scrapSale);

        $validated = $request->validate([
            'sale_date' => 'date',
            'item_name' => 'string|max:255',
            'quantity' => 'integer|min:1',
            'weight_kg' => 'nullable|numeric|min:0',
            'price_per_unit' => 'numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if (isset($validated['quantity']) && isset($validated['price_per_unit'])) {
            $validated['total_price'] = $validated['quantity'] * $validated['price_per_unit'];
        }

        $scrapSale->update($validated);

        return response()->json($scrapSale->load('createdBy'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScrapSale $scrapSale)
    {
        $this->authorize('delete', $scrapSale);

        $scrapSale->delete();

        return response()->json(['message' => 'Scrap sale deleted successfully']);
    }

    /**
     * Get sales report
     */
    public function getReport(Request $request)
    {
        $query = ScrapSale::query();

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('sale_date', [$request->date_from, $request->date_to]);
        }

        $totalSales = $query->sum('total_price');
        $totalQuantity = $query->sum('quantity');
        $totalWeight = $query->sum('weight_kg');
        $salesCount = $query->count();

        return response()->json([
            'total_sales' => $totalSales,
            'total_quantity' => $totalQuantity,
            'total_weight_kg' => $totalWeight,
            'sales_count' => $salesCount,
            'average_per_sale' => $salesCount > 0 ? round($totalSales / $salesCount, 2) : 0,
        ]);
    }
}
