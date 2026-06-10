<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function getAll()
    {
        return Category::latest()->get();
    }

    public function findById(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function create(array $data): Category
    {
        $category = Category::create($data);

        ActivityLogService::log('create', 'Kategori '.$category->nama_kategori.' ditambahkan', Category::class, $category->id);

        return $category;
    }

    public function update(int $id, array $data): Category
    {
        $category = $this->findById($id);
        $category->update($data);

        ActivityLogService::log('update', 'Kategori '.$category->nama_kategori.' diperbarui', Category::class, $category->id);

        return $category;
    }

    public function delete(int $id): void
    {
        $category = $this->findById($id);
        $category->delete();

        ActivityLogService::log('delete', 'Kategori '.$category->nama_kategori.' dihapus', Category::class, $id);
    }
}
