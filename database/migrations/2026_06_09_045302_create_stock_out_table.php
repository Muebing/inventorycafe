<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_out', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('items')->onDelete('cascade');
            $table->integer('jumlah');
            $table->string('tujuan', 50);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_out');
    }
};
