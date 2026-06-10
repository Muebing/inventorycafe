<?php

namespace App\Services;

use App\Models\Item;
use App\Models\StockOut;
use Illuminate\Support\Facades\DB;

class StockOutService
{
    public function getAll(array $filters = [])
    {
        $query = StockOut::with('barang');

        if (! empty($filters['barang_id'])) {
            $query->where('barang_id', $filters['barang_id']);
        }

        if (! empty($filters['tujuan'])) {
            $query->where('tujuan', $filters['tujuan']);
        }

        if (! empty($filters['tanggal_mulai'])) {
            $query->whereDate('tanggal', '>=', $filters['tanggal_mulai']);
        }

        if (! empty($filters['tanggal_selesai'])) {
            $query->whereDate('tanggal', '<=', $filters['tanggal_selesai']);
        }

        return $query->latest()->get();
    }

    public function findById(int $id): StockOut
    {
        return StockOut::with('barang')->findOrFail($id);
    }

    public function create(array $data): StockOut
    {
        return DB::transaction(function () use ($data) {
            $item = Item::findOrFail($data['barang_id']);

            if ($item->stok < $data['jumlah']) {
                throw new \Exception("Stok {$item->nama_item} tidak mencukupi. Tersedia: {$item->stok}");
            }

            $stockOut = StockOut::create($data);

            $item->decrement('stok', $data['jumlah']);

            ActivityLogService::log(
                'create',
                "Barang keluar: {$item->nama_item} ({$data['jumlah']}) untuk {$data['tujuan']}",
                StockOut::class,
                $stockOut->id
            );

            return $stockOut;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $stockOut = $this->findById($id);

            $item = Item::findOrFail($stockOut->barang_id);
            $item->increment('stok', $stockOut->jumlah);

            $stockOut->delete();

            ActivityLogService::log(
                'delete',
                "Barang keluar dihapus: {$item->nama_item} ({$stockOut->jumlah})",
                StockOut::class,
                $id
            );
        });
    }
}
