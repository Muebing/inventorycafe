@extends('layouts.app')

@section('title', 'Stock Adjustment')
@section('page_title', 'Stock Adjustment')

@section('content')
    <div class="card-custom">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <span><i class="bi bi-sliders me-1" style="color:var(--primary)"></i> Riwayat Penyesuaian Stok</span>
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
                <div class="col-md-3"><input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}"></div>
                <div class="col-md-3"><input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}"></div>
                <div class="col-md-3 d-flex gap-1">
                    <button type="submit" class="btn btn-custom-primary btn-sm flex-fill"><i class="bi bi-funnel me-1"></i>Filter</button>
                    <a href="{{ route('stock-adjustments.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width:45px">No</th>
                            <th>Barang</th>
                            <th style="width:110px">Penyesuaian</th>
                            <th>Keterangan</th>
                            <th style="width:110px">Tanggal</th>
                            <th style="width:70px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($adjustments as $adjustment)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>{{ $adjustment->barang->nama_item ?? '-' }}</td>
                                <td>
                                    <span class="badge-custom" style="background:{{ $adjustment->jumlah_penyesuaian >= 0 ? '#E8F5E9;color:#2E7D32' : '#FFEBEE;color:#C62828' }}">
                                        {{ $adjustment->jumlah_penyesuaian >= 0 ? '+' : '' }}{{ number_format($adjustment->jumlah_penyesuaian) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $adjustment->keterangan ?? '-' }}</td>
                                <td>{{ $adjustment->tanggal->format('d/m/Y') }}</td>
                                <td>
                                    <form action="{{ route('stock-adjustments.destroy', $adjustment->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus penyesuaian ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-custom-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada penyesuaian stok</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade modal-custom" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('stock-adjustments.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Penyesuaian Stok</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
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
                        <div class="mb-3">
                            <label class="form-label">Jumlah Penyesuaian <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_penyesuaian" class="form-control"
                                   placeholder="10 (tambah) atau -5 (kurang)" required>
                            <small class="text-muted">Gunakan angka positif untuk menambah stok, negatif untuk mengurangi</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Alasan penyesuaian"></textarea>
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
