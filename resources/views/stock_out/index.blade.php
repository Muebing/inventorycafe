@extends('layouts.app')

@section('title', 'Barang Keluar')
@section('page_title', 'Barang Keluar')

@section('content')
    <div class="card-custom">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <span><i class="bi bi-arrow-up-circle-fill me-1" style="color:var(--primary)"></i> Riwayat Barang Keluar</span>
            <button class="btn btn-custom-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-lg me-1"></i>Tambah
            </button>
        </div>
        <div class="card-body p-3">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <select name="barang_id" class="form-select">
                        <option value="">Semua Barang</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" {{ request('barang_id') == $item->id ? 'selected' : '' }}>{{ $item->nama_item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tujuan" class="form-select">
                        <option value="">Semua Tujuan</option>
                        <option value="produksi" {{ request('tujuan') == 'produksi' ? 'selected' : '' }}>Produksi</option>
                        <option value="penjualan" {{ request('tujuan') == 'penjualan' ? 'selected' : '' }}>Penjualan</option>
                    </select>
                </div>
                <div class="col-md-2"><input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}"></div>
                <div class="col-md-2"><input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}"></div>
                <div class="col-md-3 d-flex gap-1">
                    <button type="submit" class="btn btn-custom-primary btn-sm flex-fill"><i class="bi bi-funnel me-1"></i>Filter</button>
                    <a href="{{ route('stock-out.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width:45px">No</th>
                            <th>Barang</th>
                            <th style="width:80px">Jumlah</th>
                            <th style="width:100px">Tujuan</th>
                            <th style="width:110px">Tanggal</th>
                            <th style="width:70px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockOuts as $stockOut)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>{{ $stockOut->barang->nama_item ?? '-' }}</td>
                                <td><strong>{{ number_format($stockOut->jumlah) }}</strong></td>
                                <td>
                                    <span class="badge-custom" style="background:{{ $stockOut->tujuan == 'produksi' ? '#E3F2FD;color:#1565C0' : '#E8F5E9;color:#2E7D32' }}">
                                        {{ ucfirst($stockOut->tujuan) }}
                                    </span>
                                </td>
                                <td>{{ $stockOut->tanggal->format('d/m/Y') }}</td>
                                <td>
                                    <form action="{{ route('stock-out.destroy', $stockOut->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus? Stok akan ditambahkan kembali.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-custom-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada barang keluar</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade modal-custom" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('stock-out.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Barang Keluar</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Barang <span class="text-danger">*</span></label>
                            <select name="barang_id" class="form-select" required>
                                <option value="">Pilih Barang</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_item }} (Stok: {{ $item->stok }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" name="jumlah" class="form-control" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tujuan <span class="text-danger">*</span></label>
                                <select name="tujuan" class="form-select" required>
                                    <option value="">Pilih</option>
                                    <option value="produksi">Produksi</option>
                                    <option value="penjualan">Penjualan</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
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
@endsection
