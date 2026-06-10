<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Services\ItemService;

class ItemController extends Controller
{
    protected ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    public function index()
    {
        $filters = request()->only(['search', 'kategori_id']);
        $items = $this->itemService->getAll($filters);
        $categories = Category::all();

        return view('items.index', compact('items', 'categories'));
    }

    public function store(StoreItemRequest $request)
    {
        $this->itemService->create($request->validated());

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(UpdateItemRequest $request, int $id)
    {
        $this->itemService->update($id, $request->validated());

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $this->itemService->delete($id);

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}
