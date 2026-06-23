@extends('layouts.app')

@section('content')
<style>
    /* Global Overrides untuk Tema Premium Mentari Atlas */
    body { background-color: #f8fafc !important; }
    
    .text-emerald-custom { color: #10b981 !important; }
    .text-slate-dark { color: #0f172a !important; }
    .text-slate-muted { color: #64748b !important; }
    
    /* Card & Table Styling */
    .card-custom { border: 1px solid #f1f5f9; border-radius: 1.25rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); background: white; overflow: hidden; position: relative; z-index: 1; }
    .card-custom::before { content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: linear-gradient(90deg, #10b981, #0ea5e9); opacity: 0; transition: opacity 0.4s ease; }
    .card-custom:hover::before { opacity: 1; }
    .card-custom:hover { transform: translateY(-6px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05); border-color: #e2e8f0; }
    .card-custom:hover i.fa-2x { transform: scale(1.15) rotate(8deg); }
    i.fa-2x { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .table-custom-header th { background-color: #f1f5f9 !important; color: #334155 !important; font-weight: 600 !important; border-bottom: 2px solid #e2e8f0 !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    
    /* Custom Soft Border Colors */
    .border-left-emerald { border-left: 4px solid #10b981 !important; }
    .border-left-cyan { border-left: 4px solid #06b6d4 !important; }
    .border-left-rose { border-left: 4px solid #f43f5e !important; }
    .border-left-warning { border-left: 4px solid #f59e0b !important; }
    .border-left-slate { border-left: 4px solid #64748b !important; }

    /* Soft Badges */
    .badge-secondary-soft { background-color: #f1f5f9 !important; color: #475569 !important; border: 1px solid #cbd5e1; }
    .badge-emerald-soft { background-color: #d1fae5 !important; color: #047857 !important; border: 1px solid #a7f3d0; }
    .badge-rose-soft { background-color: #ffe4e6 !important; color: #be123c !important; border: 1px solid #fecdd3; }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 80vh;">
    
    {{-- HEADER & TOMBOL KEMBALI --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h4 mb-1 text-slate-dark fw-bold"><i class="fas fa-history text-emerald-custom me-2"></i>Log Riwayat Data & Stok</h1>
            <div class="d-flex align-items-center mt-2">
                <span class="badge badge-secondary-soft px-3 py-1.5 rounded-pill fw-bold letter-spacing-1 me-2">{{ $barang->kode_barang }}</span>
                <span class="text-slate-muted fw-medium">{{ $barang->nama_barang }}</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Katalog
            </a>
        </div>
    </div>

    {{-- RINGKASAN STOK (5 KARTU) --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-4 mb-4">
        
        <div class="col">
            <div class="card card-custom border-left-emerald shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-emerald-custom text-uppercase mb-1 tracking-wider letter-spacing-wide" style="font-size: 0.7rem;">Stok Bagus</div>
                            <div class="h2 mb-0 fw-bolder text-slate-dark">{{ $barang->stok_akhir }} <span class="fs-6 text-muted fw-normal">Unit</span></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box-open fa-2x text-emerald-custom opacity-40"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col">
            <div class="card card-custom border-left-rose shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1 tracking-wider letter-spacing-wide" style="font-size: 0.7rem;">Stok Rusak</div>
                            <div class="h2 mb-0 fw-bolder text-slate-dark">{{ $barang->stok_rusak ?? 0 }} <span class="fs-6 text-muted fw-normal">Unit</span></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heart-broken fa-2x text-danger opacity-40"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-custom border-left-cyan shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1 tracking-wider letter-spacing-wide" style="font-size: 0.7rem;">Total Masuk (In)</div>
                            <div class="h2 mb-0 fw-bolder text-slate-dark">{{ $barang->barang_masuk ?? 0 }} <span class="fs-6 text-muted fw-normal">Unit</span></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-alt-circle-down fa-2x text-info opacity-40"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col">
            <div class="card card-custom border-left-warning shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1 tracking-wider letter-spacing-wide" style="font-size: 0.7rem;">Barang Keluar (Out)</div>
                            <div class="h2 mb-0 fw-bolder text-slate-dark">{{ $barang->barang_keluar ?? 0 }} <span class="fs-6 text-muted fw-normal">Unit</span></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-alt-circle-up fa-2x text-warning opacity-40"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col">
            <div class="card card-custom border-left-slate shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-slate-muted text-uppercase mb-1 tracking-wider letter-spacing-wide" style="font-size: 0.7rem;">Harga Jual Valid</div>
                            <div class="h3 mb-0 fw-bolder text-slate-dark" style="letter-spacing: -1px;">Rp{{ number_format($barang->harga_jual, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-slate-muted opacity-40"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL RIWAYAT JURNAL STOK --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-custom bg-white overflow-hidden shadow-sm">
                <div class="card-header py-3 bg-white border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-slate-dark d-flex align-items-center">
                        <i class="fas fa-clipboard-list text-slate-muted me-2"></i> Jurnal Pergerakan Stok Detail
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive d-none d-lg-block">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;" width="100%" cellspacing="0">
                            <thead class="table-custom-header">
                                <tr>
                                    <th class="px-4 py-3">Waktu Transaksi</th>
                                    <th class="py-3">Kategori</th>
                                    <th class="py-3">Target Fisik</th>
                                    <th class="py-3 text-center">Mutasi</th>
                                    <th class="py-3 text-center">Sisa Stok</th>
                                    <th class="py-3 pe-4">Keterangan / Catatan Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($histories as $history)
                                
                                {{-- SENSOR & PENERJEMAH OTOMATIS --}}
                                @php
                                    // 1. Sensor Barang Rusak
                                    $keterangan = strtolower($history->keterangan ?? '');
                                    $isRusak = str_contains($keterangan, 'rusak') || str_contains($keterangan, 'cacat');

                                    // 2. Penerjemah Kategori (Agar bahasa lebih profesional)
                                    $kategoriMentah = strtoupper(str_replace('_', ' ', $history->event_label));
                                    
                                    if ($kategoriMentah === 'RETUR CUSTOMER' || $kategoriMentah === 'RETURN CUSTOMER') {
                                        $kategoriTampil = 'RETURN PENJUALAN';
                                    } elseif ($kategoriMentah === 'RETUR SUPPLIER' || $kategoriMentah === 'RETURN SUPPLIER') {
                                        $kategoriTampil = 'RETURN PEMBELIAN';
                                    } else {
                                        $kategoriTampil = $kategoriMentah;
                                    }
                                @endphp

                                <tr class="{{ $isRusak ? 'bg-light' : '' }}">
                                    <td class="px-4 text-slate-dark fw-medium text-nowrap">
                                        {{ $history->created_at->format('d M Y') }} <br>
                                        <span class="text-slate-muted small">{{ $history->created_at->format('H:i:s') }} WIB</span>
                                    </td>
                                    
                                    <td>
                                        <span class="badge badge-secondary-soft px-2.5 py-1.5 rounded">{{ $kategoriTampil }}</span>
                                        <div class="small text-muted mt-1 fw-bold">{{ $history->event_reference ?? '-' }}</div>
                                    </td>
                                    
                                    <td>
                                        @if($isRusak)
                                            <span class="badge badge-rose-soft px-2 py-1"><i class="fas fa-heart-broken me-1"></i> Stok Rusak</span>
                                        @else
                                            <span class="badge badge-emerald-soft px-2 py-1"><i class="fas fa-box me-1"></i> Stok Bagus</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @if($history->change > 0)
                                            <span class="badge badge-emerald-soft px-2 py-1 fs-6 fw-bold">+{{ $history->change }}</span>
                                        @elseif($history->change < 0)
                                            <span class="badge badge-rose-soft px-2 py-1 fs-6 fw-bold">{{ $history->change }}</span>
                                        @else
                                            <span class="text-muted fw-bold">0</span>
                                        @endif
                                    </td>
                                    
                                    <td class="text-center text-nowrap">
                                        <span class="text-slate-muted me-1">{{ $history->stock_before }}</span> 
                                        <i class="fas fa-arrow-right text-muted mx-1" style="font-size: 0.7rem;"></i> 
                                        <span class="text-slate-dark fw-bold fs-6 ms-1">{{ $history->stock_after }}</span>
                                    </td>
                                    
                                    <td class="pe-4 text-slate-muted" style="max-width: 300px;">
                                        {{ $history->keterangan ?? '-' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-slate-muted bg-white">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-clipboard fa-3x mb-3 text-muted opacity-25"></i>
                                            <span>Belum ada catatan aktivitas masuk/keluar untuk barang ini.</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- MOBILE CARDS --}}
                    <div class="d-lg-none p-3 bg-light">
                        @forelse($histories as $history)
                            @php
                                $keterangan = strtolower($history->keterangan ?? '');
                                $isRusak = str_contains($keterangan, 'rusak') || str_contains($keterangan, 'cacat');
                                $kategoriMentah = strtoupper(str_replace('_', ' ', $history->event_label));
                                if ($kategoriMentah === 'RETUR CUSTOMER' || $kategoriMentah === 'RETURN CUSTOMER') {
                                    $kategoriTampil = 'RETURN PENJUALAN';
                                } elseif ($kategoriMentah === 'RETUR SUPPLIER' || $kategoriMentah === 'RETURN SUPPLIER') {
                                    $kategoriTampil = 'RETURN PEMBELIAN';
                                } else {
                                    $kategoriTampil = $kategoriMentah;
                                }
                            @endphp
                            <div class="card card-custom mb-3 overflow-hidden" style="border-radius: 12px; border-left: 4px solid {{ $history->change > 0 ? '#10b981' : ($history->change < 0 ? '#f43f5e' : '#64748b') }} !important;">
                                <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #e2e8f0;">
                                    <span class="badge badge-secondary-soft text-uppercase fw-bold" style="font-size: 0.7rem;">{{ $kategoriTampil }}</span>
                                    <span class="text-slate-muted small" style="font-size: 0.75rem;"><i class="far fa-calendar-alt me-1"></i> {{ $history->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <span class="small text-muted d-block" style="font-size: 0.65rem;">Referensi Event:</span>
                                            <span class="fw-bold text-slate-dark">{{ $history->event_reference ?? '-' }}</span>
                                        </div>
                                        <div class="text-end">
                                            <span class="small text-muted d-block" style="font-size: 0.65rem;">Mutasi Stok:</span>
                                            @if($history->change > 0)
                                                <span class="badge badge-success-soft px-2 py-0.5 fw-bold" style="font-size: 0.75rem;">+{{ $history->change }} Pcs</span>
                                            @elseif($history->change < 0)
                                                <span class="badge badge-rose-soft px-2 py-0.5 fw-bold" style="font-size: 0.75rem;">{{ $history->change }} Pcs</span>
                                            @else
                                                <span class="badge badge-secondary-soft px-2 py-0.5 fw-bold" style="font-size: 0.75rem;">0 Pcs</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="p-2 bg-light rounded text-center border">
                                                <span class="d-block text-muted mb-1" style="font-size: 0.65rem;">Stok Fisik</span>
                                                @if($isRusak)
                                                    <span class="badge badge-rose-soft px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;"><i class="fas fa-heart-broken me-1"></i> Rusak</span>
                                                @else
                                                    <span class="badge badge-emerald-soft px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;"><i class="fas fa-box me-1"></i> Bagus</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 bg-light rounded text-center border">
                                                <span class="d-block text-muted mb-1" style="font-size: 0.65rem;">Alur Perubahan</span>
                                                <span class="text-slate-dark fw-bold" style="font-size: 0.8rem;">
                                                    {{ $history->stock_before }} <i class="fas fa-arrow-right text-muted mx-1" style="font-size: 0.65rem;"></i> {{ $history->stock_after }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-light p-2.5 rounded-3 border">
                                        <small class="text-muted d-block" style="font-size: 0.65rem; margin-bottom: 2px;">Keterangan / Catatan Admin:</small>
                                        <span class="text-slate-dark fw-medium" style="font-size: 0.8rem;">{{ $history->keterangan ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="card p-5 text-center text-slate-muted bg-white shadow-sm" style="border-radius: 12px;">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-clipboard fa-3x mb-3 text-muted opacity-25"></i>
                                    <span class="fw-bold text-slate-dark mb-1">Belum Ada Riwayat Pergerakan Stok</span>
                                    <span class="small">Log perubahan stok barang ini akan tampil di sini.</span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection