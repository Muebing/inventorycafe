<?php

namespace App\Services;

use App\Models\Item;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;

class StockAdjustmentService
{
    public function getAll(array $filters = [])
    {
        $query = StockAdjustment::with('barang');

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

    public function findById(int $id): StockAdjustment
    {
        return StockAdjustment::with('barang')->findOrFail($id);
    }

    public function create(array $data): StockAdjustment
    {
        return DB::transaction(function () use ($data) {
            $adjustment = StockAdjustment::create($data);

            $item = Item::findOrFail($data['barang_id']);
            $item->increment('stok', $data['jumlah_penyesuaian']);

            $sign = $data['jumlah_penyesuaian'] >= 0 ? '+' : '';
            ActivityLogService::log(
                'create',
                "Penyesuaian stok: {$item->nama_item} ({$sign}{$data['jumlah_penyesuaian']}) - {$data['keterangan']}",
                StockAdjustment::class,
                $adjustment->id
            );

            return $adjustment;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $adjustment = $this->findById($id);

            $item = Item::findOrFail($adjustment->barang_id);
            $item->decrement('stok', $adjustment->jumlah_penyesuaian);

            $adjustment->delete();

            ActivityLogService::log(
                'delete',
                "Penyesuaian stok dihapus: {$item->nama_item}",
                StockAdjustment::class,
                $id
            );
        });
    }
}
