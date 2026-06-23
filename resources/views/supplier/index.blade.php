@extends('layouts.app')

@section('content')
<style>
    /* Unified Green Action Buttons */
    .btn-action-mentari { 
        width: 34px; 
        height: 34px; 
        padding: 0; 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        border-radius: 0.5rem; 
        background-color: #ecfdf5; /* Hijau pudar */
        color: #10b981; /* Ikon Hijau Emerald */
        border: 1px solid #a7f3d0;
        transition: all 0.2s ease; 
        flex-shrink: 0; 
    }
    .btn-action-mentari:hover { 
        background-color: #10b981; 
        color: #ffffff; 
        transform: translateY(-2px); 
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.25); 
    }

    .table-mentari-compact th, .table-mentari-compact td { padding: 0.75rem 0.5rem !important; }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 80vh;">
    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold"><i class="fas fa-truck text-emerald-custom me-2"></i>Manajemen Data Supplier</h1>
            <p class="text-slate-muted small mb-0 mt-1">Kelola data pemasok/vendor untuk kebutuhan pembelian stok barang Anda.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form action="{{ route('supplier.index') }}" method="GET" class="m-0 d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3 bg-white border" placeholder="Cari Kode/Nama..." value="{{ $search ?? '' }}" style="width: 200px; height: 38px;">
                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">Cari</button>
                @if(!empty($search))
                    <a href="{{ route('supplier.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold d-flex align-items-center">Reset</a>
                @endif
            </form>
            <button class="btn btn-emerald-custom shadow-sm rounded-pill px-4 fw-bold" style="background-color: #10b981; color: white; border: none; height: 38px;" data-bs-toggle="modal" data-bs-target="#modalTambahSupplier">
                <i class="fas fa-plus me-2"></i> Tambah Supplier
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4 border-start border-success border-4 rounded-3 px-4 py-3" role="alert">
            <i class="fas fa-check-circle text-success me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4 border-start border-danger border-4 rounded-3 px-4 py-3" role="alert">
            <i class="fas fa-exclamation-circle text-danger me-2"></i><strong>Gagal memproses data!</strong> {{ $errors->first() }}
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TABEL TEMA PREMIUM --}}
    <div class="table-wrapper-mentari">
        <div class="table-responsive d-none d-lg-block">
            <table class="table table-mentari table-mentari-compact align-middle mb-0" style="font-size: 0.85rem; width: 100%;">
                <thead>
                    <tr>
                        <th class="ps-3 text-nowrap">Kode Supplier</th>
                        <th>Nama Supplier / PT</th>
                        <th>No. Telepon / WA</th>
                        <th class="text-center">Termin Bayar</th>
                        <th class="text-center pe-3" style="width: 1%; white-space: nowrap;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $s)
                    <tr>
                        <td class="ps-3 fw-bold text-emerald-custom text-nowrap">{{ $s->kode_supplier }}</td>
                        <td class="fw-bold text-slate-dark">{{ $s->nama_supplier }}</td>
                        <td class="text-slate-muted">{{ $s->telepon ?: '-' }}</td>
                        <td class="text-center"><span class="badge bg-warning text-dark rounded-pill px-2 py-1">{{ $s->jatuh_tempo_hari ?? 30 }} Hari</span></td>
                        <td class="pe-3 text-center align-middle" style="white-space: nowrap;">
                            <div class="d-flex justify-content-center align-items-center gap-2 flex-nowrap">
                                
                                {{-- Tombol Detail --}}
                                <button type="button" class="btn-action-mentari" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $s->id }}" title="Lihat Rincian">
                                    <i class="fas fa-eye"></i>
                                </button>

                                {{-- Tombol Edit --}}
                                <button type="button" class="btn-action-mentari" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $s->id }}" title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('supplier.destroy', $s->id) }}" method="POST" class="m-0 ms-1">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-action-mentari btn-delete" title="Hapus Supplier">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-slate-muted bg-white">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-truck-loading fa-3x mb-3 text-muted opacity-25"></i>
                                <span>Belum ada data supplier yang terdaftar.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

                {{-- MOBILE CARDS --}}
                <div class="d-lg-none p-3" style="background-color: var(--bg-page);">
                    @forelse($suppliers as $s)
                    <div class="card card-custom mb-3" style="border-radius: 16px;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-emerald-custom fw-bold px-2 py-1" style="border-radius: 8px;">{{ $s->kode_supplier }}</span>
                            </div>
                            
                            <h6 class="fw-bold text-slate-dark mb-1" style="font-size: 1.05rem;">{{ $s->nama_supplier }}</h6>
                            <div class="text-muted mb-3" style="font-size: 0.8rem;"><i class="fas fa-phone-alt me-1"></i>{{ $s->telepon ?: '-' }}</div>
                            
                            <div class="mb-3 bg-light p-2" style="border-radius: 12px; border: 1px solid var(--border-panel);">
                                <small class="text-muted d-block" style="font-size:0.65rem;">Termin Bayar</small>
                                <span class="badge bg-warning text-dark px-2 py-1">{{ $s->jatuh_tempo_hari ?? 30 }} Hari</span>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-primary-soft flex-fill fw-bold py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $s->id }}"><i class="fas fa-eye me-1"></i>Detail</button>
                                <button type="button" class="btn btn-sm btn-warning-soft flex-fill fw-bold py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $s->id }}"><i class="fas fa-pen me-1"></i>Edit</button>
                                <form action="{{ route('supplier.destroy', $s->id) }}" method="POST" class="m-0 flex-fill">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger-soft w-100 fw-bold py-2 btn-delete" style="border-radius: 10px;"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted bg-white shadow-sm" style="border-radius: 16px;">Belum ada data supplier.</div>
                    @endforelse
                </div>

