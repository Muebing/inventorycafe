<?php

namespace App\Services;

use App\Models\Supplier;

class SupplierService
{
    public function getAll()
    {
        return Supplier::latest()->get();
    }

    public function findById(int $id): Supplier
    {
        return Supplier::findOrFail($id);
    }

    public function create(array $data): Supplier
    {
        $supplier = Supplier::create($data);

        ActivityLogService::log('create', 'Supplier '.$supplier->nama_supplier.' ditambahkan', Supplier::class, $supplier->id);

        return $supplier;
    }

    public function update(int $id, array $data): Supplier
    {
        $supplier = $this->findById($id);
        $supplier->update($data);

        ActivityLogService::log('update', 'Supplier '.$supplier->nama_supplier.' diperbarui', Supplier::class, $supplier->id);

        return $supplier;
    }

    public function delete(int $id): void
    {
        $supplier = $this->findById($id);
        $supplier->delete();

        ActivityLogService::log('delete', 'Supplier '.$supplier->nama_supplier.' dihapus', Supplier::class, $id);
    }
}
