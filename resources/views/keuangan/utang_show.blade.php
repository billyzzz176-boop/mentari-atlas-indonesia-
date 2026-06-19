@extends('layouts.app')

@section('content')
<style>
    /* Global Overrides untuk Tema Premium Mentari Atlas */
    body { background-color: #f8fafc !important; }
    
    .text-emerald-custom { color: #10b981 !important; }
    .text-slate-dark { color: #0f172a !important; }
    .text-slate-muted { color: #64748b !important; }
    
    .bg-emerald-custom { background-color: #10b981 !important; color: #ffffff !important; }
    .bg-emerald-soft { background-color: #ecfdf5 !important; }
    
    .btn-emerald-custom { background-color: #10b981 !important; border-color: #10b981 !important; color: #ffffff !important; font-weight: 500; transition: all 0.2s; }
    .btn-emerald-custom:hover { background-color: #059669 !important; color: #ffffff !important; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); }
    
    /* Card & Table Styling */
    .card-custom { border: 1px solid #e2e8f0; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .table-custom-header th { background-color: #f8fafc !important; color: #475569 !important; font-weight: 700 !important; border-bottom: 2px solid #e2e8f0 !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding-top: 1rem; padding-bottom: 1rem; }
    
    /* Soft Badges */
    .badge-success-soft { background-color: #d1fae5 !important; color: #065f46 !important; border: 1px solid #a7f3d0; }
    .badge-danger-soft { background-color: #fee2e2 !important; color: #991b1b !important; border: 1px solid #fecaca; }
    .badge-warning-soft { background-color: #fef3c7 !important; color: #92400e !important; border: 1px solid #fde68a; }
    .badge-secondary-soft { background-color: #f1f5f9 !important; color: #475569 !important; border: 1px solid #cbd5e1; }
    
    /* Hover Row */
    .table-hover tbody tr { transition: background-color 0.2s; }
    .table-hover tbody tr:hover { background-color: #f8fafc !important; }
    
    /* Nominal Fonts */
    .font-monospace-custom { font-family: 'Courier New', Courier, monospace; font-weight: 700; letter-spacing: -0.5px; }

    /* Custom Form Inputs */
    .form-control-custom { border-radius: 0.5rem; border: 1px solid #cbd5e1; padding: 0.75rem 1rem; }
    .form-control-custom:focus { border-color: #10b981; box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25); }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 80vh;">
    
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold"><i class="fas fa-file-invoice-dollar text-emerald-custom me-2"></i>Detail Pengeluaran & Cicilan Utang</h1>
            <p class="text-slate-muted small mb-0 mt-1">Kelola pembayaran uang keluar dan pantau historis cicilan kepada supplier ini.</p>
        </div>
        <a href="{{ route('keuangan.utang.index') }}" class="btn btn-light fw-bold shadow-sm rounded-pill px-4" style="border: 1px solid #cbd5e1; color: #475569;">
            <i class="fas fa-arrow-left me-1.5"></i> Kembali
        </a>
    </div>

    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert badge-success-soft alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3 px-4 py-3" role="alert">
            <i class="fas fa-check-circle text-success me-2"></i><strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert badge-danger-soft alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3 px-4 py-3" role="alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-exclamation-triangle text-danger me-2 mt-1"></i>
                <div>
                    <strong>Gagal Proses!</strong>
                    <ul class="mb-0 mt-1 ps-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 mb-4">
        {{-- KOLOM KIRI: Informasi Tagihan Utang --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card card-custom bg-white h-100 overflow-hidden" style="border-top: 4px solid #10b981;">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h6 class="m-0 fw-bold text-slate-dark"><i class="fas fa-info-circle text-emerald-custom me-2"></i>Rincian Utang</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <small class="text-slate-muted fw-bold text-uppercase" style="letter-spacing: 0.5px;">No. Invoice Tagihan / Jurnal</small>
                        <div class="fs-5 text-emerald-custom fw-bold">{{ $utang->no_utang_jurnal ?? '-' }}</div>
                    </div>
                    <div class="mb-4">
                        <small class="text-slate-muted fw-bold text-uppercase" style="letter-spacing: 0.5px;">No. Purchase Order</small>
                        <div class="fs-6 text-slate-dark"><span class="badge badge-secondary-soft rounded-pill px-3 py-1">{{ $utang->pembelian->no_pembelian ?? '-' }}</span></div>
                    </div>
                    <div class="mb-4">
                        <small class="text-slate-muted fw-bold text-uppercase" style="letter-spacing: 0.5px;">Supplier / Vendor</small>
                        <div class="fs-5 text-slate-dark fw-bold">{{ $utang->pembelian->nama_supplier ?? '-' }}</div>
                    </div>
                    <div class="mb-4">
                        <small class="text-slate-muted fw-bold text-uppercase" style="letter-spacing: 0.5px;"><i class="far fa-calendar-alt me-1 text-danger"></i>Tanggal Jatuh Tempo</small>
                        <div class="fs-6 text-danger fw-bold">{{ \Carbon\Carbon::parse($utang->tanggal_jatuh_tempo)->format('d F Y') }}</div>
                    </div>
                    
                    <hr class="border-secondary-subtle my-4">

                    @php
                        // PERBAIKAN RUMUS AKUNTANSI: Murni mengambil angka dari DB Utang
                        $totalDN = $utang->potongan_dn ?? 0;
                        $tagihanAwalSebelumDN = $utang->total_utang; // Ini adalah Harga kotor saat PO
                        $utangBersih = $tagihanAwalSebelumDN - $totalDN; // Ini harga setelah diretur
                        $sisa_tagihan = $utangBersih - $utang->total_dibayar; // Ini yang wajib dibayar
                    @endphp

                    {{-- RINCIAN TAGIHAN: Dibuat sangat rinci persis seperti Piutang Customer --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-slate-muted fw-medium">Total Utang Awal:</span>
                        <span class="fw-bold text-slate-dark font-monospace-custom" style="font-size: 1.1rem;">Rp {{ number_format($tagihanAwalSebelumDN, 0, ',', '.') }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-slate-muted fw-medium"><i class="fas fa-tags text-warning me-1"></i> Potongan Retur / DN:</span>
                        <span class="fw-bold text-warning font-monospace-custom" style="font-size: 1.1rem;">- Rp {{ number_format($totalDN, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2 border-top border-light pt-2">
                        <span class="text-slate-muted fw-bold">Utang Bersih (Nett):</span>
                        <span class="fw-bold text-slate-dark font-monospace-custom" style="font-size: 1.1rem;">Rp {{ number_format($utangBersih, 0, ',', '.') }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                        <span class="text-slate-muted fw-medium">Total Uang Keluar (Terbayar):</span>
                        <span class="fw-bold text-success font-monospace-custom" style="font-size: 1.1rem;">Rp {{ number_format($utang->total_dibayar, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mt-4" style="background-color: #fef2f2; border: 1px dashed #fecaca;">
                        <span class="fw-bold text-danger">SISA UTANG WAJIB BAYAR:</span>
                        <span class="fw-bold text-danger font-monospace-custom fs-4">Rp {{ number_format(max(0, $sisa_tagihan), 0, ',', '.') }}</span>
                    </div>

                    <div class="mt-4 pt-2">
                        <small class="text-slate-muted fw-bold d-block mb-2 text-center text-uppercase" style="letter-spacing: 1px;">Status Saat Ini</small>
                        @if($utang->status_bayar == 'lunas' || strtolower($utang->status_bayar) == 'lunas' || $sisa_tagihan <= 0)
                            <div class="badge bg-emerald-custom text-white fs-6 px-4 py-3 w-100 rounded-pill shadow-sm d-flex justify-content-center align-items-center">
                                <i class="fas fa-check-double me-2 fs-5"></i> <span style="letter-spacing: 2px;">LUNAS</span>
                            </div>
                        @elseif(strtolower($utang->status_bayar) == 'cicil')
                            <div class="badge badge-warning-soft text-dark fs-6 px-4 py-3 w-100 rounded-pill shadow-sm d-flex justify-content-center align-items-center" style="color: #92400e !important; border-color: #fcd34d;">
                                <i class="fas fa-hourglass-half me-2 fs-5"></i> <span style="letter-spacing: 2px;">DICICIL</span>
                            </div>
                        @else
                            <div class="badge badge-danger-soft text-danger fs-6 px-4 py-3 w-100 rounded-pill shadow-sm d-flex justify-content-center align-items-center">
                                <i class="fas fa-times-circle me-2 fs-5"></i> <span style="letter-spacing: 1px;">BELUM DIBAYAR</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Form Input Pembayaran --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card card-custom bg-white h-100 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h6 class="m-0 fw-bold text-slate-dark"><i class="fas fa-cash-register text-emerald-custom me-2"></i>Input Pembayaran Cicilan Baru</h6>
                </div>
                <div class="card-body p-4">
                    @if(strtolower($utang->status_bayar) == 'lunas' || $sisa_tagihan <= 0)
                        <div class="text-center py-5 h-100 d-flex flex-column justify-content-center align-items-center bg-emerald-soft rounded-4 border" style="border-color: #a7f3d0 !important;">
                            <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                                <i class="fas fa-check text-emerald-custom" style="font-size: 3rem;"></i>
                            </div>
                            <h3 class="fw-bold text-emerald-custom mb-2">Utang Sudah Lunas!</h3>
                            <p class="text-slate-muted mb-0">Bagus! Tidak ada lagi sisa utang yang perlu dibayarkan ke supplier ini.</p>
                        </div>
                    @else
                        <form action="{{ route('keuangan.utang.bayar', $utang->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-dark small">Nominal Bayar *</label>
                                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text bg-light border-secondary-subtle fw-bold text-slate-muted px-3">Rp</span>
                                        <input type="number" name="jumlah_bayar" class="form-control form-control-lg text-end font-monospace-custom text-emerald-custom border-secondary-subtle" style="font-size: 1.25rem;" placeholder="0" max="{{ $sisa_tagihan }}" required>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <small class="text-slate-muted"><i class="fas fa-info-circle me-1"></i>Masukkan angka saja</small>
                                        <small class="text-danger fw-bold">Max: Rp {{ number_format($sisa_tagihan, 0, ',', '.') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-dark small">Pilih Metode Pembayaran *</label>
                                    <select name="metode_pembayaran" id="metode_pembayaran" class="form-select form-control-custom form-select-lg bg-light" style="font-size: 1rem;" onchange="toggleMetodeLainnya()" required>
                                        <option value="" selected disabled>-- Pilih Bank / Tunai --</option>
                                        <option value="Transfer BCA">💳 Transfer BCA</option>
                                        <option value="Transfer Mandiri">💳 Transfer Mandiri</option>
                                        <option value="Transfer BRI">💳 Transfer BRI</option>
                                        <option value="Tunai / Cash">💵 Tunai / Cash</option>
                                        <option value="Giro / Cek">📄 Giro / Cek</option>
                                        <option value="Lainnya">✏️ Lainnya (Isi Manual)</option>
                                    </select>
                                    
                                    {{-- Input Muncul Dinamis Jika Pilih Lainnya --}}
                                    <div class="mt-2" id="div_metode_lainnya" style="display: none;">
                                        <input type="text" name="metode_pembayaran_lainnya" id="metode_pembayaran_lainnya" class="form-control border-secondary-subtle shadow-sm" placeholder="Contoh: Transfer BNI, Dana, dll.">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-slate-dark small">Keterangan / Catatan Tambahan <span class="text-slate-muted fw-normal">(Opsional)</span></label>
                                <textarea name="keterangan" class="form-control form-control-custom shadow-sm" rows="2" placeholder="Contoh: Pembayaran tahap pertama ke supplier, ditransfer via m-banking, dll."></textarea>
                            </div>
                            
                            <div class="mb-4 p-3 rounded-3" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                                <label class="form-label fw-bold text-slate-dark small"><i class="fas fa-camera me-1 text-slate-muted"></i> Upload Bukti Transfer / Bayar <span class="text-slate-muted fw-normal">(Opsional)</span></label>
                                <input type="file" name="bukti_bayar" class="form-control bg-white" accept="image/jpeg, image/png, image/jpg">
                                <small class="text-slate-muted d-block mt-2"><i class="fas fa-info-circle me-1"></i> Format: JPG, JPEG, atau PNG. Ukuran Maksimal File: 2 MB.</small>
                            </div>

                            <div class="text-end pt-2">
                                <button type="submit" class="btn btn-emerald-custom btn-lg px-5 shadow-sm rounded-pill w-100 w-md-auto" onclick="return confirm('Konfirmasi: Proses input pembayaran utang ini? Pastikan dana sudah ditransfer ke supplier dengan valid.')">
                                    <i class="fas fa-save me-2"></i> Simpan Pembayaran Keluar
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL ARUS KAS: Riwayat Sejarah Cicilan Uang Tunai/Transfer --}}
    <div class="card card-custom bg-white overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h6 class="m-0 fw-bold text-slate-dark"><i class="fas fa-history text-emerald-custom me-2"></i>Riwayat Arus Kas / Pembayaran Keluar (Uang Tunai / Transfer)</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem; width: 100%;">
                    <thead class="table-custom-header">
                        <tr>
                            <th class="ps-4 text-center" width="5%">No</th>
                            <th width="15%">Waktu Input</th>
                            <th class="text-end" width="15%">Nominal Uang Keluar</th>
                            <th class="text-center" width="15%">Metode Bayar</th>
                            <th width="15%">Eksekutor (Admin)</th>
                            <th class="pe-4">Catatan & Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $noKas = 1; @endphp
                        @forelse($utang->pembayarans->sortBy('created_at') as $bayar)
                        <tr>
                            <td data-label="No" class="ps-4 text-center fw-bold text-slate-muted">{{ $noKas++ }}</td>
                            <td data-label="Waktu Input">
                                <span class="fw-bold text-slate-dark d-block mb-1">{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y') }}</span>
                                <small class="text-slate-muted"><i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('H:i') }} WIB</small>
                            </td>
                            <td data-label="Nominal Uang Keluar" class="text-end font-monospace-custom fs-6 text-success">
                                Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}
                            </td>
                            <td data-label="Metode Bayar" class="text-center">
                                <span class="badge badge-secondary-soft rounded-pill px-3 py-1.5"><i class="fas fa-wallet me-1"></i> {{ $bayar->metode_pembayaran ?? 'Tidak dicatat' }}</span>
                            </td>
                            <td data-label="Eksekutor (Admin)">
                                <span class="fw-semibold text-slate-dark d-block"><i class="fas fa-user-circle text-emerald-custom me-1"></i> {{ $bayar->pembayar->name ?? 'Sistem' }}</span>
                            </td>
                            <td data-label="Catatan & Bukti" class="pe-4">
                                <div class="text-slate-muted fst-italic mb-2" style="font-size: 0.8rem;">"{{ $bayar->keterangan ?? 'Tidak ada catatan' }}"</div>
                                @if($bayar->bukti_bayar)
                                    <a href="{{ asset('storage/' . $bayar->bukti_bayar) }}" target="_blank" class="btn btn-sm btn-light border shadow-sm rounded-pill text-slate-dark" style="font-size: 0.75rem;">
                                        <i class="fas fa-image text-info me-1"></i> Lihat Struk
                                    </a>
                                @else
                                    <span class="badge badge-warning-soft px-2 py-1 rounded-pill" style="font-size: 0.65rem;">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Tanpa Bukti Lampiran
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-slate-muted bg-white">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-comment-dollar fa-2x mb-2 opacity-25"></i>
                                    <span>Belum ada riwayat pengeluaran uang kas / transfer untuk utang ini.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="text-end fw-bold text-slate-dark pt-3 pb-3">TOTAL KAS KELUAR:</td>
                            <td class="text-end text-success fw-bold font-monospace-custom fs-6 pt-3 pb-3">
                                Rp {{ number_format($utang->total_dibayar, 0, ',', '.') }}
                            </td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- TABEL ADJUSMENT: Riwayat Pemotongan Khusus Debit Note (DN) --}}
    <div class="card card-custom bg-white overflow-hidden" style="border-top: 3px solid #f59e0b;">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h6 class="m-0 fw-bold text-slate-dark"><i class="fas fa-tags text-warning me-2"></i>Riwayat Penyesuaian / Potongan Debit Note (DN)</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <style>
                    @media (max-width: 991.98px) {
                        #tabelDN, #tabelDN tbody, #tabelDN tr, #tabelDN td { display: block; width: 100% !important; min-width: auto !important; }
                        #tabelDN thead { display: none; }
                        #tabelDN tr { margin-bottom: 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
                        #tabelDN td { border: none !important; padding: 0.5rem 0 !important; text-align: right !important; display: flex; justify-content: space-between; align-items: center; }
                        #tabelDN td[data-label]::before { content: attr(data-label); display: block; font-size: 0.75rem; font-weight: bold; color: #64748b; text-transform: uppercase; margin-right: 1rem; text-align: left; }
                        #tabelDN td:first-child:not([colspan]) { border-bottom: 1px dashed #e2e8f0 !important; padding-bottom: 0.75rem !important; margin-bottom: 0.5rem; }
                        #tabelDN td:last-child:not([colspan]) { border-top: 1px dashed #e2e8f0 !important; margin-top: 0.5rem; padding-top: 1rem !important; justify-content: center; }
                        #tabelDN td:last-child:not([colspan])::before { display: none; }
                        #tabelDN tfoot { display: block; width: 100%; margin-top: 1rem; }
                        #tabelDN tfoot tr { background: #e2e8f0; border-radius: 0.5rem; padding: 1rem; }
                    }
                </style>
                <table id="tabelDN" class="table table-hover align-middle mb-0" style="font-size: 0.85rem; width: 100%;">
                    <thead class="table-custom-header">
                        <tr>
                            <th class="ps-4 text-center" width="5%">No</th>
                            <th width="15%">Waktu Eksekusi</th>
                            <th class="text-center" width="20%">Label Pemotongan</th>
                            <th class="text-end" width="15%">Nominal Potongan</th>
                            <th class="pe-4">Alasan Pemotongan Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $noDN = 1; @endphp
                        {{-- PERBAIKAN: Langsung membaca dari tabel khusus CreditNote ($debitNotes) --}}
                        @forelse($debitNotes as $dn)
                        <tr>
                            <td data-label="No" class="ps-4 text-center fw-bold text-slate-muted">{{ $noDN++ }}</td>
                            <td data-label="Waktu Eksekusi">
                                <span class="fw-bold text-slate-dark d-block mb-1">{{ \Carbon\Carbon::parse($dn->created_at)->format('d M Y') }}</span>
                                <small class="text-slate-muted"><i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($dn->created_at)->format('H:i') }} WIB</small>
                            </td>
                            <td data-label="Label Pemotongan" class="text-center">
                                <span class="badge badge-warning-soft text-dark fw-bold rounded-pill px-3 py-1.5" style="color: #92400e !important; border-color: #fcd34d;">
                                    <i class="fas fa-cut me-1"></i> {{ $dn->nomor_cn }}
                                </span>
                            </td>
                            <td data-label="Nominal Potongan" class="text-end fw-bold text-warning font-monospace-custom" style="font-size: 0.95rem;">
                                - Rp {{ number_format($dn->nominal, 0, ',', '.') }}
                            </td>
                            <td data-label="Alasan Pemotongan Harga" class="text-slate-dark fw-medium pe-4">
                                {{ $dn->keterangan ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-slate-muted bg-white">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-tags fa-2x mb-2 text-warning opacity-25"></i>
                                    <span>Belum ada klaim potongan Debit Note (DN) atau retur supplier pada invoice ini.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-bold text-slate-dark pt-3 pb-3">TOTAL POTONGAN DN:</td>
                            <td class="text-end text-warning fw-bold font-monospace-custom fs-6 pt-3 pb-3">
                                - Rp {{ number_format($utang->potongan_dn, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT UNTUK MEMUNCULKAN INPUT METODE LAINNYA --}}
<script>
    function toggleMetodeLainnya() {
        var dropdown = document.getElementById('metode_pembayaran');
        var divLainnya = document.getElementById('div_metode_lainnya');
        var inputLainnya = document.getElementById('metode_pembayaran_lainnya');

        if (dropdown.value === 'Lainnya') {
            divLainnya.style.display = 'block';
            inputLainnya.setAttribute('required', 'required');
            inputLainnya.focus();
        } else {
            divLainnya.style.display = 'none';
            inputLainnya.removeAttribute('required');
            inputLainnya.value = ''; // Kosongkan isi jika user mengganti opsi
        }
    }
</script>
@endsection
