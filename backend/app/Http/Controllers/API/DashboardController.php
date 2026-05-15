<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Fund;
use App\Models\ScrapSale;
use App\Models\DonationDistribution;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard analytics
     */
    public function getAnalytics()
    {
        // Activities stats
        $totalActivities = Activity::count();
        $ongoingActivities = Activity::where('status', 'ongoing')->count();
        $completedActivities = Activity::where('status', 'completed')->count();

        // Funds stats
        $activeFunds = Fund::where('status', 'active')->count();
        $totalRaised = Fund::sum('current_amount') ?? 0;
        $totalTarget = Fund::sum('target_amount') ?? 0;

        // Scrap sales stats
        $totalSales = ScrapSale::sum('total_price') ?? 0;
        $totalScrapQuantity = ScrapSale::sum('quantity') ?? 0;
        $latestMonthSales = ScrapSale::whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->sum('total_price') ?? 0;

        // Donation distributions stats
        $totalDistributions = DonationDistribution::count();
        $totalRecipientsHelped = DonationDistribution::sum('recipient_count') ?? 0;

        return response()->json([
            'activities' => [
                'total' => $totalActivities,
                'ongoing' => $ongoingActivities,
                'completed' => $completedActivities,
            ],
            'funds' => [
                'active_funds' => $activeFunds,
                'total_raised' => round($totalRaised, 2),
                'total_target' => round($totalTarget, 2),
                'progress_percentage' => $totalTarget > 0 ? round(($totalRaised / $totalTarget) * 100, 2) : 0,
            ],
            'scrap_sales' => [
                'total_sales' => round($totalSales, 2),
                'total_quantity' => $totalScrapQuantity,
                'this_month_sales' => round($latestMonthSales, 2),
            ],
            'donations' => [
                'total_distributions' => $totalDistributions,
                'total_recipients' => $totalRecipientsHelped,
            ],
        ]);
    }

    /**
     * Get monthly revenue chart data
     */
    public function getMonthlyRevenue(Request $request)
    {
        $year = $request->get('year', now()->year);

        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $sales = ScrapSale::whereMonth('sale_date', $month)
                ->whereYear('sale_date', $year)
                ->sum('total_price') ?? 0;

            $monthlyData[] = [
                'month' => $month,
                'amount' => round($sales, 2),
            ];
        }

        return response()->json($monthlyData);
    }

    public function revenueYears()
    {
        $years = ScrapSale::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return response()->json($years);
    }

    /**
     * Get recent activities for dashboard
     */
    public function getRecentActivities()
    {
        $recentActivities = Activity::with('createdBy')
            ->latest()
            ->take(5)
            ->get();

        return response()->json($recentActivities);
    }

    /**
     * Get recent distributions
     */
    public function getRecentDistributions()
    {
        $recentDistributions = DonationDistribution::with('items', 'createdBy')
            ->latest()
            ->take(5)
            ->get();

        return response()->json($recentDistributions);
    }
}
