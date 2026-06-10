<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockAdjustmentRequest;
use App\Models\Item;
use App\Services\StockAdjustmentService;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    protected StockAdjustmentService $adjustmentService;

    public function __construct(StockAdjustmentService $adjustmentService)
    {
        $this->adjustmentService = $adjustmentService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['barang_id', 'tanggal_mulai', 'tanggal_selesai']);
        $adjustments = $this->adjustmentService->getAll($filters);
        $items = Item::all();

        return view('stock_adjustments.index', compact('adjustments', 'items'));
    }

    public function store(StoreStockAdjustmentRequest $request)
    {
        try {
            $this->adjustmentService->create($request->validated());

            return redirect()->route('stock-adjustments.index')
                ->with('success', 'Penyesuaian stok berhasil dicatat.');
        } catch (\Exception $e) {
            return redirect()->route('stock-adjustments.index')
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->adjustmentService->delete($id);

            return redirect()->route('stock-adjustments.index')
                ->with('success', 'Data penyesuaian berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('stock-adjustments.index')
                ->with('error', $e->getMessage());
        }
    }
}
