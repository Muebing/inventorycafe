<?php

namespace App\Services;

use App\Models\Category;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getStats(): array
    {
        $itemService = app(ItemService::class);

        return [
            'total_barang' => $itemService->countTotal(),
            'low_stock' => $itemService->countLowStock(),
            'out_of_stock' => $itemService->countOutOfStock(),
            'low_stock_items' => $itemService->getLowStockItems(),
            'out_of_stock_items' => $itemService->getOutOfStockItems(),
            'total_kategori' => Category::count(),
            'total_supplier' => Supplier::count(),
        ];
    }

    public function getChartData(): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = date('Y-m', strtotime("-{$i} months"));
        }

        $labels = [];
        $masukData = [];
        $keluarData = [];

        foreach ($months as $month) {
            $yearMonth = explode('-', $month);
            $year = $yearMonth[0];
            $monthNum = $yearMonth[1];

            $labels[] = date('F', mktime(0, 0, 0, (int) $monthNum, 1));

            $totalMasuk = StockIn::whereYear('tanggal', $year)
                ->whereMonth('tanggal', $monthNum)
                ->sum('jumlah');

            $totalKeluar = StockOut::whereYear('tanggal', $year)
                ->whereMonth('tanggal', $monthNum)
                ->sum('jumlah');

            $masukData[] = (int) $totalMasuk;
            $keluarData[] = (int) $totalKeluar;
        }

        return [
            'labels' => $labels,
            'masuk' => $masukData,
            'keluar' => $keluarData,
        ];
    }

    public function getMonthlySummary(): array
    {
        $bulanIni = date('m');
        $tahunIni = date('Y');

        return [
            'masuk_bulan_ini' => StockIn::whereYear('tanggal', $tahunIni)
                ->whereMonth('tanggal', $bulanIni)
                ->count(),
            'keluar_bulan_ini' => StockOut::whereYear('tanggal', $tahunIni)
                ->whereMonth('tanggal', $bulanIni)
                ->count(),
            'total_masuk_bulan_ini' => StockIn::whereYear('tanggal', $tahunIni)
                ->whereMonth('tanggal', $bulanIni)
                ->sum('jumlah'),
            'total_keluar_bulan_ini' => StockOut::whereYear('tanggal', $tahunIni)
                ->whereMonth('tanggal', $bulanIni)
                ->sum('jumlah'),
        ];
    }

    public function getRecentTransactions(int $limit = 10): array
    {
        $stockIn = StockIn::with('barang')
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn ($item) => [
                'type' => 'Masuk',
                'item' => $item->barang?->nama_item ?? '-',
                'jumlah' => $item->jumlah,
                'tanggal' => $item->tanggal,
            ]);

        $stockOut = StockOut::with('barang')
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn ($item) => [
                'type' => 'Keluar',
                'item' => $item->barang?->nama_item ?? '-',
                'jumlah' => $item->jumlah,
                'tanggal' => $item->tanggal,
            ]);

        return $stockIn->concat($stockOut)->sortByDesc('tanggal')->take($limit)->values()->toArray();
    }

    public function getTopItems(): array
    {
        $topMasuk = StockIn::select('barang_id', DB::raw('SUM(jumlah) as total'))
            ->with('barang')
            ->groupBy('barang_id')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(fn ($item) => [
                'nama' => $item->barang?->nama_item ?? '-',
                'total' => $item->total,
            ])
            ->toArray();

        $topKeluar = StockOut::select('barang_id', DB::raw('SUM(jumlah) as total'))
            ->with('barang')
            ->groupBy('barang_id')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(fn ($item) => [
                'nama' => $item->barang?->nama_item ?? '-',
                'total' => $item->total,
            ])
            ->toArray();

        return [
            'top_masuk' => $topMasuk,
            'top_keluar' => $topKeluar,
        ];
    }
}
