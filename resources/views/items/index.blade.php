@extends('layouts.app')

@section('title', 'Barang')
@section('page_title', 'Barang')

@section('content')
    <div class="card-custom">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <span><i class="bi bi-box-seam-fill me-1" style="color:var(--primary)"></i> Daftar Barang</span>
            <button class="btn btn-custom-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-lg me-1"></i>Tambah
            </button>
        </div>
        <div class="card-body p-3">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white" style="border-right:none"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" style="border-left:none" placeholder="Cari nama barang..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="kategori_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('kategori_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-1">
                    <button type="submit" class="btn btn-custom-primary btn-sm flex-fill"><i class="bi bi-filter me-1"></i>Filter</button>
                    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary btn-sm flex-fill"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width:45px">No</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th style="width:70px">Satuan</th>
                            <th style="width:80px">Stok</th>
                            <th style="width:90px">Min</th>
                            <th style="width:90px">Status</th>
                            <th style="width:130px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>{{ $item->nama_item }}</td>
                                <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                <td>{{ $item->satuan }}</td>
                                <td><strong>{{ number_format($item->stok) }}</strong></td>
                                <td class="text-muted">{{ number_format($item->stok_minimum) }}</td>
                                <td>
                                    @if ($item->isOutOfStock())
                                        <span class="badge-custom" style="background:#FFEBEE;color:#C62828">Habis</span>
                                    @elseif ($item->isLowStock())
                                        <span class="badge-custom" style="background:#FFF8E1;color:#E65100">Menipis</span>
                                    @else
                                        <span class="badge-custom" style="background:#E8F5E9;color:#2E7D32">Aman</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-custom-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin hapus {{ $item->nama_item }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-custom-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada barang</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade modal-custom" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('items.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Barang</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_item" class="form-control" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori_id" class="form-select" required>
                                    <option value="">Pilih</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Satuan <span class="text-danger">*</span></label>
                                <select name="satuan" class="form-select" required>
                                    <option value="">Pilih</option>
                                    <option value="kg">Kg</option>
                                    <option value="pcs">Pcs</option>
                                    <option value="liter">Liter</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Stok Awal</label>
                                <input type="number" name="stok" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                                <input type="number" name="stok_minimum" class="form-control" value="5" min="0" required>
                            </div>
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

    @foreach ($items as $item)
        <div class="modal fade modal-custom" id="editModal{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('items.update', $item->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Barang</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" name="nama_item" class="form-control" value="{{ $item->nama_item }}" required>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select name="kategori_id" class="form-select" required>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ $item->kategori_id == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Satuan <span class="text-danger">*</span></label>
                                    <select name="satuan" class="form-select" required>
                                        <option value="kg" {{ $item->satuan == 'kg' ? 'selected' : '' }}>Kg</option>
                                        <option value="pcs" {{ $item->satuan == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                        <option value="liter" {{ $item->satuan == 'liter' ? 'selected' : '' }}>Liter</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Stok Saat Ini</label>
                                <input type="text" class="form-control" value="{{ $item->stok }}" readonly style="background:#f9f9f9">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                                <input type="number" name="stok_minimum" class="form-control" value="{{ $item->stok_minimum }}" min="0" required>
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
