@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
    @php
        $alerts = array_merge(
            $stats['low_stock_items']->map(fn($i) => ['item' => $i, 'type' => 'low'])->toArray(),
            $stats['out_of_stock_items']->map(fn($i) => ['item' => $i, 'type' => 'out'])->toArray()
        );
    @endphp

    @if (count($alerts) > 0)
        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2"
             style="border-radius:12px;border:none;background:#FFF8E1;color:#E65100">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>
                <strong>{{ count($alerts) }} barang</strong> perlu perhatian
                (stok menipis / habis)
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-md">
            <div class="stat-card stat-primary" style="padding:1rem">
                <div>
                    <div class="stat-number" style="font-size:1.5rem">{{ $stats['total_barang'] }}</div>
                    <div class="stat-label" style="font-size:.75rem">Total Barang</div>
                </div>
                <i class="bi bi-box-seam-fill" style="font-size:1.8rem;opacity:.3"></i>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="stat-card stat-warning" style="padding:1rem">
                <div>
                    <div class="stat-number" style="font-size:1.5rem">{{ $stats['low_stock'] }}</div>
                    <div class="stat-label" style="font-size:.75rem">Stok Menipis</div>
                </div>
                <i class="bi bi-exclamation-triangle-fill" style="font-size:1.8rem;opacity:.3"></i>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="stat-card stat-danger" style="padding:1rem">
                <div>
                    <div class="stat-number" style="font-size:1.5rem">{{ $stats['out_of_stock'] }}</div>
                    <div class="stat-label" style="font-size:.75rem">Stok Habis</div>
                </div>
                <i class="bi bi-x-octagon-fill" style="font-size:1.8rem;opacity:.3"></i>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="stat-card" style="padding:1rem;background:linear-gradient(135deg,#1565C0,#42A5F5)">
                <div>
                    <div class="stat-number" style="font-size:1.5rem">{{ $stats['total_kategori'] }}</div>
                    <div class="stat-label" style="font-size:.75rem">Kategori</div>
                </div>
                <i class="bi bi-tags-fill" style="font-size:1.8rem;opacity:.3"></i>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="stat-card" style="padding:1rem;background:linear-gradient(135deg,#6A1B9A,#AB47BC)">
                <div>
                    <div class="stat-number" style="font-size:1.5rem">{{ $stats['total_supplier'] }}</div>
                    <div class="stat-label" style="font-size:.75rem">Supplier</div>
                </div>
                <i class="bi bi-truck" style="font-size:1.8rem;opacity:.3"></i>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-bar-chart-line-fill me-1" style="color:var(--primary)"></i>
                        Grafik Transaksi (6 Bulan)
                    </span>
                    <div class="d-flex gap-3 small">
                        <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#43A047;vertical-align:middle;margin-right:4px"></span> Masuk</span>
                        <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#E53935;vertical-align:middle;margin-right:4px"></span> Keluar</span>
                    </div>
                </div>
                <div class="card-body p-3" style="position:relative">
                    <canvas id="transactionChart" height="280"></canvas>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <div class="card-custom">
                        <div class="card-header py-2">
                            <small><i class="bi bi-arrow-down-circle-fill me-1" style="color:#43A047"></i> Terbanyak Masuk</small>
                        </div>
                        <div class="card-body p-2" style="max-height:180px;overflow-y:auto">
                            @forelse ($topItems['top_masuk'] as $item)
                                <div class="d-flex justify-content-between align-items-center py-1 px-2 border-bottom" style="font-size:.82rem">
                                    <span>{{ $item['nama'] }}</span>
                                    <span class="badge-custom" style="background:#E8F5E9;color:#2E7D32"><strong>{{ $item['total'] }}</strong></span>
                                </div>
                            @empty
                                <p class="text-muted small text-center py-2 mb-0">Belum ada data</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-custom">
                        <div class="card-header py-2">
                            <small><i class="bi bi-arrow-up-circle-fill me-1" style="color:#E53935"></i> Terbanyak Keluar</small>
                        </div>
                        <div class="card-body p-2" style="max-height:180px;overflow-y:auto">
                            @forelse ($topItems['top_keluar'] as $item)
                                <div class="d-flex justify-content-between align-items-center py-1 px-2 border-bottom" style="font-size:.82rem">
                                    <span>{{ $item['nama'] }}</span>
                                    <span class="badge-custom" style="background:#FFEBEE;color:#C62828"><strong>{{ $item['total'] }}</strong></span>
                                </div>
                            @empty
                                <p class="text-muted small text-center py-2 mb-0">Belum ada data</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-custom mb-3">
                <div class="card-header py-2">
                    <small><i class="bi bi-calendar3 me-1" style="color:var(--accent)"></i> Ringkasan Bulan Ini</small>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-2 rounded" style="background:#E8F5E9">
                                <div style="font-size:1.25rem;font-weight:700;color:#2E7D32">{{ $monthlySummary['masuk_bulan_ini'] }}</div>
                                <small class="text-muted">Transaksi Masuk</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded" style="background:#FFEBEE">
                                <div style="font-size:1.25rem;font-weight:700;color:#C62828">{{ $monthlySummary['keluar_bulan_ini'] }}</div>
                                <small class="text-muted">Transaksi Keluar</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded" style="background:#E8F5E9">
                                <div style="font-size:1.25rem;font-weight:700;color:#2E7D32">{{ number_format($monthlySummary['total_masuk_bulan_ini']) }}</div>
                                <small class="text-muted">Total Item Masuk</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded" style="background:#FFEBEE">
                                <div style="font-size:1.25rem;font-weight:700;color:#C62828">{{ number_format($monthlySummary['total_keluar_bulan_ini']) }}</div>
                                <small class="text-muted">Total Item Keluar</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-custom">
                <div class="card-header py-2">
                    <small><i class="bi bi-bell-fill me-1" style="color:var(--accent)"></i> Notifikasi Stok</small>
                </div>
                <div class="card-body p-2" style="max-height:280px;overflow-y:auto">
                    @forelse ($stats['low_stock_items'] as $item)
                        <div class="d-flex align-items-center gap-2 mb-1 p-2 rounded" style="background:#FFF8E1">
                            <i class="bi bi-exclamation-circle" style="color:#F57F17;font-size:.85rem"></i>
                            <div style="font-size:.8rem">
                                <strong>{{ $item->nama_item }}</strong>
                                <span class="text-muted"> Stok: {{ $item->stok }} (Min: {{ $item->stok_minimum }})</span>
                            </div>
                        </div>
                    @empty
                        @if (count($stats['out_of_stock_items']) === 0)
                            <p class="text-muted small text-center py-2 mb-0">Semua stok aman</p>
                        @endif
                    @endforelse
                    @foreach ($stats['out_of_stock_items'] as $item)
                        <div class="d-flex align-items-center gap-2 mb-1 p-2 rounded" style="background:#FFEBEE">
                            <i class="bi bi-x-circle" style="color:#C62828;font-size:.85rem"></i>
                            <div style="font-size:.8rem">
                                <strong>{{ $item->nama_item }}</strong>
                                <span class="text-danger"> Stok habis!</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-arrow-left-right me-1" style="color:var(--primary)"></i> Transaksi Terbaru</span>
            <small class="text-muted">{{ count($recentTransactions) }} transaksi terakhir</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width:80px">Tipe</th>
                            <th>Barang</th>
                            <th style="width:80px">Jumlah</th>
                            <th style="width:110px">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTransactions as $tx)
                            <tr>
                                <td>
                                    @if ($tx['type'] === 'Masuk')
                                        <span class="badge-custom" style="background:#E8F5E9;color:#2E7D32">
                                            <i class="bi bi-arrow-down me-1"></i>Masuk
                                        </span>
                                    @else
                                        <span class="badge-custom" style="background:#FFEBEE;color:#C62828">
                                            <i class="bi bi-arrow-up me-1"></i>Keluar
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $tx['item'] }}</td>
                                <td><strong>{{ $tx['jumlah'] }}</strong></td>
                                <td class="text-muted">{{ $tx['tanggal'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada transaksi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('transactionChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartData['labels']),
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: @json($chartData['masuk']),
                        backgroundColor: 'rgba(67,160,71,.7)',
                        borderColor: '#43A047',
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: .35,
                    },
                    {
                        label: 'Barang Keluar',
                        data: @json($chartData['keluar']),
                        backgroundColor: 'rgba(229,57,53,.7)',
                        borderColor: '#E53935',
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: .35,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#333',
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        cornerRadius: 8,
                        padding: 10
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,.05)', drawBorder: false },
                        ticks: { font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }
</script>
@endpush
