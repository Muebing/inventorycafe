<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $stats = $this->dashboardService->getStats();
        $chartData = $this->dashboardService->getChartData();
        $monthlySummary = $this->dashboardService->getMonthlySummary();
        $recentTransactions = $this->dashboardService->getRecentTransactions();
        $topItems = $this->dashboardService->getTopItems();

        return view('dashboard.index', compact(
            'stats',
            'chartData',
            'monthlySummary',
            'recentTransactions',
            'topItems'
        ));
    }
}
