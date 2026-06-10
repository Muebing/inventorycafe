<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockOutRequest;
use App\Models\Item;
use App\Services\StockOutService;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
    protected StockOutService $stockOutService;

    public function __construct(StockOutService $stockOutService)
    {
        $this->stockOutService = $stockOutService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['barang_id', 'tujuan', 'tanggal_mulai', 'tanggal_selesai']);
        $stockOuts = $this->stockOutService->getAll($filters);
        $items = Item::all();

        return view('stock_out.index', compact('stockOuts', 'items'));
    }

    public function store(StoreStockOutRequest $request)
    {
        try {
            $this->stockOutService->create($request->validated());

            return redirect()->route('stock-out.index')
                ->with('success', 'Barang keluar berhasil dicatat.');
        } catch (\Exception $e) {
            return redirect()->route('stock-out.index')
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->stockOutService->delete($id);

            return redirect()->route('stock-out.index')
                ->with('error', 'Data barang keluar berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('stock-out.index')
                ->with('error', $e->getMessage());
        }
    }
}
