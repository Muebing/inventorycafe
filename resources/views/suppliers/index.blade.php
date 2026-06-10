@extends('layouts.app')

@section('title', 'Supplier')
@section('page_title', 'Supplier')

@section('content')
    <div class="card-custom">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <span><i class="bi bi-truck me-1" style="color:var(--primary)"></i> Daftar Supplier</span>
            <button class="btn btn-custom-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-lg me-1"></i>Tambah
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th>Nama Supplier</th>
                            <th>Alamat</th>
                            <th style="width:130px">Kontak</th>
                            <th style="width:130px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>{{ $supplier->nama_supplier }}</td>
                                <td class="text-muted">{{ $supplier->alamat ?? '-' }}</td>
                                <td>{{ $supplier->kontak ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-custom-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $supplier->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin hapus supplier ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-custom-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada supplier</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade modal-custom" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Supplier</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                            <input type="text" name="nama_supplier" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kontak</label>
                            <input type="text" name="kontak" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-custom-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($suppliers as $supplier)
        <div class="modal fade modal-custom" id="editModal{{ $supplier->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Supplier</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                                <input type="text" name="nama_supplier" class="form-control" value="{{ $supplier->nama_supplier }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="2">{{ $supplier->alamat }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kontak</label>
                                <input type="text" name="kontak" class="form-control" value="{{ $supplier->kontak }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-custom-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