</div>

{{-- =========================================================================
    KUMPULAN MODAL (DILETAKKAN DI LUAR TABEL AGAR LAYOUT TIDAK HANCUR)
========================================================================== --}}

@foreach($suppliers as $s)
    {{-- MODAL DETAIL SUPPLIER --}}
    <div class="modal fade" id="modalDetail{{ $s->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light border-bottom py-3">
                    <h6 class="modal-title fw-bold text-slate-dark"><i class="fas fa-id-card text-info me-2"></i>Rincian Data: {{ $s->nama_supplier }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3"><span class="small text-muted d-block">Kode Supplier</span><span class="fw-bold">{{ $s->kode_supplier }}</span></div>
                            <div class="mb-3"><span class="small text-muted d-block">Nama Supplier</span><span class="fw-bold">{{ $s->nama_supplier }}</span></div>
                            <div class="mb-3"><span class="small text-muted d-block">No. Telepon / WA</span><span class="fw-bold">{{ $s->telepon ?: '-' }}</span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3"><span class="small text-muted d-block">Nomor KTP</span><span class="fw-bold">{{ $s->ktp ?: '-' }}</span></div>
                            <div class="mb-3"><span class="small text-muted d-block">Nomor NPWP</span><span class="fw-bold">{{ $s->npwp ?: '-' }}</span></div>
                            <div class="mb-3"><span class="small text-muted d-block">Termin Jatuh Tempo</span><span class="fw-bold text-danger">{{ $s->jatuh_tempo_hari ?? 30 }} Hari</span></div>
                            <div class="mb-3"><span class="small text-muted d-block">Alamat Lengkap</span><span class="fw-bold">{{ $s->alamat ?: '-' }}</span></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3 border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT SUPPLIER --}}
    <div class="modal fade" id="modalEdit{{ $s->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('supplier.update', $s->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf @method('PUT')
                <div class="modal-header bg-light border-bottom py-3">
                    <h6 class="modal-title fw-bold text-slate-dark"><i class="fas fa-edit text-warning me-2"></i>Edit Data: {{ $s->nama_supplier }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-dark">Kode Supplier <small class="text-muted">(Otomatis)</small></label>
                            <input type="text" name="kode_supplier" class="form-control bg-light text-muted" value="{{ $s->kode_supplier }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-dark">Nama Supplier / PT <span class="text-danger">*</span></label>
                            <input type="text" name="nama_supplier" class="form-control bg-light" value="{{ $s->nama_supplier }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-dark">Nomor KTP</label>
                            <input type="text" name="ktp" class="form-control bg-light" value="{{ $s->ktp }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-dark">Nomor NPWP</label>
                            <input type="text" name="npwp" class="form-control bg-light" value="{{ $s->npwp }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-dark">No. Telepon / WA</label>
                            <input type="text" name="telepon" class="form-control bg-light" value="{{ $s->telepon }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-dark">Jatuh Tempo (Hari)</label>
                            <input type="number" name="jatuh_tempo_hari" class="form-control bg-light" value="{{ $s->jatuh_tempo_hari ?? 30 }}" min="0">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-slate-dark">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control bg-light" rows="2">{{ $s->alamat }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3 border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-emerald-custom rounded-pill px-4 fw-bold" style="background-color: #10b981; color: white; border: none;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

{{-- MODAL TAMBAH SUPPLIER BARU --}}
<div class="modal fade" id="modalTambahSupplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('supplier.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header text-white border-bottom-0 py-3" style="background-color: #10b981;">
                <h6 class="modal-title fw-bold"><i class="fas fa-truck me-2"></i>Registrasi Data Supplier</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label small fw-bold text-slate-dark">Nama Supplier / PT <span class="text-danger">*</span></label>
                        <input type="text" name="nama_supplier" class="form-control bg-light" placeholder="Contoh: PT. Sumber Makmur" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-slate-dark">Nomor KTP</label>
                        <input type="text" name="ktp" class="form-control bg-light" placeholder="16 digit NIK">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-slate-dark">Nomor NPWP</label>
                        <input type="text" name="npwp" class="form-control bg-light" placeholder="Nomor NPWP perusahaan/pribadi">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-slate-dark">No. Telepon / WA</label>
                        <input type="text" name="telepon" class="form-control bg-light" placeholder="0812xxxx">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-slate-dark">Jatuh Tempo (Hari) <span class="text-danger">*</span></label>
                        <input type="number" name="jatuh_tempo_hari" class="form-control bg-light" value="30" min="0" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold text-slate-dark">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control bg-light" rows="2" placeholder="Alamat gudang supplier..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-3 border-top">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-emerald-custom rounded-pill px-4 fw-bold" style="background-color: #10b981; color: white; border: none;">Simpan Supplier Baru</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection