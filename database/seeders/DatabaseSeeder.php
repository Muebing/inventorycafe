<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Inventory',
            'email' => 'admin@inventorycafe.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $categories = [
            ['nama_kategori' => 'Bahan Baku'],
            ['nama_kategori' => 'Bahan Kemasan'],
            ['nama_kategori' => 'Alat & Perlengkapan'],
            ['nama_kategori' => 'Minuman'],
            ['nama_kategori' => 'Snack & Makanan Ringan'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        $suppliers = [
            ['nama_supplier' => 'PT Bahan Baku Utama', 'alamat' => 'Jakarta', 'kontak' => '021-12345678'],
            ['nama_supplier' => 'CV Kemasan Indah', 'alamat' => 'Bandung', 'kontak' => '022-87654321'],
            ['nama_supplier' => 'UD Perlengkapan Kita', 'alamat' => 'Surabaya', 'kontak' => '031-11223344'],
            ['nama_supplier' => 'PT Minuman Segar', 'alamat' => 'Semarang', 'kontak' => '024-99887766'],
        ];

        foreach ($suppliers as $sup) {
            Supplier::create($sup);
        }

        $items = [
            ['nama_item' => 'Kopi Arabika', 'kategori_id' => 1, 'satuan' => 'kg', 'stok' => 25, 'stok_minimum' => 10],
            ['nama_item' => 'Gula Pasir', 'kategori_id' => 1, 'satuan' => 'kg', 'stok' => 50, 'stok_minimum' => 15],
            ['nama_item' => 'Susu Cair', 'kategori_id' => 1, 'satuan' => 'liter', 'stok' => 30, 'stok_minimum' => 10],
            ['nama_item' => 'Cup Gelas 16oz', 'kategori_id' => 2, 'satuan' => 'pcs', 'stok' => 500, 'stok_minimum' => 100],
            ['nama_item' => 'Lid Cup', 'kategori_id' => 2, 'satuan' => 'pcs', 'stok' => 450, 'stok_minimum' => 100],
            ['nama_item' => 'Sedotan', 'kategori_id' => 2, 'satuan' => 'pcs', 'stok' => 3, 'stok_minimum' => 200],
            ['nama_item' => 'Mesin Kopi', 'kategori_id' => 3, 'satuan' => 'pcs', 'stok' => 2, 'stok_minimum' => 1],
            ['nama_item' => 'Air Mineral', 'kategori_id' => 4, 'satuan' => 'pcs', 'stok' => 0, 'stok_minimum' => 50],
            ['nama_item' => 'Kentang Goreng', 'kategori_id' => 5, 'satuan' => 'kg', 'stok' => 8, 'stok_minimum' => 5],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
