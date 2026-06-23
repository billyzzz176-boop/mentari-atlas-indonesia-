@extends('layouts.app')

@section('content')
<style>
    /* Global Overrides untuk Tema Premium Mentari Atlas */
    body { background-color: #f8fafc !important; }
    
    /* Aksen Merah Khusus Utang */
    .text-red-custom { color: #e11d48 !important; }
    .badge-red-soft { background-color: #ffe4e6 !important; color: #e11d48 !important; border: 1px solid #fecdd3; }
    .btn-red-custom { background-color: #e11d48 !important; border-color: #e11d48 !important; color: #ffffff !important; font-weight: 500; transition: all 0.2s; }
    .btn-red-custom:hover { background-color: #be123c !important; color: #ffffff !important; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(225, 29, 72, 0.2); }
    
    .text-slate-dark { color: #0f172a !important; }
    .text-slate-muted { color: #64748b !important; }
    
    /* Card & Table Styling */
    .card-custom { border: 1px solid #e2e8f0; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .table-mentari thead th, .table-mentari thead th:last-child { background: linear-gradient(135deg, #e11d48 0%, #be123c 100%) !important; color: #ffffff !important; border-bottom: none !important; font-weight: 600 !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; white-space: nowrap; }
    
    /* Soft Badges Standar */
    .badge-success-soft { background-color: #d1fae5 !important; color: #065f46 !important; border: 1px solid #a7f3d0; }
    .badge-warning-soft { background-color: #fef3c7 !important; color: #92400e !important; border: 1px solid #fde68a; }
    
    /* Nominal Fonts */
    .font-monospace-custom { font-family: 'Courier New', Courier, monospace; font-weight: 700; letter-spacing: -0.5px; }

    /* Diet Ketat Tabel */
    .table-mentari-compact th, .table-mentari-compact td { padding: 0.75rem 0.5rem !important; }
    
    /* Tombol Aksi Bulat */
    .btn-action-circle {
        width: 32px; height: 32px; padding: 0;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; transition: all 0.2s ease; flex-shrink: 0;
    }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 80vh;">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold"><i class="fas fa-file-invoice-dollar text-red-custom me-2"></i>Buku Jurnal Utang</h1>
            <p class="text-slate-muted small mb-0 mt-1">Pantau arus uang keluar, riwayat cicilan, dan sisa kewajiban kepada supplier secara akurat.</p>
        </div>
        <div>
            <span class="badge badge-red-soft px-3 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 0.85rem;">
                <i class="fas fa-arrow-up me-1"></i> Arus Kas Keluar
            </span>
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert badge-success-soft alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3 px-4 py-3" role="alert">
            <i class="fas fa-check-circle text-success me-2"></i><strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert badge-red-soft alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3 px-4 py-3" role="alert">
            <i class="fas fa-exclamation-triangle text-danger me-2"></i><strong>Gagal Proses!</strong> {{ $errors->first() }}
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- KONTEN UTAMA JURNAL UTANG --}}
    <div class="table-wrapper-mentari">
        <style>
            @media (max-width: 991.98px) {
                #tabelUtang, #tabelUtang tbody, #tabelUtang tr, #tabelUtang td { display: block; width: 100% !important; min-width: auto !important; }
                #tabelUtang thead { display: none; }
                #tabelUtang tr:not(.tfoot-grand-total) { margin-bottom: 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
                #tabelUtang td { border: none !important; padding: 0.5rem 0 !important; text-align: right !important; display: flex; justify-content: space-between; align-items: center; }
                #tabelUtang td[data-label]::before { content: attr(data-label); display: block; font-size: 0.75rem; font-weight: bold; color: #64748b; text-transform: uppercase; margin-right: 1rem; text-align: left; }
                #tabelUtang td:first-child:not([colspan]) { border-bottom: 1px dashed #e2e8f0 !important; padding-bottom: 0.75rem !important; margin-bottom: 0.5rem; }
                #tabelUtang td:last-child:not([colspan]) { border-top: 1px dashed #e2e8f0 !important; margin-top: 0.5rem; padding-top: 1rem !important; justify-content: center; }
                #tabelUtang td:last-child:not([colspan])::before { display: none; }
                #tabelUtang tfoot { display: block; width: 100%; margin-top: 1rem; }
                #tabelUtang tfoot .tfoot-grand-total { background: #e2e8f0; border-radius: 0.5rem; padding: 1rem; }
            }
        </style>
        <div class="table-responsive d-none d-lg-block">
            <table id="tabelUtang" class="table table-mentari table-mentari-compact align-middle mb-0" style="font-size: 0.8rem; width: 100%;">
                <thead>
                    <tr>
                        <th class="ps-3 text-center" style="width: 1%;">No</th>
                        <th style="width: 1%; white-space: nowrap;">Info Jurnal</th>
                        <th>Supplier</th>
                        <th class="text-end" style="width: 1%; white-space: nowrap;">Utang Awal</th>
                        <th class="text-end" style="width: 1%; white-space: nowrap;" title="Potongan DN/Retur">Potongan DN</th>
                        <th class="text-end text-red-custom" style="width: 1%; white-space: nowrap;" title="Utang Setelah Dipotong DN">Utang Bersih</th>
                        <th class="text-end" style="width: 1%; white-space: nowrap;">Utang Terbayar</th>
                        <th class="text-end" style="width: 1%; white-space: nowrap;">Sisa Utang</th>
                        <th class="text-center" style="width: 1%; white-space: nowrap;">Status</th>
                        <th class="text-center pe-3" style="width: 1%; white-space: nowrap;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        // Inisialisasi variabel untuk menghitung Grand Total di footer
                        $grandTotalUtangAwal = 0;
                        $grandTotalDN = 0;
                        $grandTotalUtangBersih = 0;
                        $grandTotalTerbayar = 0;
                        $grandTotalSisa = 0;
                    @endphp

                    @forelse($utangs as $u)
                        @php
                            // PERBAIKAN RUMUS AKUNTANSI BARU
                            $utangAwal = $u->total_utang; // Sesuai dengan total PO awal
                            $totalDN = $u->potongan_dn ?? 0; // Mengambil langsung dari kolom baru
                            $utangBersih = $utangAwal - $totalDN; // Harga setelah retur/diskon
                            $sisaUtang = $utangBersih - $u->total_dibayar; // Sisa akhir mutlak
                            
                            $status = strtolower($u->status_bayar);

                            // Tambahkan ke Grand Total
                            $grandTotalUtangAwal += $utangAwal;
                            $grandTotalDN += $totalDN;
                            $grandTotalUtangBersih += $utangBersih;
                            $grandTotalTerbayar += $u->total_dibayar;
                            $grandTotalSisa += max(0, $sisaUtang);
                        @endphp
                        <tr>
                            <td data-label="No" class="ps-3 text-center fw-bold text-slate-muted">{{ $loop->iteration }}</td>
                            <td data-label="Info Jurnal" style="white-space: nowrap;">
                                <span class="fw-bold text-red-custom d-block">{{ $u->no_utang_jurnal ?? '-' }}</span>
                                <span class="text-slate-muted" style="font-size: 0.7rem;"><i class="fas fa-file-alt me-1"></i>{{ $u->pembelian->no_pembelian ?? '-' }}</span>
                            </td>
                            <td data-label="Supplier">
                                <span class="fw-bold text-slate-dark d-block text-wrap" style="max-width: 150px; line-height: 1.2;">{{ $u->pembelian->nama_supplier ?? '-' }}</span>
                            </td>
                            <td data-label="Utang Awal" class="text-end font-monospace-custom text-slate-dark" style="white-space: nowrap;">
                                Rp {{ number_format($utangAwal, 0, ',', '.') }}
                            </td>
                            <td data-label="Potongan DN" class="text-end font-monospace-custom {{ $totalDN > 0 ? 'text-warning' : 'text-slate-muted opacity-50' }}" style="white-space: nowrap;">
                                Rp {{ number_format($totalDN, 0, ',', '.') }}
                            </td>
                            <td data-label="Utang Bersih" class="text-end fw-bold font-monospace-custom text-slate-dark" style="white-space: nowrap; background-color: #f1f5f9;">
                                Rp {{ number_format($utangBersih, 0, ',', '.') }}
                            </td>
                            <td data-label="Utang Terbayar" class="text-end font-monospace-custom text-success" style="white-space: nowrap;">
                                Rp {{ number_format($u->total_dibayar, 0, ',', '.') }}
                            </td>
                            <td data-label="Sisa Utang" class="text-end font-monospace-custom {{ $sisaUtang > 0 ? 'text-danger fw-bold' : 'text-slate-muted' }}" style="white-space: nowrap;">
                                @if($status === 'lunas' || $sisaUtang <= 0)
                                    <i class="fas fa-check text-success"></i> Rp 0
                                @else
                                    Rp {{ number_format($sisaUtang, 0, ',', '.') }}
                                @endif
                            </td>
                            <td data-label="Status" class="text-center" style="white-space: nowrap;">
                                @if($status === 'lunas' || $sisaUtang <= 0)
                                    <span class="badge badge-success-soft px-2 py-1 rounded-pill shadow-sm">Lunas</span>
                                @elseif($status === 'cicil')
                                    <span class="badge badge-warning-soft px-2 py-1 rounded-pill shadow-sm">Cicil</span>
                                @else
                                    <span class="badge badge-red-soft px-2 py-1 rounded-pill shadow-sm" style="border-color: #fecdd3;">Belum Bayar</span>
                                @endif
                            </td>
                            <td data-label="Aksi" class="text-center pe-3 align-middle" style="white-space: nowrap;">
                                <div class="d-flex gap-1 justify-content-center align-items-center flex-nowrap">
                                    @if($status !== 'lunas' && $sisaUtang > 0)
                                        <button type="button" class="btn-action-circle btn-potongan-dn shadow-sm" style="background-color: #fde68a; color: #92400e; border: 1px solid #fcd34d;" 
                                                data-id="{{ $u->pembelian_id }}" data-supplier="{{ $u->pembelian->nama_supplier ?? '-' }}"
                                                data-bs-toggle="tooltip" title="Potong Debit Note (Manual)">
                                            <i class="fas fa-cut" style="font-size: 0.75rem;"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('keuangan.utang.show', $u->id) }}" class="btn-action-circle btn-red-custom shadow-sm" data-bs-toggle="tooltip" title="Lihat Detail">
                                        <i class="fas fa-eye" style="font-size: 0.8rem;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-slate-muted bg-white">
                                <i class="fas fa-folder-open fa-3x mb-3 opacity-25 text-red-custom"></i>
                                <span class="d-block fw-bold">Belum Ada Data Utang</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                {{-- BARIS GRAND TOTAL --}}
                @if(count($utangs) > 0)
                <tfoot style="background-color: #e2e8f0; border-top: 2px solid #cbd5e1;">
                    <tr class="tfoot-grand-total">
                        {{-- GRAND TOTAL MOBILE LABEL --}}
                        <td colspan="10" class="text-center fw-bold text-slate-dark py-2 d-block d-lg-none" style="background-color: #e2e8f0; border-radius: 0.5rem 0.5rem 0 0; margin-bottom: 0.5rem;">GRAND TOTAL KESELURUHAN</td>
                        <td colspan="3" class="text-end fw-bold text-slate-dark py-3 d-none d-lg-table-cell">GRAND TOTAL KESELURUHAN:</td>
                        <td class="text-end fw-bold text-slate-dark font-monospace-custom py-3" data-label="Grand Utang Awal" style="white-space: nowrap;">
                            Rp {{ number_format($grandTotalUtangAwal, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold text-warning font-monospace-custom py-3" data-label="Grand Potongan DN" style="white-space: nowrap;">
                            Rp {{ number_format($grandTotalDN, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold text-slate-dark font-monospace-custom py-3" data-label="Grand Utang Bersih" style="white-space: nowrap; background-color: #cbd5e1;">
                            Rp {{ number_format($grandTotalUtangBersih, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold text-success font-monospace-custom py-3" data-label="Grand Terbayar" style="white-space: nowrap;">
                            Rp {{ number_format($grandTotalTerbayar, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold text-danger font-monospace-custom py-3" data-label="Grand Sisa" style="white-space: nowrap;">
                            Rp {{ number_format($grandTotalSisa, 0, ',', '.') }}
                        </td>
                        <td colspan="2" class="d-none d-lg-table-cell"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        
        {{-- MOBILE CARDS --}}
        <div class="d-lg-none p-2">
            @forelse($utangs as $u)
                @php
                $listKataKunciDN = ['Debit Note', 'Debit Note / Retur Pembelian', 'Retur Pembelian / Debit Note', 'Retur Pembelian'];
                $totalDN = $u->pembayarans ? $u->pembayarans->whereIn('metode_pembayaran', $listKataKunciDN)->sum('jumlah_bayar') : 0;
                $utangBersih = $u->total_tagihan ?? $u->total_utang; 
                $utangAwal = $utangBersih + $totalDN; 
                $sisaUtang = ($u->total_tagihan ?? $u->total_utang) - $u->total_dibayar;
                $status = strtolower($u->status_bayar);
                @endphp
                <div class="card card-custom mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-red-custom d-block">{{ $u->no_utang_jurnal ?? '-' }}</span>
                            <span class="text-slate-muted" style="font-size: 0.75rem;"><i class="fas fa-file-alt me-1"></i>{{ $u->pembelian->no_pembelian ?? '-' }}</span>
                        </div>
                        <div>
                            @if($status === 'lunas' || $sisaUtang <= 0)
                                <span class="badge badge-success-soft px-2 py-1 rounded-pill shadow-sm">Lunas</span>
                            @elseif($status === 'cicil')
                                <span class="badge badge-warning-soft px-2 py-1 rounded-pill shadow-sm">Cicil</span>
                            @else
                                <span class="badge badge-red-soft px-2 py-1 rounded-pill shadow-sm" style="border-color: #fecdd3;">Belum Bayar</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body py-2">
                        <div class="mb-2">
                            <span class="fw-bold text-slate-dark d-block" style="font-size: 0.9rem;">{{ $u->pembelian->nama_supplier ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem;">
                            <span class="text-slate-muted">Utang Awal</span>
                            <span class="font-monospace-custom text-slate-dark">Rp {{ number_format($utangAwal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem;">
                            <span class="text-slate-muted">Potongan DN</span>
                            <span class="font-monospace-custom {{ $totalDN > 0 ? 'text-warning' : 'text-slate-muted opacity-50' }}">Rp {{ number_format($totalDN, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem; background-color: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 4px;">
                            <span class="text-slate-dark fw-bold">Utang Bersih</span>
                            <span class="font-monospace-custom text-slate-dark fw-bold">Rp {{ number_format($utangBersih, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem;">
                            <span class="text-slate-muted">Utang Terbayar</span>
                            <span class="font-monospace-custom text-success">Rp {{ number_format($u->total_dibayar, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 mt-2 pt-2" style="font-size: 0.85rem; border-top: 1px dashed #e2e8f0;">
                            <span class="text-slate-dark fw-bold">Sisa Utang</span>
                            <span class="font-monospace-custom {{ $sisaUtang > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                @if($status === 'lunas' || $sisaUtang <= 0)
                                    <i class="fas fa-check text-success"></i> Rp 0
                                @else
                                    Rp {{ number_format($sisaUtang, 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 pb-3 pt-0">
                        <div class="d-flex gap-2">
                            @if($status !== 'lunas' && $sisaUtang > 0)
                                <button type="button" class="btn btn-sm btn-potongan-dn flex-fill shadow-sm fw-bold" style="background-color: #fde68a; color: #92400e; border: 1px solid #fcd34d;" 
                                        data-id="{{ $u->pembelian_id }}" data-supplier="{{ $u->pembelian->nama_supplier ?? '-' }}">
                                    <i class="fas fa-cut me-1"></i> Potong DN
                                </button>
                            @endif
                            <a href="{{ route('keuangan.utang.show', $u->id) }}" class="btn btn-sm btn-red-custom flex-fill shadow-sm">
                                <i class="fas fa-eye me-1"></i> Detail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-white rounded-3 shadow-sm border border-slate-100">
                    <i class="fas fa-folder-open fa-3x mb-3 opacity-25 text-red-custom"></i>
                    <span class="d-block fw-bold text-slate-muted">Belum Ada Data Utang</span>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- MODAL DEBIT NOTE MANUAL --}}
<div class="modal fade" id="modalTambahDebitNote" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('keuangan.creditNote.store') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            @csrf
            <input type="hidden" name="tipe" value="pembelian">
            <input type="hidden" name="referensi_id" id="mdl_referensi_id">
            <div class="modal-header border-0 py-3" style="background-color: #f59e0b;">
                <h6 class="modal-title fw-bold text-white"><i class="fas fa-receipt me-2"></i>Buat Debit Note Manual</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-3 text-center" style="background-color: #fffbeb; border: 1px dashed #fcd34d; border-radius: 0.5rem;">
                        <div class="small fw-bold text-slate-muted mb-1">Target Pemotongan Supplier:</div>
                        <div class="fw-bold text-slate-dark fs-6" id="mdl_nama_supplier">-</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-slate-dark">Besaran Potongan Harga *</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-secondary-subtle fw-bold">Rp</span>
                        <input type="number" name="nominal" class="form-control border-secondary-subtle" min="1" required>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-bold text-slate-dark">Catatan / Alasan Penyesuaian *</label>
                    <textarea name="keterangan" class="form-control border-secondary-subtle shadow-sm" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer bg-white border-top-0 py-3">
                <button type="submit" class="btn fw-bold shadow-sm px-4 w-100 text-white" style="background-color: #f59e0b;" onclick="return confirm('Eksekusi potongan utang ini?')">
                    Simpan Potongan Manual
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl); });

    const tombolPotonganDN = document.querySelectorAll('.btn-potongan-dn');
    const modalElement = document.getElementById('modalTambahDebitNote');
    if (modalElement) {
        const modalFormDN = new bootstrap.Modal(modalElement);
        tombolPotonganDN.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault(); 
                document.getElementById('mdl_referensi_id').value = this.getAttribute('data-id');
                document.getElementById('mdl_nama_supplier').innerText = this.getAttribute('data-supplier');
                modalFormDN.show();
            });
        });
    }
});
</script>
@endsection
