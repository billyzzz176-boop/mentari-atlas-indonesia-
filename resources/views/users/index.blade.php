@extends('layouts.app')

@section('content')
@php
    $daftarMenu = [
        'riwayat_so' => 'Riwayat SO',
        'approval_so' => 'Approval SO',
        'data_barang' => 'Data Barang',
        'backorder' => 'Back Order',
        'pembelian_stok' => 'Pembelian Stok',
        'return_barang' => 'Return Barang (Penjualan & Pembelian)',
        'akses_keuangan' => 'Keuangan (Piutang & Utang)',
        'tingkat_cust' => 'Data Customer',
        'data_supplier' => 'Data Supplier',
        'profit_laba' => 'Profit / Laba',
        'unduh_laporan' => 'Unduh Laporan',
        'audit_trail' => 'Audit Trail',
        'akun_staf' => 'Akun Staf',
    ];
@endphp

<style>
    /* Global Overrides untuk Tema Premium Mentari Atlas */
    body { background-color: #f8fafc !important; }
    
    .text-emerald-custom { color: #10b981 !important; }
    .text-slate-dark { color: #0f172a !important; }
    .text-slate-muted { color: #64748b !important; }
    
    .bg-emerald-custom { background-color: #10b981 !important; color: #ffffff !important; }
    .btn-emerald-custom { background-color: #10b981 !important; border-color: #10b981 !important; color: #ffffff !important; font-weight: 500; transition: all 0.2s; }
    .btn-emerald-custom:hover { background-color: #059669 !important; color: #ffffff !important; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); }
    
    /* Card & Table Styling */
    .card-custom { border: 1px solid #e2e8f0; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    
    /* Diet Ketat Tabel Biar Rapi */
    .table-mentari-compact th, .table-mentari-compact td { padding: 0.75rem 0.5rem !important; }

    /* Soft Badges */
    .badge-success-soft { background-color: #d1fae5 !important; color: #065f46 !important; border: 1px solid #a7f3d0; }
    .badge-warning-soft { background-color: #fef3c7 !important; color: #92400e !important; border: 1px solid #fde68a; }
    .badge-danger-soft { background-color: #fee2e2 !important; color: #991b1b !important; border: 1px solid #fecaca; }
    .badge-secondary-soft { background-color: #f1f5f9 !important; color: #475569 !important; border: 1px solid #cbd5e1; }
    .badge-info-soft { background-color: #cffafe !important; color: #155e75 !important; border: 1px solid #a5f3fc; }
    .badge-primary-soft { background-color: #ede9fe !important; color: #5b21b6 !important; border: 1px solid #ddd6fe; }

    /* Custom Checkbox Emerald */
    .form-check-input:checked {
        background-color: #10b981 !important;
        border-color: #10b981 !important;
    }
    .form-check-input:focus {
        border-color: #34d399;
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
    }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 80vh;">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold"><i class="fas fa-user-shield text-emerald-custom me-2"></i>Manajemen Akun Pengguna</h1>
            <p class="text-slate-muted small mb-0 mt-1">Kelola seluruh hak akses staf dan direksi Mentari Atlas Indonesia.</p>
        </div>
        <button class="btn btn-emerald-custom shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
            <i class="fas fa-user-plus me-2"></i> Tambah Akun Staf
        </button>
    </div>

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
        <div class="alert badge-success-soft alert-dismissible fade show border-0 shadow-sm border-start border-4 border-success rounded-3 px-4 py-3 mb-4">
            <i class="fas fa-check-circle text-success me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Notifikasi Error Validasi --}}
    @if ($errors->any())
        <div class="alert badge-danger-soft alert-dismissible fade show border-0 shadow-sm border-start border-4 border-danger rounded-3 px-4 py-3 mb-4">
            <i class="fas fa-exclamation-triangle text-danger me-2"></i><strong>Gagal menyimpan data!</strong> Silakan periksa kembali:
            <ul class="mb-0 mt-2 small text-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TABEL TEMA PREMIUM --}}
    <div class="table-wrapper-mentari d-none d-lg-block">
        <div class="table-responsive">
            <table class="table table-mentari table-mentari-compact align-middle mb-0" style="font-size: 0.85rem; width: 100%;">
                <thead>
                    <tr>
                        <th class="ps-4">Informasi Pengguna</th>
                        <th>Email Kontak</th>
                        <th class="text-center">Hak Akses (Role)</th>
                        <th class="text-center pe-4" style="width: 1%; white-space: nowrap;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-slate-dark" style="font-size: 0.95rem;">
                                {{ $user->name }}
                                @if(Auth::id() == $user->id) 
                                    <span class="badge badge-success-soft ms-2 shadow-sm border-0" style="font-size: 0.6rem;"><i class="fas fa-check-circle me-1"></i>AKUN ANDA</span> 
                                @endif
                            </div>
                        </td>
                        <td class="text-slate-muted fw-bold">{{ $user->email }}</td>
                        <td class="text-center">
                            @if($user->role == 'direktur' || $user->role == 'superadmin')
                                <span class="badge badge-danger-soft px-3 py-1.5 rounded-pill fw-bold shadow-sm" style="letter-spacing: 0.5px;">DIREKTUR</span>
                            @elseif($user->role == 'sales')
                                <span class="badge badge-info-soft px-3 py-1.5 rounded-pill fw-bold shadow-sm" style="letter-spacing: 0.5px;">SALES</span>
                            @elseif($user->role == 'manager')
                                <span class="badge badge-primary-soft px-3 py-1.5 rounded-pill fw-bold shadow-sm" style="letter-spacing: 0.5px;">MANAGER</span>
                            @elseif($user->role == 'admin_warehouse')
                                <span class="badge badge-success-soft px-3 py-1.5 rounded-pill fw-bold shadow-sm" style="letter-spacing: 0.5px;">WAREHOUSE</span>
                            @elseif($user->role == 'admin_keuangan')
                                <span class="badge badge-warning-soft px-3 py-1.5 rounded-pill fw-bold shadow-sm" style="letter-spacing: 0.5px;">KEUANGAN</span>
                            @else
                                <span class="badge badge-secondary-soft px-3 py-1.5 rounded-pill fw-bold shadow-sm" style="letter-spacing: 0.5px;">{{ strtoupper(str_replace('_', ' ', $user->role)) }}</span>
                            @endif
                        </td>
                        <td class="pe-4 text-center align-middle" style="white-space: nowrap;">
                            <div class="d-flex gap-2 justify-content-center align-items-center flex-nowrap">
                                <button class="btn btn-sm btn-light border px-3 rounded-pill fw-bold text-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEditUser{{ $user->id }}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>

                                @if(Auth::id() != $user->id)
                                <form action="{{ url('/users/'.$user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }} secara permanen?')" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border px-3 rounded-pill fw-bold text-danger shadow-sm">
                                        <i class="fas fa-trash me-1"></i> Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MOBILE CARDS --}}
    <div class="d-lg-none p-3" id="users-mobile-list" style="background-color: var(--bg-page);">
        @foreach($users as $user)
            <div class="card card-custom mb-3" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-slate-dark" style="font-size: 1rem;">
                            {{ $user->name }}
                        </span>
                        @if(Auth::id() == $user->id) 
                            <span class="badge badge-success-soft shadow-sm border-0" style="font-size: 0.65rem;"><i class="fas fa-check-circle me-1"></i>AKUN ANDA</span> 
                        @endif
                    </div>
                    
                    <div class="text-slate-muted mb-3" style="font-size: 0.85rem;">
                        <i class="far fa-envelope me-1"></i> {{ $user->email }}
                    </div>

                    <div class="mb-3">
                        <span class="small text-muted d-block mb-1">Akses Sistem:</span>
                        @if($user->role == 'direktur' || $user->role == 'superadmin')
                            <span class="badge badge-danger-soft px-2.5 py-1 rounded fw-bold shadow-sm" style="font-size: 0.7rem; letter-spacing: 0.5px;">DIREKTUR</span>
                        @elseif($user->role == 'sales')
                            <span class="badge badge-info-soft px-2.5 py-1 rounded fw-bold shadow-sm" style="font-size: 0.7rem; letter-spacing: 0.5px;">SALES</span>
                        @elseif($user->role == 'manager')
                            <span class="badge badge-primary-soft px-2.5 py-1 rounded fw-bold shadow-sm" style="font-size: 0.7rem; letter-spacing: 0.5px;">MANAGER</span>
                        @elseif($user->role == 'admin_warehouse')
                            <span class="badge badge-success-soft px-2.5 py-1 rounded fw-bold shadow-sm" style="font-size: 0.7rem; letter-spacing: 0.5px;">WAREHOUSE</span>
                        @elseif($user->role == 'admin_keuangan')
                            <span class="badge badge-warning-soft px-2.5 py-1 rounded fw-bold shadow-sm" style="font-size: 0.7rem; letter-spacing: 0.5px;">KEUANGAN</span>
                        @else
                            <span class="badge badge-secondary-soft px-2.5 py-1 rounded fw-bold shadow-sm" style="font-size: 0.7rem; letter-spacing: 0.5px;">{{ strtoupper(str_replace('_', ' ', $user->role)) }}</span>
                        @endif
                    </div>

                    <div class="d-flex gap-2 border-top pt-3">
                        <button class="btn btn-sm btn-outline-primary flex-fill fw-bold rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEditUser{{ $user->id }}">
                            <i class="fas fa-edit me-1"></i> Edit
                        </button>

                        @if(Auth::id() != $user->id)
                        <form action="{{ url('/users/'.$user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }} secara permanen?')" class="m-0 flex-fill">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100 fw-bold rounded-pill shadow-sm">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- MODAL EDIT USER (DITEMPATKAN DI LUAR TABLE SEHINGGA BISA DIAKSES MOBILE/DESKTOP) --}}
    @foreach($users as $user)
    <div class="modal fade" id="modalEditUser{{ $user->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered text-start" style="white-space: normal;">
            <form action="{{ url('/users/'.$user->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf @method('PUT')
                <div class="modal-header bg-light border-bottom py-3">
                    <h6 class="modal-title fw-bold text-slate-dark"><i class="fas fa-user-edit me-2 text-emerald-custom"></i>Edit Akses: {{ $user->name }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-slate-dark">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control bg-light" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-slate-dark">Email Login</label>
                        <input type="email" name="email" class="form-control bg-light" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-slate-dark">Tentukan Role</label>
                        <select name="role" class="form-select bg-light fw-bold text-slate-dark" required onchange="toggleHakAksesEdit(this, {{ $user->id }})">
                            <option value="direktur" {{ $user->role == 'direktur' ? 'selected' : '' }}>Direktur Utama</option>
                            <option value="manager" {{ $user->role == 'manager' ? 'selected' : '' }}>Manager / Otorisator</option>
                            <option value="admin_keuangan" {{ in_array($user->role, ['admin_keuangan', 'keuangan']) ? 'selected' : '' }}>Admin Keuangan / Penagihan</option>
                            <option value="admin_warehouse" {{ in_array($user->role, ['admin_warehouse', 'warehouse']) ? 'selected' : '' }}>Admin Gudang / Logistik</option>
                            <option value="sales" {{ $user->role == 'sales' ? 'selected' : '' }}>Staf Sales / Marketing</option>
                        </select>
                    </div>

                    {{-- CHECKBOX HAK AKSES MENU EDIT --}}
                    <div class="mb-3 mt-4 pt-3 border-top hak-akses-container-edit-{{ $user->id }}" style="{{ in_array(strtolower($user->role), ['direktur', 'superadmin', 'admin', 'sales']) ? 'display: none;' : '' }}">
                        <label class="form-label small fw-bold text-slate-dark mb-3"><i class="fas fa-check-square text-emerald-custom me-1"></i>Izin Akses Menu Khusus</label>
                        <div class="row px-2">
                            @foreach($daftarMenu as $val => $label)
                                <div class="col-6 mb-2">
                                    <div class="form-check text-start">
                                        <input class="form-check-input shadow-sm border-secondary" type="checkbox" name="hak_akses[]" value="{{ $val }}" id="edit_{{ $user->id }}_{{ $val }}" {{ in_array($val, $user->hak_akses ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label small text-slate-dark fw-medium" for="edit_{{ $user->id }}_{{ $val }}" style="cursor: pointer;">
                                            {{ $label }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-3 mt-4 border-top">
                        <label class="form-label small fw-bold text-danger"><i class="fas fa-key me-1"></i>Reset Password (Opsional)</label>
                        <input type="password" name="password" class="form-control bg-light border-danger border-opacity-25" placeholder="Ketik password baru (min. 8 karakter) jika ingin diubah">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-3">
                    <button type="button" class="btn btn-outline-secondary fw-bold rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-emerald-custom fw-bold rounded-pill px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>

{{-- MODAL TAMBAH USER --}}
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ url('/users') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div class="modal-header bg-emerald-custom text-white border-bottom-0 py-3">
                <h6 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>Registrasi Akun Baru</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-slate-dark">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control bg-light" placeholder="Masukkan nama staf" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-slate-dark">Email Resmi <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control bg-light" placeholder="contoh: staf@mentariatlas.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-slate-dark">Password Default <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control bg-light" placeholder="Minimal 8 karakter rahasia" required minlength="8">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-slate-dark">Tentukan Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select bg-light fw-bold text-slate-dark" required id="selectRoleTambah" onchange="toggleHakAksesTambah(this)">
                        <option value="" disabled selected>-- Pilih Divisi --</option>
                        <option value="direktur">Direktur Utama</option>
                        <option value="manager">Manager / Otorisator</option>
                        <option value="admin_keuangan">Admin Keuangan / Penagihan</option>
                        <option value="admin_warehouse">Admin Gudang / Logistik</option>
                        <option value="sales">Staf Sales / Marketing</option>
                    </select>
                </div>

                {{-- CHECKBOX HAK AKSES MENU TAMBAH --}}
                <div class="mb-0 mt-4 pt-3 border-top" id="hakAksesContainerTambah">
                    <label class="form-label small fw-bold text-slate-dark mb-3"><i class="fas fa-check-square text-emerald-custom me-1"></i>Izin Akses Menu Khusus</label>
                    <div class="row px-2">
                        @foreach($daftarMenu as $val => $label)
                            <div class="col-6 mb-2">
                                <div class="form-check text-start">
                                    <input class="form-check-input shadow-sm border-secondary" type="checkbox" name="hak_akses[]" value="{{ $val }}" id="add_{{ $val }}">
                                    <label class="form-check-label small text-slate-dark fw-medium" for="add_{{ $val }}" style="cursor: pointer;">
                                        {{ $label }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-light border-top py-3">
                <button type="submit" class="btn btn-emerald-custom w-100 fw-bold rounded-pill py-2 shadow-sm">
                    <i class="fas fa-check-circle me-1"></i> Daftarkan Akun
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    function toggleHakAksesEdit(selectObj, userId) {
        var container = document.querySelector('.hak-akses-container-edit-' + userId);
        if (selectObj.value === 'sales' || selectObj.value === 'direktur') {
            container.style.display = 'none';
            // Uncheck all boxes if hidden
            var checkboxes = container.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(function(cb) { cb.checked = false; });
        } else {
            container.style.display = 'block';
        }
    }

        document.getElementById('role-select-tambah').addEventListener('change', function() {
            var val = this.value.toLowerCase();
            var container = document.getElementById('hak-akses-container-tambah');
            // Tampilkan hak akses jika role BUKAN direktur/admin dan BUKAN sales
            if (['direktur', 'superadmin', 'admin', 'sales'].includes(val) || val === '') {
                container.style.display = 'none';
                var checkboxes = container.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(function(cb) { cb.checked = false; });
            } else {
                container.style.display = 'block';
            }
        });

    // Trigger initial check for Tambah User form
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('hak-akses-container-tambah').style.display = 'none';
    });
</script>
@endsection