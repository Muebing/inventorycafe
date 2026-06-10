<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = ['nama_kategori'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'kategori_id');
    }
}
