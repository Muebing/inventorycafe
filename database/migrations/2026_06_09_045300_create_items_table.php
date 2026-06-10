<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('nama_item', 100);
            $table->foreignId('kategori_id')->constrained('categories')->onDelete('cascade');
            $table->string('satuan', 20);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
