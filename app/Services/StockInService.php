<?php

namespace App\Services;

use App\Models\Item;
use App\Models\StockIn;
use Illuminate\Support\Facades\DB;

class StockInService
{
    public function getAll(array $filters = [])
    {
        $query = StockIn::with('barang', 'supplier');

        if (! empty($filters['barang_id'])) {
            $query->where('barang_id', $filters['barang_id']);
        }

        if (! empty($filters['tanggal_mulai'])) {
            $query->whereDate('tanggal', '>=', $filters['tanggal_mulai']);
        }

        if (! empty($filters['tanggal_selesai'])) {
            $query->whereDate('tanggal', '<=', $filters['tanggal_selesai']);
        }

        return $query->latest()->get();
    }

    public function findById(int $id): StockIn
    {
        return StockIn::with('barang', 'supplier')->findOrFail($id);
    }

    public function create(array $data): StockIn
    {
        return DB::transaction(function () use ($data) {
            $stockIn = StockIn::create($data);

            $item = Item::findOrFail($data['barang_id']);
            $item->increment('stok', $data['jumlah']);

            ActivityLogService::log(
                'create',
                "Barang masuk: {$item->nama_item} ({$data['jumlah']}) dari supplier",
                StockIn::class,
                $stockIn->id
            );

            return $stockIn;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $stockIn = $this->findById($id);

            $item = Item::findOrFail($stockIn->barang_id);
            $item->decrement('stok', $stockIn->jumlah);

            $stockIn->delete();

            ActivityLogService::log(
                'delete',
                "Barang masuk dihapus: {$item->nama_item} ({$stockIn->jumlah})",
                StockIn::class,
                $id
            );
        });
    }
}
