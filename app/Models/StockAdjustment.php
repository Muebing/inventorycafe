<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    protected $table = 'stock_adjustments';

    protected $fillable = ['barang_id', 'jumlah_penyesuaian', 'keterangan', 'tanggal'];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'barang_id');
    }
}
