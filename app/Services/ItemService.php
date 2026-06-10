<?php

namespace App\Services;

use App\Models\Item;

class ItemService
{
    public function getAll(array $filters = [])
    {
        $query = Item::with('kategori');

        if (! empty($filters['search'])) {
            $query->where('nama_item', 'like', '%'.$filters['search'].'%');
        }

        if (! empty($filters['kategori_id'])) {
            $query->where('kategori_id', $filters['kategori_id']);
        }

        return $query->latest()->get();
    }

    public function findById(int $id): Item
    {
        return Item::with('kategori')->findOrFail($id);
    }

    public function create(array $data): Item
    {
        $data['stok'] = $data['stok'] ?? 0;

        $item = Item::create($data);

        ActivityLogService::log('create', 'Barang '.$item->nama_item.' ditambahkan', Item::class, $item->id);

        return $item;
    }

    public function update(int $id, array $data): Item
    {
        $item = $this->findById($id);
        $item->update($data);

        ActivityLogService::log('update', 'Barang '.$item->nama_item.' diperbarui', Item::class, $item->id);

        return $item;
    }

    public function delete(int $id): void
    {
        $item = $this->findById($id);
        $item->delete();

        ActivityLogService::log('delete', 'Barang '.$item->nama_item.' dihapus', Item::class, $id);
    }

    public function getLowStockItems()
    {
        return Item::with('kategori')
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->where('stok', '>', 0)
            ->get();
    }

    public function getOutOfStockItems()
    {
        return Item::with('kategori')
            ->where('stok', '<=', 0)
            ->get();
    }

    public function countTotal(): int
    {
        return Item::count();
    }

    public function countLowStock(): int
    {
        return Item::whereColumn('stok', '<=', 'stok_minimum')
            ->where('stok', '>', 0)
            ->count();
    }

    public function countOutOfStock(): int
    {
        return Item::where('stok', '<=', 0)->count();
    }
}
