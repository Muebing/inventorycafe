<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockInRequest;
use App\Models\Item;
use App\Models\Supplier;
use App\Services\StockInService;
use Illuminate\Http\Request;

class StockInController extends Controller
{
    protected StockInService $stockInService;

    public function __construct(StockInService $stockInService)
    {
        $this->stockInService = $stockInService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['barang_id', 'tanggal_mulai', 'tanggal_selesai']);
        $stockIns = $this->stockInService->getAll($filters);
        $items = Item::all();
        $suppliers = Supplier::all();

        return view('stock_in.index', compact('stockIns', 'items', 'suppliers'));
    }

    public function store(StoreStockInRequest $request)
    {
        try {
            $this->stockInService->create($request->validated());

            return redirect()->route('stock-in.index')
                ->with('success', 'Barang masuk berhasil dicatat.');
        } catch (\Exception $e) {
            return redirect()->route('stock-in.index')
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->stockInService->delete($id);

            return redirect()->route('stock-in.index')
                ->with('success', 'Data barang masuk berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('stock-in.index')
                ->with('error', $e->getMessage());
        }
    }
}
