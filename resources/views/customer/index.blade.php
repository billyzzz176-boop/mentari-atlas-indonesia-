@extends('layouts.app')

@section('content')
<style>
    /* Soft Tier Badges */
    .badge-gold { background: linear-gradient(135deg, #fef08a 0%, #eab308 100%); color: #713f12 !important; border: 1px solid #fde047; box-shadow: 0 2px 4px rgba(234, 179, 8, 0.2); }
    .badge-silver { background: linear-gradient(135deg, #f1f5f9 0%, #cbd5e1 100%); color: #334155 !important; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(148, 163, 184, 0.2); }
    .badge-bronze { background: linear-gradient(135deg, #ffedd5 0%, #f97316 100%); color: #7c2d12 !important; border: 1px solid #fed7aa; box-shadow: 0 2px 4px rgba(249, 115, 22, 0.2); }

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

    /* Custom Select untuk Tier */
    .select-tier-custom { 
        border: 1px solid #a7f3d0; 
        background-color: #ecfdf5; 
        color: #047857; 
        font-weight: 600; 
        cursor: pointer; 
        transition: all 0.2s ease; 
    }
    .select-tier-custom:focus { border-color: #10b981; box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.15); background-color: #ffffff; }

    .table-mentari-compact th, .table-mentari-compact td { padding: 0.75rem 0.5rem !important; }
    
    /* Photo Viewer in Modal */
    .img-preview-box { width: 100%; height: 180px; object-fit: cover; border-radius: 0.5rem; border: 1px solid #e2e8f0; background-color: #f8fafc; }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 80vh;">
    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold"><i class="fas fa-users text-emerald-custom me-2"></i>Manajemen Data Customer</h1>
            <p class="text-slate-muted small mb-0 mt-1">Kelola data pelanggan, dokumen legalitas, plafon kredit, dan tingkatan tier.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form action="{{ route('customer.index') }}" method="GET" class="m-0 d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3 bg-white border" placeholder="Cari ID/Nama..." value="{{ $search ?? '' }}" style="width: 200px; height: 38px;">
                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">Cari</button>
                @if(!empty($search))
                    <a href="{{ route('customer.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold d-flex align-items-center">Reset</a>
                @endif
            </form>
            <button class="btn btn-emerald-custom shadow-sm rounded-pill px-4 fw-bold" style="background-color: #10b981; color: white; border: none; height: 38px;" data-bs-toggle="modal" data-bs-target="#modalTambahCustomer">
                <i class="fas fa-plus me-2"></i> Tambah Customer
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
            <i class="fas fa-exclamation-circle text-danger me-2"></i><strong>Gagal memproses data!</strong> Silakan cek kembali.
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TABEL TEMA PREMIUM --}}
    <div class="table-wrapper-mentari">
        <div class="table-responsive d-none d-lg-block">
            <table class="table table-mentari table-mentari-compact align-middle mb-0" style="font-size: 0.85rem; width: 100%;">
                <thead>
                    <tr>
                        <th class="ps-3 text-nowrap">Kode Customer</th>
                        <th>Nama Customer / Toko</th>
                        <th>No. Telepon</th>
                        <th>Plafon (Limit Kredit)</th>
                        <th class="text-center">Termin Bayar</th>
                        <th class="text-center">Tingkatan Saat Ini</th>
                        <th class="text-center pe-3" style="width: 1%; white-space: nowrap;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                    <tr>
                        <td class="ps-3 fw-bold text-emerald-custom text-nowrap">{{ $c->id_cust }}</td>
                        <td class="fw-bold text-slate-dark">{{ $c->nama_customer }}</td>
                        <td class="text-slate-muted">{{ $c->no_telp ?: '-' }}</td>
                        <td class="fw-bold text-danger">Rp {{ number_format($c->plafon, 0, ',', '.') }}</td>
                        <td class="text-center"><span class="badge bg-warning text-dark rounded-pill px-2 py-1">{{ $c->tempo_hari ?? 30 }} Hari</span></td>
                        <td class="text-center">
                            <div class="mb-2">
                                @if(($c->tingkat_customer ?? 'Bronze') === 'Gold')
                                    <span class="badge badge-gold rounded-pill px-3 py-2 fw-bold shadow-sm" style="letter-spacing: 0.5px;"><i class="fas fa-crown me-1"></i>GOLD</span>
                                @elseif(($c->tingkat_customer ?? 'Bronze') === 'Silver')
                                    <span class="badge badge-silver rounded-pill px-3 py-2 fw-bold shadow-sm" style="letter-spacing: 0.5px;"><i class="fas fa-medal me-1 text-secondary"></i>SILVER</span>
                                @else
                                    <span class="badge badge-bronze rounded-pill px-3 py-2 fw-bold shadow-sm" style="letter-spacing: 0.5px;"><i class="fas fa-award me-1"></i>BRONZE</span>
                                @endif
                            </div>
                            <form action="{{ route('customer.updateTier', $c->id) }}" method="POST" class="m-0 d-flex justify-content-center align-items-center gap-1">
                                @csrf
                                <select name="tingkat_customer" class="form-select form-select-sm rounded select-tier-custom shadow-none" style="width: 85px; font-size: 0.7rem; height: 28px; padding-top: 2px; padding-bottom: 2px;" required>
                                    <option value="Bronze" {{ ($c->tingkat_customer ?? 'Bronze') == 'Bronze' ? 'selected' : '' }}>Bronze</option>
                                    <option value="Silver" {{ ($c->tingkat_customer ?? 'Bronze') == 'Silver' ? 'selected' : '' }}>Silver</option>
                                    <option value="Gold" {{ ($c->tingkat_customer ?? 'Bronze') == 'Gold' ? 'selected' : '' }}>Gold</option>
                                </select>
                                <button type="submit" class="btn-action-mentari" style="width: 28px; height: 28px;" title="Simpan Tingkatan">
                                    <i class="fas fa-check" style="font-size: 0.7rem;"></i>
                                </button>
                            </form>
                        </td>
                        <td class="pe-3 text-center align-middle" style="white-space: nowrap;">
                            <div class="d-flex justify-content-center align-items-center gap-2 flex-nowrap">
                                
                                {{-- Tombol Detail --}}
                                <button type="button" class="btn-action-mentari" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $c->id }}" title="Lihat Rincian">
                                    <i class="fas fa-eye"></i>
                                </button>

                                {{-- Tombol Edit --}}
                                <button type="button" class="btn-action-mentari" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $c->id }}" title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('customer.destroy', $c->id) }}" method="POST" class="m-0 ms-1">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-action-mentari btn-delete" title="Hapus Customer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-slate-muted bg-white">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-users-slash fa-3x mb-3 text-muted opacity-25"></i>
                                <span>Belum ada data customer yang terdaftar.</span>
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
                    @forelse($customers as $c)
                    <div class="card card-custom mb-3" style="border-radius: 16px;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-emerald-custom fw-bold px-2 py-1" style="border-radius: 8px;">{{ $c->id_cust }}</span>
                                <div>
                                    @if(($c->tingkat_customer ?? 'Bronze') === 'Gold')
                                        <span class="badge badge-gold rounded-pill px-2 py-1 shadow-sm" style="font-size:0.6rem;"><i class="fas fa-crown me-1"></i>GOLD</span>
                                    @elseif(($c->tingkat_customer ?? 'Bronze') === 'Silver')
                                        <span class="badge badge-silver rounded-pill px-2 py-1 shadow-sm" style="font-size:0.6rem;"><i class="fas fa-medal me-1 text-secondary"></i>SILVER</span>
                                    @else
                                        <span class="badge badge-bronze rounded-pill px-2 py-1 shadow-sm" style="font-size:0.6rem;"><i class="fas fa-award me-1"></i>BRONZE</span>
                                    @endif
                                </div>
                            </div>
                            
                            <h6 class="fw-bold text-slate-dark mb-1" style="font-size: 1.05rem;">{{ $c->nama_customer }}</h6>
                            <div class="text-muted mb-3" style="font-size: 0.8rem;"><i class="fas fa-phone-alt me-1"></i>{{ $c->no_telp ?: '-' }}</div>
                            
                            <div class="d-flex justify-content-between mb-3 bg-light p-2" style="border-radius: 12px; border: 1px solid var(--border-panel);">
                                <div><small class="text-muted d-block" style="font-size:0.65rem;">Plafon / Limit</small><span class="fw-bold text-danger" style="font-size:0.85rem;">Rp {{ number_format($c->plafon, 0, ',', '.') }}</span></div>
                                <div class="text-end"><small class="text-muted d-block" style="font-size:0.65rem;">Termin</small><span class="badge bg-warning text-dark px-2 py-1">{{ $c->tempo_hari ?? 30 }} Hari</span></div>
                            </div>
                            
                            <form action="{{ route('customer.updateTier', $c->id) }}" method="POST" class="mb-3 d-flex gap-2">
                                @csrf
                                <select name="tingkat_customer" class="form-select form-select-sm rounded select-tier-custom shadow-none" required>
                                    <option value="Bronze" {{ ($c->tingkat_customer ?? 'Bronze') == 'Bronze' ? 'selected' : '' }}>Tier Bronze</option>
                                    <option value="Silver" {{ ($c->tingkat_customer ?? 'Bronze') == 'Silver' ? 'selected' : '' }}>Tier Silver</option>
                                    <option value="Gold" {{ ($c->tingkat_customer ?? 'Bronze') == 'Gold' ? 'selected' : '' }}>Tier Gold</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-emerald-custom flex-shrink-0" style="border-radius: 8px;"><i class="fas fa-check"></i></button>
                            </form>
                            
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-primary-soft flex-fill fw-bold py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $c->id }}"><i class="fas fa-eye me-1"></i>Detail</button>
                                <button type="button" class="btn btn-sm btn-warning-soft flex-fill fw-bold py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $c->id }}"><i class="fas fa-pen me-1"></i>Edit</button>
                                <form action="{{ route('customer.destroy', $c->id) }}" method="POST" class="m-0 flex-fill">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger-soft w-100 fw-bold py-2" style="border-radius: 10px;" onclick="return confirm('Hapus customer ini?')"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted bg-white shadow-sm" style="border-radius: 16px;">Belum ada data customer.</div>
                    @endforelse
                </div>

