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
        // Create or update admin account
        User::updateOrCreate(
            ['email' => 'admin@inventorycafe.test'],
            [
                'name' => 'Admin Inventory',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        // Categories
        $categories = [
            'Bahan Baku',
            'Bahan Kemasan',
            'Alat & Perlengkapan',
            'Minuman',
            'Snack & Makanan Ringan',
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate([
                'nama_kategori' => $categoryName,
            ]);
        }

        // Suppliers
        $suppliers = [
            ['nama_supplier' => 'PT Bahan Baku Utama', 'alamat' => 'Jakarta', 'kontak' => '021-12345678'],
            ['nama_supplier' => 'CV Kemasan Indah', 'alamat' => 'Bandung', 'kontak' => '022-87654321'],
            ['nama_supplier' => 'UD Perlengkapan Kita', 'alamat' => 'Surabaya', 'kontak' => '031-11223344'],
            ['nama_supplier' => 'PT Minuman Segar', 'alamat' => 'Semarang', 'kontak' => '024-99887766'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(
                ['nama_supplier' => $supplier['nama_supplier']],
                $supplier
            );
        }

        // Retrieve category IDs based on category names
        $categoryIds = Category::pluck('id', 'nama_kategori');

        // Items
        $items = [
            ['nama_item' => 'Kopi Arabika', 'kategori_id' => $categoryIds['Bahan Baku'], 'satuan' => 'kg', 'stok' => 25, 'stok_minimum' => 10],
            ['nama_item' => 'Gula Pasir', 'kategori_id' => $categoryIds['Bahan Baku'], 'satuan' => 'kg', 'stok' => 50, 'stok_minimum' => 15],
            ['nama_item' => 'Susu Cair', 'kategori_id' => $categoryIds['Bahan Baku'], 'satuan' => 'liter', 'stok' => 30, 'stok_minimum' => 10],
            ['nama_item' => 'Cup Gelas 16oz', 'kategori_id' => $categoryIds['Bahan Kemasan'], 'satuan' => 'pcs', 'stok' => 500, 'stok_minimum' => 100],
            ['nama_item' => 'Lid Cup', 'kategori_id' => $categoryIds['Bahan Kemasan'], 'satuan' => 'pcs', 'stok' => 450, 'stok_minimum' => 100],
            ['nama_item' => 'Sedotan', 'kategori_id' => $categoryIds['Bahan Kemasan'], 'satuan' => 'pcs', 'stok' => 3, 'stok_minimum' => 200],
            ['nama_item' => 'Mesin Kopi', 'kategori_id' => $categoryIds['Alat & Perlengkapan'], 'satuan' => 'pcs', 'stok' => 2, 'stok_minimum' => 1],
            ['nama_item' => 'Air Mineral', 'kategori_id' => $categoryIds['Minuman'], 'satuan' => 'pcs', 'stok' => 0, 'stok_minimum' => 50],
            ['nama_item' => 'Kentang Goreng', 'kategori_id' => $categoryIds['Snack & Makanan Ringan'], 'satuan' => 'kg', 'stok' => 8, 'stok_minimum' => 5],
        ];

        foreach ($items as $item) {
            Item::firstOrCreate(
                ['nama_item' => $item['nama_item']],
                $item
            );
        }
    }
}
