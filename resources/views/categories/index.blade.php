@extends('layouts.app')

@section('title', 'Kategori')
@section('page_title', 'Kategori')

@section('content')
    <div class="card-custom">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <span><i class="bi bi-tags-fill me-1" style="color:var(--primary)"></i> Daftar Kategori</span>
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
                            <th>Nama Kategori</th>
                            <th style="width:120px">Jumlah Barang</th>
                            <th style="width:130px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>{{ $category->nama_kategori }}</td>
                                <td><span class="badge-custom" style="background:var(--primary-bg);color:var(--primary)">{{ $category->items->count() }} barang</span></td>
                                <td>
                                    <button class="btn btn-custom-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin hapus kategori {{ $category->nama_kategori }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-custom-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada kategori</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade modal-custom" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Kategori</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-custom-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($categories as $category)
        <div class="modal fade modal-custom" id="editModal{{ $category->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('categories.update', $category->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Kategori</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                            <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" class="form-control" value="{{ $category->nama_kategori }}" required>
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
