<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockIn extends Model
{
    protected $table = 'stock_in';

    protected $fillable = ['barang_id', 'supplier_id', 'jumlah', 'tanggal'];

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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
