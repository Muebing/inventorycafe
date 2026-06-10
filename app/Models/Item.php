<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $table = 'items';

    protected $fillable = ['nama_item', 'kategori_id', 'satuan', 'stok', 'stok_minimum'];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    public function stockIn(): HasMany
    {
        return $this->hasMany(StockIn::class, 'barang_id');
    }

    public function stockOut(): HasMany
    {
        return $this->hasMany(StockOut::class, 'barang_id');
    }

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class, 'barang_id');
    }

    public function isLowStock(): bool
    {
        return $this->stok <= $this->stok_minimum && $this->stok > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->stok <= 0;
    }
}
