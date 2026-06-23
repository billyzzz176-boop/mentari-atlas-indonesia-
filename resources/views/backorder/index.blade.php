@extends('layouts.app')

@section('content')
<style>
    /* Global Overrides untuk Tema Premium Mentari Atlas */
    body { background-color: #f8fafc !important; }
    
    .text-emerald-custom { color: #10b981 !important; }
    .text-slate-dark { color: #0f172a !important; }
    .text-slate-muted { color: #64748b !important; }
    
    .bg-emerald-custom { background-color: #10b981 !important; color: #ffffff !important; }
    .btn-emerald-custom { background-color: #10b981 !important; border-color: #10b981 !important; color: #ffffff !important; font-weight: 500; transition: all 0.2s; }
    .btn-emerald-custom:hover { background-color: #059669 !important; color: #ffffff !important; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); }
    
    /* Soft Badges */
    .badge-success-soft { background-color: #d1fae5 !important; color: #065f46 !important; border: 1px solid #a7f3d0; }
    .badge-danger-soft { background-color: #fee2e2 !important; color: #991b1b !important; border: 1px solid #fecaca; }
    .badge-warning-soft { background-color: #fef3c7 !important; color: #92400e !important; border: 1px solid #fde68a; }
    .badge-secondary-soft { background-color: #f1f5f9 !important; color: #475569 !important; border: 1px solid #cbd5e1; }
    
    /* Filter Tabs Styling */
    .nav-filter .nav-link { color: #64748b; font-weight: 600; border-radius: 999px; padding: 0.5rem 1.25rem; margin-right: 0.5rem; transition: all 0.2s; cursor: pointer; border: 1px solid transparent; }
    .nav-filter .nav-link:hover { background-color: #f1f5f9; color: #0f172a; }
    .nav-filter .nav-link.active { background-color: #10b981; color: #ffffff; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2); }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 80vh;">
    
    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold"><i class="fas fa-hourglass-half text-warning me-2"></i>Daftar Antrean Back Order</h1>
            <p class="text-slate-muted small mb-0 mt-1">Pantau sisa kuantitas item pesanan Sales Order yang tertunda. Pastikan stok tersedia agar tim Warehouse bisa melakukan pengemasan.</p>
        </div>
    </div>

    {{-- Kotak Notifikasi Berhasil / Gagal --}}
    @if(session('success'))
        <div class="alert badge-success-soft alert-dismissible fade show border-0 shadow-sm rounded-3 px-4 py-3 mb-4">
            <i class="fas fa-check-circle text-success me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert badge-danger-soft alert-dismissible fade show border-0 shadow-sm rounded-3 px-4 py-3 mb-4">
            <i class="fas fa-exclamation-triangle text-danger me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTER TABS --}}
    <div class="mb-4">
        <ul class="nav nav-filter border-0">
            <li class="nav-item">
                <a class="nav-link active filter-btn" data-filter="all"><i class="fas fa-list me-1"></i> Semua Data</a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-btn" data-filter="pending"><i class="fas fa-circle-notch fa-spin me-1"></i> Menunggu Stok</a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-btn" data-filter="completed"><i class="fas fa-check-circle me-1"></i> Selesai (Terpenuhi)</a>
            </li>
        </ul>
    </div>

    {{-- TABEL DATA BACK ORDER (MENGGUNAKAN TEMA BARU) --}}
    <div class="table-wrapper-mentari">
        <div class="table-responsive d-none d-lg-block">
            <table class="table table-mentari table-mentari-compact align-middle mb-0" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th class="ps-4">No. SO & Tanggal</th>
                        <th>Pelanggan & Sales</th>
                        <th>Info Barang</th>
                        <th class="text-center">Permintaan Awal</th>
                        <th class="text-center">Kekurangan (BO)</th>
                        <th class="text-center">Status Antrean</th>
                        <th class="text-center sticky-action" style="width: 160px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody id="bo-table-body">
                    @forelse($backOrders as $bo)
                        @php
                            // Menentukan kategori filter untuk baris ini
                            $kategoriFilter = (strtolower($bo->status_bo) === 'terpenuhi' || strtolower($bo->status_bo) === 'selesai') ? 'completed' : 'pending';
                        @endphp
                        <tr class="bo-row" data-status="{{ $kategoriFilter }}">
                            <td class="ps-4">
                                <span class="fw-bold text-slate-dark d-block" style="font-size: 0.85rem;">{{ $bo->penjualan->no_so }}</span>
                                <span class="text-slate-muted small" style="font-size: 0.75rem;"><i class="far fa-calendar-alt me-1"></i> {{ date('d M Y', strtotime($bo->penjualan->tanggal_order)) }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-slate-dark d-block" style="font-size: 0.85rem;">{{ $bo->penjualan->customer->nama_customer }}</span>
                                <span class="badge badge-secondary-soft rounded-pill px-2 py-0.5 mt-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                    <i class="fas fa-user-tie me-1"></i> {{ $bo->penjualan->user->name ?? 'Sales' }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-emerald-custom d-block" style="font-size: 0.85rem;">{{ $bo->barang->nama_barang }}</span>
                                <span class="text-slate-muted small" style="font-size: 0.75rem;">SKU: {{ $bo->barang->kode_barang }}</span>
                            </td>
                            <td class="text-center fw-semibold text-slate-dark">
                                {{ $bo->jumlah_diminta }} Pcs
                            </td>
                            <td class="text-center">
                                <span class="badge badge-danger-soft fw-bold px-2.5 py-1 rounded-pill" style="font-size: 0.75rem;">
                                    <i class="fas fa-arrow-down me-1"></i> {{ $bo->jumlah_kurang }} Pcs
                                </span>
                            </td>
                            
                            {{-- INDIKATOR STATUS --}}
                            <td class="text-center">
                                @if($kategoriFilter === 'completed')
                                    <span class="badge badge-success-soft rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.75rem;">
                                        <i class="fas fa-check-circle me-1"></i> Terpenuhi
                                    </span>
                                @else
                                    <span class="badge badge-warning-soft rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.75rem;">
                                        <i class="fas fa-circle-notch fa-spin me-1"></i> Menunggu Stok
                                    </span>
                                @endif
                            </td>
                            
                            {{-- TOMBOL TINDAKAN DINAMIS --}}
                            <td class="text-center sticky-action">
                                @if($kategoriFilter === 'pending')
                                    @if($bo->barang->stok_akhir >= $bo->jumlah_kurang)
                                        <form action="{{ route('backorder.penebusan', $bo->id) }}" method="POST" onsubmit="return confirm('Konfirmasi: Lepaskan sisa stok dan kemas barang sekarang?')">
                                            @csrf
                                            <button type="submit" class="btn btn-emerald-custom btn-sm rounded-pill px-3 py-1 fw-bold shadow-sm" style="font-size: 0.75rem;" title="Stok tersedia: {{ $bo->barang->stok_akhir }} Unit. Klik untuk kirim barang.">
                                                <i class="fas fa-box-open me-1"></i> Kemas Sisa
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ url('/pembelian') }}" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 fw-bold shadow-sm" style="font-size: 0.75rem;" title="Stok gudang sisa {{ $bo->barang->stok_akhir ?? 0 }}, butuh total {{ $bo->jumlah_kurang }}.">
                                            <i class="fas fa-truck-loading me-1"></i> Restock Dulu
                                        </a>
                                    @endif
                                @else
                                    <span class="text-slate-muted small fw-bold" style="font-size: 0.75rem;"><i class="fas fa-lock me-1"></i> Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-state-row">
                            <td colspan="7" class="text-center py-5 text-slate-muted bg-white">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-check-double text-success fa-3x mb-3 opacity-25"></i>
                                    <span class="fw-bold text-slate-dark mb-1">Tidak Ada Antrean Back Order</span>
                                    <span class="small">Semua pesanan pelanggan saat ini terlayani dengan stok yang aman.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MOBILE CARDS --}}
    <div class="d-lg-none p-2" id="bo-mobile-list">
        @forelse($backOrders as $bo)
            @php
                $kategoriFilter = (strtolower($bo->status_bo) === 'terpenuhi' || strtolower($bo->status_bo) === 'selesai') ? 'completed' : 'pending';
            @endphp
            <div class="card card-custom mb-3 bo-row" data-status="{{ $kategoriFilter }}" style="border-left: 4px solid {{ $kategoriFilter === 'completed' ? '#10b981' : '#f59e0b' }} !important;">
                <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #e2e8f0;">
                    <span class="fw-bold text-slate-dark" style="font-size: 0.85rem;"><i class="fas fa-file-invoice me-1"></i> {{ $bo->penjualan->no_so }}</span>
                    <span class="text-slate-muted small" style="font-size: 0.75rem;"><i class="far fa-calendar-alt me-1"></i> {{ date('d M Y', strtotime($bo->penjualan->tanggal_order)) }}</span>
                </div>
                <div class="card-body p-3">
                    <h6 class="fw-bold text-slate-dark mb-1" style="font-size: 1rem;">{{ $bo->penjualan->customer->nama_customer }}</h6>
                    <div class="text-muted mb-3" style="font-size: 0.8rem;">
                        <i class="fas fa-user-tie me-1"></i> Sales: <strong>{{ $bo->penjualan->user->name ?? 'Sales' }}</strong>
                    </div>

                    <div class="p-2 bg-light rounded-3 mb-3 border">
                        <div class="fw-bold text-emerald-custom" style="font-size: 0.85rem;">{{ $bo->barang->nama_barang }}</div>
                        <div class="text-muted small" style="font-size: 0.75rem;">SKU: {{ $bo->barang->kode_barang }}</div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 bg-light rounded text-center border">
                                <span class="d-block text-muted" style="font-size: 0.65rem;">Permintaan Awal</span>
                                <span class="fw-bold text-slate-dark" style="font-size: 0.85rem;">{{ $bo->jumlah_diminta }} Pcs</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-danger-subtle rounded text-center border border-danger-subtle">
                                <span class="d-block text-danger" style="font-size: 0.65rem;">Kekurangan (BO)</span>
                                <span class="fw-bold text-danger" style="font-size: 0.85rem;">{{ $bo->jumlah_kurang }} Pcs</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($kategoriFilter === 'completed')
                                <span class="badge badge-success-soft rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.75rem;">
                                    <i class="fas fa-check-circle me-1"></i> Terpenuhi
                                </span>
                            @else
                                <span class="badge badge-warning-soft rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.75rem;">
                                    <i class="fas fa-circle-notch fa-spin me-1"></i> Menunggu Stok
                                </span>
                            @endif
                        </div>
                        <div>
                            @if($kategoriFilter === 'pending')
                                @if($bo->barang->stok_akhir >= $bo->jumlah_kurang)
                                    <form action="{{ route('backorder.penebusan', $bo->id) }}" method="POST" onsubmit="return confirm('Konfirmasi: Lepaskan sisa stok dan kemas barang sekarang?')">
                                        @csrf
                                        <button type="submit" class="btn btn-emerald-custom btn-sm rounded-pill px-3 py-1 fw-bold shadow-sm" style="font-size: 0.75rem;" title="Stok tersedia: {{ $bo->barang->stok_akhir }} Unit. Klik untuk kirim barang.">
                                            <i class="fas fa-box-open me-1"></i> Kemas Sisa
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ url('/pembelian') }}" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 fw-bold shadow-sm" style="font-size: 0.75rem;" title="Stok gudang sisa {{ $bo->barang->stok_akhir ?? 0 }}, butuh total {{ $bo->jumlah_kurang }}.">
                                        <i class="fas fa-truck-loading me-1"></i> Restock
                                    </a>
                                @endif
                            @else
                                <span class="text-slate-muted text-uppercase small fw-bold" style="font-size: 0.75rem;"><i class="fas fa-lock me-1"></i> Selesai</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-5 text-center text-slate-muted bg-white shadow-sm" style="border-radius: 12px;">
                <div class="d-flex flex-column align-items-center">
                    <i class="fas fa-check-double text-success fa-3x mb-3 opacity-25"></i>
                    <span class="fw-bold text-slate-dark mb-1">Tidak Ada Antrean Back Order</span>
                    <span class="small">Semua pesanan pelanggan saat ini terlayani dengan stok yang aman.</span>
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Aktivasi Tooltip Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // 2. Logika JavaScript Filter Instan
        const filterBtns = document.querySelectorAll('.filter-btn');
        const rows = document.querySelectorAll('.bo-row');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Hapus kelas 'active' dari semua tombol
                filterBtns.forEach(b => b.classList.remove('active'));
                // Tambahkan kelas 'active' ke tombol yang diklik
                this.classList.add('active');

                const filterValue = this.getAttribute('data-filter');

                // Tampilkan atau sembunyikan baris tabel
                rows.forEach(row => {
                    if (filterValue === 'all' || row.getAttribute('data-status') === filterValue) {
                        row.style.display = ''; // Munculkan
                    } else {
                        row.style.display = 'none'; // Sembunyikan
                    }
                });
            });
        });
    });
</script>
@endsection