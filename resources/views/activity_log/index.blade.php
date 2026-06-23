@extends('layouts.app')

@section('content')
<style>
    body { background-color: #f8fafc !important; }
    .text-emerald-custom { color: #10b981 !important; }
    .bg-emerald-soft { background-color: #ecfdf5 !important; }
    .text-slate-dark { color: #0f172a !important; }
    .text-slate-muted { color: #64748b !important; }
    
    .card-custom { border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .table-custom-header th { background-color: #10b981 !important; color: #ffffff !important; font-weight: 600 !important; border-bottom: 2px solid #059669 !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    
    .log-row { transition: background-color 0.2s ease; }
    .log-row:hover { background-color: #f8fafc; }
    
    /* Action Badges */
    .badge-action { font-size: 0.7rem; padding: 0.35em 0.65em; letter-spacing: 0.5px; font-weight: 600; }
    .action-create { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .action-update { background-color: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }
    .action-delete { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .action-login  { background-color: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }
    .action-other  { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold"><i class="fas fa-shield-alt text-emerald-custom me-2"></i> Audit Trail & Rekam Jejak</h1>
            <p class="text-slate-muted small mb-0 mt-1">Pantau seluruh pergerakan, perubahan data, dan aktivitas staf di dalam sistem Mentari Atlas.</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-light border shadow-sm rounded-pill fw-medium px-4">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card card-custom bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-slate-dark">
                <i class="fas fa-list-ul text-emerald-custom me-2"></i> Daftar Aktivitas Sistem
            </h6>
            <span class="badge bg-emerald-soft text-emerald-custom border border-success border-opacity-25 rounded-pill px-3 py-2">
                <i class="fas fa-clock me-1"></i> Real-time Monitoring
            </span>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive d-none d-lg-block">
                <table class="table align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="table-custom-header">
                        <tr>
                            <th class="py-3 px-4" style="width: 180px;">Waktu & Tanggal</th>
                            <th class="py-3">Pengguna (Aktor)</th>
                            <th class="py-3" style="width: 150px;">Jenis Aksi</th>
                            <th class="py-3 text-start">Deskripsi Aktivitas</th>
                            <th class="py-3 text-center" style="width: 130px;">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr class="log-row border-bottom">
                            <td class="py-3 px-4">
                                <div class="fw-bold text-slate-dark">{{ $log->created_at->format('d/m/Y') }}</div>
                                <div class="text-slate-muted small"><i class="far fa-clock me-1"></i>{{ $log->created_at->format('H:i:s') }} WIB</div>
                            </td>
                            <td>
                                @if($log->user)
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2 border" style="width: 32px; height: 32px;">
                                            <i class="fas fa-user text-secondary" style="font-size: 0.8rem;"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-slate-dark d-block">{{ $log->user->name }}</span>
                                            <span class="text-slate-muted" style="font-size: 0.7rem;">{{ strtoupper(str_replace('_', ' ', $log->user->role)) }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic"><i class="fas fa-robot me-1"></i> Sistem / Auto</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $actionClass = 'action-other';
                                    $actionUpper = strtoupper($log->action);
                                    if(str_contains($actionUpper, 'BUAT') || str_contains($actionUpper, 'TAMBAH')) $actionClass = 'action-create';
                                    elseif(str_contains($actionUpper, 'EDIT') || str_contains($actionUpper, 'UPDATE') || str_contains($actionUpper, 'SETUJU')) $actionClass = 'action-update';
                                    elseif(str_contains($actionUpper, 'HAPUS') || str_contains($actionUpper, 'TOLAK')) $actionClass = 'action-delete';
                                    elseif(str_contains($actionUpper, 'LOGIN')) $actionClass = 'action-login';
                                @endphp
                                <span class="badge badge-action rounded-pill {{ $actionClass }}">
                                    {{ $actionUpper }}
                                </span>
                            </td>
                            <td class="text-start">
                                <span class="text-slate-dark fw-medium">{{ $log->description }}</span>
                            </td>
                            <td class="text-center text-slate-muted" style="font-family: monospace; font-size: 0.8rem;">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-slate-muted bg-white">
                                <i class="fas fa-search-minus fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Belum ada aktivitas yang terekam di dalam sistem.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE CARDS --}}
            <div class="d-lg-none p-3 bg-light">
                @forelse($logs as $log)
                    @php
                        $actionClass = 'action-other';
                        $actionUpper = strtoupper($log->action);
                        if(str_contains($actionUpper, 'BUAT') || str_contains($actionUpper, 'TAMBAH')) $actionClass = 'action-create';
                        elseif(str_contains($actionUpper, 'EDIT') || str_contains($actionUpper, 'UPDATE') || str_contains($actionUpper, 'SETUJU')) $actionClass = 'action-update';
                        elseif(str_contains($actionUpper, 'HAPUS') || str_contains($actionUpper, 'TOLAK')) $actionClass = 'action-delete';
                        elseif(str_contains($actionUpper, 'LOGIN')) $actionClass = 'action-login';
                    @endphp
                    <div class="card card-custom mb-3" style="border-radius: 12px;">
                        <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #e2e8f0;">
                            <span class="badge badge-action {{ $actionClass }} rounded-pill">{{ $actionUpper }}</span>
                            <span class="text-slate-muted small" style="font-size: 0.75rem;"><i class="far fa-clock me-1"></i> {{ $log->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2 border" style="width: 32px; height: 32px;">
                                    <i class="fas fa-user text-secondary" style="font-size: 0.8rem;"></i>
                                </div>
                                <div>
                                    <span class="fw-bold text-slate-dark d-block" style="font-size: 0.9rem;">{{ $log->user ? $log->user->name : 'Sistem / Auto' }}</span>
                                    @if($log->user)
                                        <span class="text-slate-muted" style="font-size: 0.75rem;">{{ strtoupper(str_replace('_', ' ', $log->user->role)) }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="bg-light p-2.5 rounded-3 border">
                                <small class="text-muted d-block" style="font-size: 0.65rem; margin-bottom: 2px;">Deskripsi Aktivitas:</small>
                                <span class="text-slate-dark fw-medium" style="font-size: 0.85rem;">{{ $log->description }}</span>
                            </div>
                            
                            <div class="mt-2 text-end">
                                <span class="text-slate-muted" style="font-family: monospace; font-size: 0.75rem;"><i class="fas fa-network-wired me-1"></i>{{ $log->ip_address ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card p-5 text-center text-slate-muted bg-white shadow-sm" style="border-radius: 12px;">
                        <i class="fas fa-search-minus fa-3x mb-3 opacity-25"></i>
                        <span class="fw-bold text-slate-dark mb-1">Belum ada aktivitas</span>
                    </div>
                @endforelse
            </div>
        </div>
        
        @if($logs->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-end">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection