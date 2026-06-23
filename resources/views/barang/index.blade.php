@extends('layouts.app')

@section('content')
<style>
    /* Global Overrides untuk Tema Premium Mentari Atlas */
    body { background-color: var(--bg-page, #f8fafc) !important; }
    
    .text-emerald-custom { color: #10b981 !important; }
    .text-slate-dark { color: #0f172a !important; }
    .text-slate-muted { color: #64748b !important; }
    
    .bg-emerald-custom { background-color: #10b981 !important; color: #ffffff !important; }
    .btn-emerald-custom { background-color: #10b981 !important; border-color: #10b981 !important; color: #ffffff !important; font-weight: 500; transition: all 0.2s; }
    .btn-emerald-custom:hover { background-color: #059669 !important; color: #ffffff !important; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); }
    
    /* Card Styling */
    .card-custom { border: 1px solid #e2e8f0; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .border-left-emerald { border-left: 4px solid #10b981 !important; }
    
    /* Soft Badges */
    .badge-success-soft { background-color: #d1fae5 !important; color: #065f46 !important; border: 1px solid #a7f3d0; }
    .badge-danger-soft { background-color: #fee2e2 !important; color: #991b1b !important; border: 1px solid #fecaca; }
    .badge-secondary-soft { background-color: #f1f5f9 !important; color: #475569 !important; border: 1px solid #cbd5e1; }

    /* Minimalist Action Buttons */
    .btn-action-circle {
        width: 34px; height: 34px; padding: 0;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; transition: all 0.2s ease; border: none; flex-shrink: 0;
    }
    .btn-primary-soft { background-color: #e0e7ff; color: #4f46e5; }
    .btn-primary-soft:hover { background-color: #c7d2fe; color: #3730a3; transform: scale(1.1); }
    
    .btn-info-soft { background-color: #e0f2fe; color: #0284c7; }
    .btn-info-soft:hover { background-color: #bae6fd; color: #0369a1; transform: scale(1.1); }
    
    .btn-warning-soft { background-color: #fef3c7; color: #d97706; }
    .btn-warning-soft:hover { background-color: #fde68a; color: #b45309; transform: scale(1.1); }
    
    .btn-danger-soft { background-color: #fee2e2; color: #ef4444; }
    .btn-danger-soft:hover { background-color: #fecaca; color: #dc2626; transform: scale(1.1); }

    /* Search Box */
    .search-input:focus { border-color: #10b981; box-shadow: none; outline: none; }
    .focus-ring-emerald:focus-within { border-color: #10b981 !important; box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25) !important; }

    /* DIET TABEL EKSTRIM */
    .table-mentari-compact th, .table-mentari-compact td { padding: 0.75rem 0.5rem !important; font-size: 0.85rem !important; }
</style>

<div class="container-fluid py-4">
    
    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold"><i class="fas fa-boxes text-emerald-custom me-2"></i>Manajemen Stok Barang</h1>
            <p class="text-slate-muted small mb-0 mt-1">Pantau persediaan gudang, harga jual, dan perbarui data master barang.</p>
        </div>
        
        <div class="d-flex flex-column flex-sm-row gap-2 w-100 justify-content-md-end">
            <form action="{{ route('barang.index') }}" method="GET" class="m-0" style="min-width: 300px;">
                <div class="input-group shadow-sm rounded-pill overflow-hidden border bg-white focus-ring-emerald transition-all">
                    <input type="text" name="search" class="form-control border-0 search-input ps-4 pe-4 bg-white" placeholder="Cari kode atau nama barang..." value="{{ request('search') }}">
                    <button class="btn bg-white border-0 text-emerald-custom px-3" type="submit"><i class="fas fa-search"></i></button>
                    @if(request('search'))
                        <a href="{{ route('barang.index') }}" class="btn bg-white border-0 text-danger px-3 border-start" title="Hapus Pencarian"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
            <button type="button" class="btn btn-emerald-custom shadow-sm rounded-pill flex-shrink-0 px-4" data-bs-toggle="modal" data-bs-target="#modalTambahBarang">
                <i class="fas fa-plus me-2"></i> Tambah Barang
            </button>
        </div>
    </div>

    <div class="row">
        {{-- PANEL IMPORT CSV --}}
        <div class="col-md-12 mb-4 d-none d-lg-block">
            <div class="card card-custom border-left-emerald bg-white py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-emerald-custom text-uppercase mb-2 tracking-wider">
                                <i class="fas fa-cloud-upload-alt me-1"></i> Import Massal via File CSV
                            </div>
                            <form action="{{ route('barang.import') }}" method="POST" enctype="multipart/form-data" class="m-0">
                                @csrf
                                <div class="input-group input-group-sm mb-1" style="max-width: 600px;">
                                    <input type="file" name="file_csv" class="form-control bg-light" accept=".csv" required style="border-top-left-radius: 999px; border-bottom-left-radius: 999px;">
                                    <button type="submit" class="btn btn-emerald-custom px-4 fw-bold shadow-sm" style="border-top-right-radius: 999px; border-bottom-right-radius: 999px;">
                                        <i class="fas fa-sync-alt me-1"></i> Sinkronisasi Data
                                    </button>
                                </div>
                                <div class="form-text" style="font-size: 0.7rem; padding-left: 15px;">
                                    <i class="fas fa-info-circle me-1 text-slate-muted"></i> Format Kolom: <strong>kode_barang, nama_barang, spesifikasi, merek, harga_beli, harga_jual, stok_bagus, stok_rusak</strong>
                                </div>
                            </form>
                        </div>
                        <div class="col-auto d-none d-lg-block ms-4">
                            <i class="fas fa-file-csv fa-3x text-slate-muted opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL INVENTARIS BARANG --}}
        <div class="col-md-12">
            <div class="table-wrapper-mentari">
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-mentari table-mentari-compact align-middle mb-0" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="ps-4 text-nowrap" style="width: 10%;">Kode SKU</th>
                                <th style="width: 35%;">Info Produk Utama</th>
                                <th class="text-end text-nowrap" style="width: 15%;">HPP (Rp)</th>
                                <th class="text-end text-nowrap" style="width: 15%;">Harga Jual (Rp)</th>
                                <th class="text-center text-nowrap" style="width: 10%;">Sisa Stok</th>
                                <th class="text-center pe-4" style="width: 15%; white-space: nowrap;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barangs as $b)
                            <tr>
                                <td class="ps-4 fw-bold text-emerald-custom text-nowrap">{{ $b->kode_barang }}</td>
                                <td>
                                    <div class="fw-bold text-slate-dark" style="font-size: 0.9rem;">{{ $b->nama_barang }}</div>
                                    @if($b->merek || $b->spesifikasi)
                                        <div class="text-slate-muted mt-1" style="font-size: 0.75rem;">
                                            @if($b->merek)<span class="me-3"><i class="fas fa-tag text-emerald-custom me-1"></i>{{ $b->merek }}</span>@endif
                                            @if($b->spesifikasi)<span><i class="fas fa-info-circle text-emerald-custom me-1"></i>{{ $b->spesifikasi }}</span>@endif
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-slate-muted text-nowrap" style="font-size: 0.9rem;">
                                    {{ number_format($b->harga_beli, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold text-slate-dark text-nowrap" style="font-size: 0.9rem;">
                                    {{ number_format($b->harga_jual, 0, ',', '.') }}
                                </td>
                                <td class="text-center text-nowrap">
                                    <span class="fw-bold fs-5 {{ $b->stok_akhir <= 15 ? 'text-danger' : 'text-emerald-custom' }}">{{ $b->stok_akhir }}</span>
                                    @if($b->stok_akhir <= 15)
                                        <span class="badge badge-danger-soft d-block mx-auto mt-1" style="font-size: 0.6rem; width: fit-content;">Kritis</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4 align-middle" style="white-space: nowrap;">
                                    <div class="d-flex justify-content-center align-items-center gap-2 flex-nowrap">
                                        {{-- Tombol Detail --}}
                                        <button type="button" class="btn-action-circle btn-primary-soft" data-bs-toggle="modal" data-bs-target="#modalDetailBarang{{ $b->id }}" title="Lihat Detail Lengkap">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <a href="{{ route('barang.history', $b->id) }}" class="btn-action-circle btn-info-soft" title="Log Riwayat"><i class="fas fa-history"></i></a>
                                        
                                        {{-- Tombol Edit --}}
                                        <button type="button" class="btn-action-circle btn-warning-soft" data-bs-toggle="modal" data-bs-target="#modalEditBarang{{ $b->id }}" title="Edit">
                                            <i class="fas fa-pen"></i>
                                        </button>

                                        <form action="{{ route('barang.destroy', $b->id) }}" method="POST" class="m-0">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn-action-circle btn-danger-soft btn-delete" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-slate-muted bg-white">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-box-open fa-3x mb-3 text-muted opacity-25"></i>
                                        @if(request('search')) <span>Pencarian "<strong>{{ request('search') }}</strong>" tidak ditemukan.</span>
                                        @else <span>Katalog barang masih kosong. Silakan import CSV atau tambah manual.</span> @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- PAGINATION DESKTOP --}}
                <div class="d-none d-lg-block mt-3 px-3">
                    {{ $barangs->links('pagination::bootstrap-5') }}
                </div>
            
                {{-- MOBILE CARDS --}}
                <div class="d-lg-none p-3" style="background-color: var(--bg-page);">
                    @forelse($barangs as $b)
                    <div class="card card-custom mb-3" style="border-radius: 16px;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-emerald-custom fw-bold px-2 py-1" style="border-radius: 8px;">{{ $b->kode_barang }}</span>
                                <div class="text-end">
                                    <span class="fw-bold fs-6 {{ $b->stok_akhir <= 15 ? 'text-danger' : 'text-emerald-custom' }}">Stok: {{ $b->stok_akhir }}</span>
                                    @if($b->stok_akhir <= 15) <span class="d-block text-danger" style="font-size: 0.6rem;">Kritis</span> @endif
                                </div>
                            </div>
                            <h6 class="fw-bold text-slate-dark mb-1" style="font-size: 1.05rem;">{{ $b->nama_barang }}</h6>
                            <div class="text-muted mb-3" style="font-size: 0.8rem;">
                                @if($b->merek)<span class="me-2"><i class="fas fa-tag text-emerald-custom me-1"></i>{{ $b->merek }}</span>@endif
                                @if($b->spesifikasi)<span><i class="fas fa-info-circle text-emerald-custom me-1"></i>{{ $b->spesifikasi }}</span>@endif
                            </div>
                            <div class="d-flex justify-content-between mb-3 bg-light p-2" style="border-radius: 12px; border: 1px solid var(--border-panel);">
                                <div><small class="text-muted d-block" style="font-size:0.65rem;">HPP</small><span class="fw-bold text-slate-dark" style="font-size:0.85rem;">Rp {{ number_format($b->harga_beli, 0, ',', '.') }}</span></div>
                                <div class="text-end"><small class="text-muted d-block" style="font-size:0.65rem;">Harga Jual</small><span class="fw-bold text-emerald-custom" style="font-size:0.9rem;">Rp {{ number_format($b->harga_jual, 0, ',', '.') }}</span></div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-primary-soft flex-fill fw-bold" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalDetailBarang{{ $b->id }}"><i class="fas fa-eye"></i></button>
                                <a href="{{ route('barang.history', $b->id) }}" class="btn btn-sm btn-info-soft flex-fill fw-bold" style="border-radius: 10px;"><i class="fas fa-history"></i></a>
                                <button type="button" class="btn btn-sm btn-warning-soft flex-fill fw-bold" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalEditBarang{{ $b->id }}"><i class="fas fa-pen"></i></button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted bg-white shadow-sm" style="border-radius: 16px;">Belum ada data barang.</div>
                    @endforelse

                    {{-- PAGINATION MOBILE --}}
                    <div class="mt-3">
                        {{ $barangs->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================== --}}
{{-- MODAL TAMBAH BARANG MANUAL --}}
{{-- ========================================================== --}}
<div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('barang.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-3">
            @csrf
            <div class="modal-header bg-light border-bottom">
                <h6 class="modal-title fw-bold text-slate-dark"><i class="fas fa-box text-emerald-custom me-2"></i>Registrasi Barang Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">Kode Barang (SKU Code) <span class="text-danger">*</span></label>
                        <input type="text" name="kode_barang" class="form-control bg-light" placeholder="Contoh: B001, OLI-MT" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control bg-light" placeholder="Contoh: Oli Mesin Matic, Aki Motor" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">Spesifikasi (Opsional)</label>
                        <input type="text" name="spesifikasi" class="form-control bg-light" placeholder="Contoh: 1 Liter, 12V">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">Merek (Opsional)</label>
                        <input type="text" name="merek" class="form-control bg-light" placeholder="Contoh: Pertamina, GS">
                    </div>
                </div>
                <div class="row pt-3 mt-1 border-top">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">HPP / Modal Awal (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga_beli" class="form-control text-end fw-bold text-danger" placeholder="0" min="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">Harga Jual (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga_jual" class="form-control text-end fw-bold border-emerald-custom" style="color: #10b981;" placeholder="0" min="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">Stok Bagus Awal <span class="text-danger">*</span></label>
                        <input type="number" name="stok_akhir" class="form-control text-center fw-bold text-emerald-custom" placeholder="0" min="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">Stok Rusak Awal</label>
                        <input type="number" name="stok_rusak" class="form-control text-center fw-bold text-danger" placeholder="0" min="0" value="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-emerald-custom rounded-pill px-4 fw-bold">Simpan ke Katalog</button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================== --}}
{{-- KUMPULAN MODAL (DETAIL & EDIT BARANG) --}}
{{-- ========================================================== --}}
@foreach($barangs as $b)

{{-- 1. MODAL DETAIL --}}
<div class="modal fade" id="modalDetailBarang{{ $b->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3" style="background-color: #f8fafc;">
                <h6 class="modal-title fw-bold text-slate-dark"><i class="fas fa-info-circle text-primary me-2"></i>Detail Komprehensif Barang</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-slate-dark border-bottom pb-2 mb-3" style="font-size: 0.9rem;">Identitas Fisik</h6>
                        <table class="table table-borderless table-sm mb-0" style="font-size: 0.85rem;">
                            <tr>
                                <td width="35%" class="text-slate-muted pb-2">Kode SKU</td>
                                <td class="fw-bold text-emerald-custom pb-2">: {{ $b->kode_barang }}</td>
                            </tr>
                            <tr>
                                <td class="text-slate-muted pb-2">Nama Barang</td>
                                <td class="fw-bold text-slate-dark pb-2">: {{ $b->nama_barang }}</td>
                            </tr>
                            <tr>
                                <td class="text-slate-muted pb-2">Merek / Tag</td>
                                <td class="fw-bold text-slate-dark pb-2">: {{ $b->merek ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-slate-muted pb-2">Spesifikasi</td>
                                <td class="fw-bold text-slate-dark pb-2">: {{ $b->spesifikasi ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-slate-muted pb-2">Terakhir Update</td>
                                <td class="fw-bold text-slate-dark pb-2">: {{ $b->updated_at ? $b->updated_at->format('d M Y, H:i') : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="fw-bold text-slate-dark border-bottom pb-2 mb-3" style="font-size: 0.9rem;">Finansial & Status Gudang</h6>
                        <table class="table table-borderless table-sm mb-0" style="font-size: 0.85rem;">
                            <tr>
                                <td width="40%" class="text-slate-muted pb-2">HPP (Modal)</td>
                                <td class="fw-bold text-danger pb-2">: Rp {{ number_format($b->harga_beli, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="text-slate-muted pb-2">Harga Jual Aktif</td>
                                <td class="fw-bold text-success pb-2">: Rp {{ number_format($b->harga_jual, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="text-slate-muted pb-2">Margin Kotor</td>
                                <td class="fw-bold text-primary pb-2">: 
                                    @if($b->harga_jual > 0)
                                        Rp {{ number_format($b->harga_jual - $b->harga_beli, 0, ',', '.') }} 
                                        <span class="badge bg-light text-primary border ms-1">{{ number_format((($b->harga_jual - $b->harga_beli) / $b->harga_jual) * 100, 1) }}%</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-slate-muted pb-2 pt-3">Barang Masuk (In)</td>
                                <td class="fw-bold text-success pb-2 pt-3">: {{ $b->barang_masuk ?? 0 }} pcs</td>
                            </tr>
                            <tr>
                                <td class="text-slate-muted pb-2">Barang Keluar (Out)</td>
                                <td class="fw-bold text-danger pb-2">: {{ $b->barang_keluar ?? 0 }} pcs</td>
                            </tr>
                            <tr>
                                <td class="text-slate-muted pb-2 pt-2 border-top">Stok Tersedia (Bagus)</td>
                                <td class="fw-bold pb-2 pt-2 border-top">: <span class="{{ $b->stok_akhir <= 15 ? 'text-danger' : 'text-emerald-custom' }}">{{ $b->stok_akhir }} pcs</span></td>
                            </tr>
                            <tr>
                                <td class="text-slate-muted pb-2">Barang Cacat/Rusak</td>
                                <td class="fw-bold pb-2">: <span class="text-danger">{{ $b->stok_rusak ?? 0 }} pcs</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0 py-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold small" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- 2. MODAL EDIT BARANG (FULL AKSES KE SEMUA KOLOM) --}}
<div class="modal fade" id="modalEditBarang{{ $b->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('barang.update', $b->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-3">
            @csrf @method('PUT')
            <div class="modal-header bg-light border-bottom">
                <h6 class="modal-title fw-bold text-slate-dark"><i class="fas fa-edit text-warning me-2"></i>Edit Data & Stok: {{ $b->nama_barang }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">Kode Barang (SKU Code) <span class="text-danger">*</span></label>
                        <input type="text" name="kode_barang" class="form-control bg-light" value="{{ $b->kode_barang }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control bg-light" value="{{ $b->nama_barang }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">Spesifikasi</label>
                        <input type="text" name="spesifikasi" class="form-control bg-light" value="{{ $b->spesifikasi }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">Merek</label>
                        <input type="text" name="merek" class="form-control bg-light" value="{{ $b->merek }}">
                    </div>
                </div>

                {{-- AREA HPP & HARGA JUAL --}}
                <div class="row pt-3 mt-1 border-top">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">HPP / Harga Beli (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga_beli" class="form-control text-end fw-bold text-danger border-warning" min="0" value="{{ $b->harga_beli }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">Harga Jual Baru (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga_jual" class="form-control text-end fw-bold border-emerald-custom" style="color: #10b981;" min="0" value="{{ $b->harga_jual }}" required>
                    </div>
                </div>

                {{-- AREA STOK FISIK (BARU DITAMBAHKAN) --}}
                <div class="row pt-2">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">Stok Tersedia (BAGUS) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="stok_akhir" class="form-control text-center fw-bold text-emerald-custom" min="0" value="{{ $b->stok_akhir }}" required>
                            <span class="input-group-text bg-light text-muted">Pcs</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-slate-dark">Stok Cacat (RUSAK) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="stok_rusak" class="form-control text-center fw-bold text-danger" min="0" value="{{ $b->stok_rusak ?? 0 }}" required>
                            <span class="input-group-text bg-light text-muted">Pcs</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-emerald-custom rounded-pill px-4 fw-bold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection