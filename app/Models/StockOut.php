<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOut extends Model
{
    protected $table = 'stock_out';

    protected $fillable = ['barang_id', 'jumlah', 'tujuan', 'tanggal'];

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
