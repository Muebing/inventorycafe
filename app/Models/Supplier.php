<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $table = 'suppliers';

    protected $fillable = ['nama_supplier', 'alamat', 'kontak'];

    public function stockIn(): HasMany
    {
        return $this->hasMany(StockIn::class, 'supplier_id');
    }
}