</div>

{{-- =========================================================================
    KUMPULAN MODAL (DILETAKKAN DI LUAR TABEL AGAR LAYOUT TIDAK HANCUR)
========================================================================== --}}

@foreach($customers as $c)
    {{-- MODAL DETAIL CUSTOMER --}}
    <div class="modal fade" id="modalDetail{{ $c->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light border-bottom py-3">
                    <h6 class="modal-title fw-bold text-slate-dark"><i class="fas fa-id-card text-info me-2"></i>Rincian Data: {{ $c->nama_customer }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3"><span class="small text-muted d-block">Kode Customer</span><span class="fw-bold">{{ $c->id_cust }}</span></div>
                            <div class="mb-3"><span class="small text-muted d-block">Nomor Telepon</span><span class="fw-bold">{{ $c->no_telp ?: '-' }}</span></div>
                            <div class="mb-3"><span class="small text-muted d-block">Plafon Kredit</span><span class="fw-bold text-danger">Rp {{ number_format($c->plafon, 0, ',', '.') }}</span></div>
                            <div class="mb-3"><span class="small text-muted d-block">Sisa Plafon Kredit (Sisa Limit)</span><span class="fw-bold text-success">Rp {{ number_format($c->sisa_plafon, 0, ',', '.') }}</span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3"><span class="small text-muted d-block">Nomor KTP</span><span class="fw-bold">{{ $c->ktp ?: '-' }}</span></div>
                            <div class="mb-3"><span class="small text-muted d-block">Nomor NPWP</span><span class="fw-bold">{{ $c->npwp ?: '-' }}</span></div>
                            <div class="mb-3"><span class="small text-muted d-block">Termin Jatuh Tempo</span><span class="fw-bold text-danger">{{ $c->tempo_hari ?? 30 }} Hari</span></div>
                            <div class="mb-3"><span class="small text-muted d-block">Alamat Lengkap</span><span class="fw-bold">{{ $c->alamat ?: '-' }}</span></div>
                        </div>
                        
                        <div class="col-12 mt-2 pt-3 border-top">
                            <h6 class="fw-bold text-slate-dark mb-3">Dokumen Lampiran</h6>
                            <div class="row g-3">
                                <div class="col-md-4 text-center">
                                    <span class="small text-muted d-block mb-2">Foto KTP</span>
                                    @if($c->foto_ktp)
                                        <a href="{{ route('berkas', $c->foto_ktp) }}" target="_blank"><img src="{{ route('berkas', $c->foto_ktp) }}" class="img-preview-box shadow-sm"></a>
                                    @else
                                        <div class="img-preview-box d-flex align-items-center justify-content-center text-muted"><i class="fas fa-image fa-2x opacity-25"></i></div>
                                    @endif
                                </div>
                                <div class="col-md-4 text-center">
                                    <span class="small text-muted d-block mb-2">Foto NPWP</span>
                                    @if($c->foto_npwp)
                                        <a href="{{ route('berkas', $c->foto_npwp) }}" target="_blank"><img src="{{ route('berkas', $c->foto_npwp) }}" class="img-preview-box shadow-sm"></a>
                                    @else
                                        <div class="img-preview-box d-flex align-items-center justify-content-center text-muted"><i class="fas fa-image fa-2x opacity-25"></i></div>
                                    @endif
                                </div>
                                <div class="col-md-4 text-center">
                                    <span class="small text-muted d-block mb-2">Foto Toko</span>
                                    @if($c->foto_toko)
                                        <a href="{{ route('berkas', $c->foto_toko) }}" target="_blank"><img src="{{ route('berkas', $c->foto_toko) }}" class="img-preview-box shadow-sm"></a>
                                    @else
                                        <div class="img-preview-box d-flex align-items-center justify-content-center text-muted"><i class="fas fa-image fa-2x opacity-25"></i></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3 border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT CUSTOMER --}}
    <div class="modal fade" id="modalEdit{{ $c->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('customer.update', $c->id) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
                @csrf @method('PUT')
                <div class="modal-header bg-light border-bottom py-3">
                    <h6 class="modal-title fw-bold text-slate-dark"><i class="fas fa-user-edit text-warning me-2"></i>Edit Data: {{ $c->nama_customer }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-dark">Nama Toko / Customer <span class="text-danger">*</span></label>
                            <input type="text" name="nama_customer" class="form-control bg-light" value="{{ $c->nama_customer }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-dark">Nomor Telepon</label>
                            <input type="text" name="no_telp" class="form-control bg-light" value="{{ $c->no_telp }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-dark">Nomor KTP</label>
                            <input type="text" name="ktp" class="form-control bg-light" value="{{ $c->ktp }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-dark">Nomor NPWP</label>
                            <input type="text" name="npwp" class="form-control bg-light" value="{{ $c->npwp }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-slate-dark">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control bg-light" rows="2">{{ $c->alamat }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-dark">Plafon (Limit Kredit Utang)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-slate-muted border-end-0">Rp</span>
                                <input type="number" name="plafon" class="form-control bg-light border-start-0" value="{{ $c->plafon }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-dark">Batas Tempo (Hari)</label>
                            <div class="input-group">
                                <input type="number" name="tempo_hari" class="form-control bg-light border-end-0" value="{{ $c->tempo_hari ?? 30 }}">
                                <span class="input-group-text bg-light fw-bold text-slate-muted border-start-0">Hari</span>
                            </div>
                        </div>
                        <div class="col-12 mt-3"><hr class="my-1"></div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-slate-dark">Upload Foto KTP Baru</label>
                            <input type="file" name="foto_ktp" class="form-control form-control-sm" accept="image/*">
                            <small class="text-muted" style="font-size: 0.65rem;">Biarkan kosong jika tidak ingin mengubah</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-slate-dark">Upload Foto NPWP Baru</label>
                            <input type="file" name="foto_npwp" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-slate-dark">Upload Foto Toko Baru</label>
                            <input type="file" name="foto_toko" class="form-control form-control-sm" accept="image/*">
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

{{-- MODAL TAMBAH CUSTOMER BARU --}}
<div class="modal fade" id="modalTambahCustomer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('customer.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header text-white border-bottom-0 py-3" style="background-color: #10b981;">
                <h6 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>Registrasi Data Customer</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-slate-dark">Nama Toko / Customer <span class="text-danger">*</span></label>
                        <input type="text" name="nama_customer" class="form-control bg-light" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-slate-dark">Nomor Telepon</label>
                        <input type="text" name="no_telp" class="form-control bg-light">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-slate-dark">Nomor KTP</label>
                        <input type="text" name="ktp" class="form-control bg-light">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-slate-dark">Nomor NPWP</label>
                        <input type="text" name="npwp" class="form-control bg-light">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold text-slate-dark">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control bg-light" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-slate-dark">Plafon (Limit Kredit Utang)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-slate-muted border-end-0">Rp</span>
                            <input type="number" name="plafon" class="form-control bg-light border-start-0" value="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-slate-dark">Batas Tempo (Hari)</label>
                        <div class="input-group">
                            <input type="number" name="tempo_hari" class="form-control bg-light border-end-0" value="30">
                            <span class="input-group-text bg-light fw-bold text-slate-muted border-start-0">Hari</span>
                        </div>
                    </div>
                    <div class="col-12 mt-3"><hr class="my-1"></div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-slate-dark">Upload Foto KTP</label>
                        <input type="file" name="foto_ktp" class="form-control form-control-sm" accept="image/*">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-slate-dark">Upload Foto NPWP</label>
                        <input type="file" name="foto_npwp" class="form-control form-control-sm" accept="image/*">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-slate-dark">Upload Foto Toko</label>
                        <input type="file" name="foto_toko" class="form-control form-control-sm" accept="image/*">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-3 border-top">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-emerald-custom rounded-pill px-4 fw-bold" style="background-color: #10b981; color: white; border: none;">Simpan Customer Baru</button>
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