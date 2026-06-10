@extends('layouts.app')

@section('title', 'Log Activity')
@section('page_title', 'Log Activity')

@section('content')
    <div class="card-custom">
        <div class="card-header">
            <span><i class="bi bi-clock-history me-1" style="color:var(--primary)"></i> Riwayat Aktivitas</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th style="width:90px">Aktivitas</th>
                            <th>Deskripsi</th>
                            <th style="width:130px">User</th>
                            <th style="width:150px">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}</td>
                                <td>
                                    @if ($log->aktivitas == 'create')
                                        <span class="badge-custom" style="background:#E8F5E9;color:#2E7D32">Create</span>
                                    @elseif ($log->aktivitas == 'update')
                                        <span class="badge-custom" style="background:#FFF8E1;color:#E65100">Update</span>
                                    @elseif ($log->aktivitas == 'delete')
                                        <span class="badge-custom" style="background:#FFEBEE;color:#C62828">Delete</span>
                                    @else
                                        <span class="badge-custom" style="background:#eee;color:#666">{{ $log->aktivitas }}</span>
                                    @endif
                                </td>
                                <td>{{ $log->deskripsi ?? '-' }}</td>
                                <td><i class="bi bi-person me-1 text-muted"></i>{{ $log->user->name ?? '-' }}</td>
                                <td class="text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada aktivitas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($logs->hasPages())
                <div class="p-3 d-flex justify-content-center border-top">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
