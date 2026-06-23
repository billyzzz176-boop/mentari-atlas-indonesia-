@extends('layouts.app')

@section('content')
<style>
    /* Global Overrides untuk Tema Premium Mentari Atlas */
    body { background-color: #f8fafc !important; }
    
    /* Aksen Biru Khusus Piutang */
    .text-blue-custom { color: #0284c7 !important; }
    .badge-blue-soft { background-color: #e0f2fe !important; color: #0284c7 !important; border: 1px solid #bae6fd; }
    .btn-blue-custom { background-color: #0284c7 !important; border-color: #0284c7 !important; color: #ffffff !important; font-weight: 500; transition: all 0.2s; }
    .btn-blue-custom:hover { background-color: #0369a1 !important; color: #ffffff !important; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2); }
    
    .text-slate-dark { color: #0f172a !important; }
    .text-slate-muted { color: #64748b !important; }
    
    /* Card & Table Styling */
    .card-custom { border: 1px solid #e2e8f0; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .table-mentari thead th, .table-mentari thead th:last-child { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important; color: #ffffff !important; border-bottom: none !important; font-weight: 600 !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; white-space: nowrap; }
    
    /* Soft Badges Standar */
    .badge-success-soft { background-color: #d1fae5 !important; color: #065f46 !important; border: 1px solid #a7f3d0; }
    .badge-danger-soft { background-color: #fee2e2 !important; color: #991b1b !important; border: 1px solid #fecaca; }
    .badge-warning-soft { background-color: #fef3c7 !important; color: #92400e !important; border: 1px solid #fde68a; }
    .badge-secondary-soft { background-color: #f1f5f9 !important; color: #475569 !important; border: 1px solid #cbd5e1; }
    
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
            <h1 class="h3 mb-0 text-slate-dark fw-bold"><i class="fas fa-hand-holding-usd text-blue-custom me-2"></i>Buku Jurnal Piutang</h1>
            <p class="text-slate-muted small mb-0 mt-1">Pantau arus uang masuk, riwayat cicilan, dan sisa tagihan dari customer secara akurat.</p>
        </div>
        <div>
            <span class="badge badge-blue-soft px-3 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 0.85rem;">
                <i class="fas fa-arrow-down me-1"></i> Arus Kas Masuk
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
        <div class="alert badge-danger-soft alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3 px-4 py-3" role="alert">
            <i class="fas fa-exclamation-triangle text-danger me-2"></i><strong>Gagal Proses!</strong> {{ $errors->first() }}
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- KONTEN UTAMA JURNAL PIUTANG --}}
    <div class="table-wrapper-mentari d-none d-lg-block">
        <div class="table-responsive">
            <table class="table table-mentari table-mentari-compact align-middle mb-0" style="font-size: 0.8rem; width: 100%;">
                <thead>
                    <tr>
                        <th class="ps-3 text-center" style="width: 1%;">No</th>
                        <th style="width: 1%; white-space: nowrap;">Info Invoice</th>
                        <th>Pelanggan</th>
                        <th class="text-end" style="width: 1%; white-space: nowrap;">Tagihan Awal</th>
                        <th class="text-end" style="width: 1%; white-space: nowrap;" title="Potongan CN/Retur">Potongan CN</th>
                        <th class="text-end text-blue-custom" style="width: 1%; white-space: nowrap;" title="Tagihan Setelah Dipotong CN">Piutang Bersih</th>
                        <th class="text-end" style="width: 1%; white-space: nowrap;">Uang Terbayar</th>
                        <th class="text-end" style="width: 1%; white-space: nowrap;">Sisa Piutang</th>
                        <th class="text-center" style="width: 1%; white-space: nowrap;">Status</th>
                        <th class="text-center pe-3" style="width: 1%; white-space: nowrap;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        // Inisialisasi variabel untuk menghitung Grand Total di footer
                        $grandTotalTagihanAwal = 0;
                        $grandTotalCN = 0;
                        $grandTotalPiutangBersih = 0;
                        $grandTotalTerbayar = 0;
                        $grandTotalSisa = 0;
                    @endphp

                    @forelse($piutangs as $p)
                        @php
                            $listKataKunciCN = ['Credit Note', 'Credit Note / Retur Customer', 'Retur Customer / Credit Note', 'Retur Customer'];
                            
                            $totalCN = $p->pembayarans ? $p->pembayarans->whereIn('metode_pembayaran', $listKataKunciCN)->sum('jumlah_bayar') : 0;
                            
                            $piutangBersih = $p->total_tagihan; // Nilai di DB total_tagihan adalah Piutang Bersih karena sudah dipotong CN saat retur
                            $tagihanAwal = $piutangBersih + $totalCN; // Kita hitung mundur tagihan awalnya
                            
                            $sisaPiutang = $p->total_tagihan - $p->total_dibayar;
                            $status = strtolower($p->status_bayar);

                            // Tambahkan ke Grand Total
                            $grandTotalTagihanAwal += $tagihanAwal;
                            $grandTotalCN += $totalCN;
                            $grandTotalPiutangBersih += $piutangBersih;
                            $grandTotalTerbayar += $p->total_dibayar;
                            $grandTotalSisa += max(0, $sisaPiutang);
                        @endphp
                        <tr>
                            <td class="ps-3 text-center fw-bold text-slate-muted">{{ $loop->iteration }}</td>
                            <td style="white-space: nowrap;">
                                <span class="fw-bold text-blue-custom d-block">{{ $p->no_invoice }}</span>
                                <span class="text-slate-muted" style="font-size: 0.7rem;"><i class="fas fa-file-alt me-1"></i>{{ $p->penjualan->no_so ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-slate-dark d-block text-wrap" style="max-width: 150px; line-height: 1.2;">{{ $p->penjualan->customer->nama_customer ?? '-' }}</span>
                            </td>
                            <td class="text-end font-monospace-custom text-slate-dark" style="white-space: nowrap;">
                                Rp {{ number_format($tagihanAwal, 0, ',', '.') }}
                            </td>
                            <td class="text-end font-monospace-custom {{ $totalCN > 0 ? 'text-warning' : 'text-slate-muted opacity-50' }}" style="white-space: nowrap;">
                                Rp {{ number_format($totalCN, 0, ',', '.') }}
                            </td>
                            {{-- KOLOM BARU: TOTAL PIUTANG BERSIH --}}
                            <td class="text-end fw-bold font-monospace-custom text-slate-dark" style="white-space: nowrap; background-color: #f1f5f9;">
                                Rp {{ number_format($piutangBersih, 0, ',', '.') }}
                            </td>
                            <td class="text-end font-monospace-custom text-success" style="white-space: nowrap;">
                                Rp {{ number_format($p->total_dibayar, 0, ',', '.') }}
                            </td>
                            <td class="text-end font-monospace-custom {{ $sisaPiutang > 0 ? 'text-danger fw-bold' : 'text-slate-muted' }}" style="white-space: nowrap;">
                                @if($status === 'lunas' || $sisaPiutang <= 0)
                                    <i class="fas fa-check text-success"></i> Rp 0
                                @else
                                    Rp {{ number_format($sisaPiutang, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="text-center" style="white-space: nowrap;">
                                @if($status === 'lunas' || $sisaPiutang <= 0)
                                    <span class="badge badge-success-soft px-2 py-1 rounded-pill shadow-sm">Lunas</span>
                                @elseif($status === 'cicil')
                                    <span class="badge badge-warning-soft px-2 py-1 rounded-pill shadow-sm">Cicil</span>
                                @else
                                    <span class="badge badge-danger-soft px-2 py-1 rounded-pill shadow-sm">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="text-center pe-3 align-middle" style="white-space: nowrap;">
                                <div class="d-flex gap-1 justify-content-center align-items-center flex-nowrap">
                                    @if($status !== 'lunas' && $sisaPiutang > 0)
                                        <button type="button" class="btn-action-circle btn-potongan-cn shadow-sm" style="background-color: #fde68a; color: #92400e; border: 1px solid #fcd34d;" 
                                                data-id="{{ $p->penjualan_id }}" data-customer="{{ $p->penjualan->customer->nama_customer ?? '-' }}"
                                                data-bs-toggle="tooltip" title="Potong Credit Note (CN)">
                                            <i class="fas fa-cut" style="font-size: 0.75rem;"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('keuangan.piutang.show', $p->id) }}" class="btn-action-circle btn-blue-custom shadow-sm" data-bs-toggle="tooltip" title="Lihat Detail">
                                        <i class="fas fa-eye" style="font-size: 0.8rem;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-slate-muted bg-white">
                                <i class="fas fa-folder-open fa-3x mb-3 opacity-25 text-blue-custom"></i>
                                <span class="d-block fw-bold">Belum Ada Data Piutang</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                {{-- BARIS GRAND TOTAL --}}
                @if(count($piutangs) > 0)
                <tfoot style="background-color: #e2e8f0; border-top: 2px solid #cbd5e1;">
                    <tr>
                        <td colspan="3" class="text-end fw-bold text-slate-dark py-3">GRAND TOTAL KESELURUHAN:</td>
                        <td class="text-end fw-bold text-slate-dark font-monospace-custom py-3" style="white-space: nowrap;">
                            Rp {{ number_format($grandTotalTagihanAwal, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold text-warning font-monospace-custom py-3" style="white-space: nowrap;">
                            Rp {{ number_format($grandTotalCN, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold text-slate-dark font-monospace-custom py-3" style="white-space: nowrap; background-color: #cbd5e1;">
                            Rp {{ number_format($grandTotalPiutangBersih, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold text-success font-monospace-custom py-3" style="white-space: nowrap;">
                            Rp {{ number_format($grandTotalTerbayar, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold text-danger font-monospace-custom py-3" style="white-space: nowrap;">
                            Rp {{ number_format($grandTotalSisa, 0, ',', '.') }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- MOBILE CARDS --}}
    <div class="d-lg-none p-2">
        @forelse($piutangs as $p)
            @php
                $listKataKunciCN = ['Credit Note', 'Credit Note / Retur Customer', 'Retur Customer / Credit Note', 'Retur Customer'];
                $totalCN = $p->pembayarans ? $p->pembayarans->whereIn('metode_pembayaran', $listKataKunciCN)->sum('jumlah_bayar') : 0;
                $piutangBersih = $p->total_tagihan; 
                $tagihanAwal = $piutangBersih + $totalCN; 
                $sisaPiutang = $p->total_tagihan - $p->total_dibayar;
                $status = strtolower($p->status_bayar);
            @endphp
            <div class="card card-custom mb-3 border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold text-blue-custom d-block">{{ $p->no_invoice }}</span>
                        <span class="text-slate-muted" style="font-size: 0.75rem;"><i class="fas fa-file-alt me-1"></i>{{ $p->penjualan->no_so ?? '-' }}</span>
                    </div>
                    <div>
                        @if($status === 'lunas' || $sisaPiutang <= 0)
                            <span class="badge badge-success-soft px-2 py-1 rounded-pill shadow-sm">Lunas</span>
                        @elseif($status === 'cicil')
                            <span class="badge badge-warning-soft px-2 py-1 rounded-pill shadow-sm">Cicil</span>
                        @else
                            <span class="badge badge-danger-soft px-2 py-1 rounded-pill shadow-sm">Belum Bayar</span>
                        @endif
                    </div>
                </div>
                <div class="card-body py-2">
                    <div class="mb-2">
                        <span class="fw-bold text-slate-dark d-block" style="font-size: 0.9rem;">{{ $p->penjualan->customer->nama_customer ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem;">
                        <span class="text-slate-muted">Tagihan Awal</span>
                        <span class="font-monospace-custom text-slate-dark">Rp {{ number_format($tagihanAwal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem;">
                        <span class="text-slate-muted">Potongan CN</span>
                        <span class="font-monospace-custom {{ $totalCN > 0 ? 'text-warning' : 'text-slate-muted opacity-50' }}">Rp {{ number_format($totalCN, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem; background-color: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 4px;">
                        <span class="text-slate-dark fw-bold">Piutang Bersih</span>
                        <span class="font-monospace-custom text-slate-dark fw-bold">Rp {{ number_format($piutangBersih, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem;">
                        <span class="text-slate-muted">Uang Terbayar</span>
                        <span class="font-monospace-custom text-success">Rp {{ number_format($p->total_dibayar, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 mt-2 pt-2" style="font-size: 0.85rem; border-top: 1px dashed #e2e8f0;">
                        <span class="text-slate-dark fw-bold">Sisa Piutang</span>
                        <span class="font-monospace-custom {{ $sisaPiutang > 0 ? 'text-danger fw-bold' : 'text-slate-muted' }}">
                            @if($status === 'lunas' || $sisaPiutang <= 0)
                                <i class="fas fa-check text-success"></i> Rp 0
                            @else
                                Rp {{ number_format($sisaPiutang, 0, ',', '.') }}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 pb-3 pt-0">
                    <div class="d-flex gap-2">
                        @if($status !== 'lunas' && $sisaPiutang > 0)
                            <button type="button" class="btn btn-sm btn-potongan-cn flex-fill shadow-sm fw-bold" style="background-color: #fde68a; color: #92400e; border: 1px solid #fcd34d;" 
                                    data-id="{{ $p->penjualan_id }}" data-customer="{{ $p->penjualan->customer->nama_customer ?? '-' }}">
                                <i class="fas fa-cut me-1"></i> Potong CN
                            </button>
                        @endif
                        <a href="{{ route('keuangan.piutang.show', $p->id) }}" class="btn btn-sm btn-blue-custom flex-fill shadow-sm">
                            <i class="fas fa-eye me-1"></i> Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-white rounded-3 shadow-sm border border-slate-100">
                <i class="fas fa-folder-open fa-3x mb-3 opacity-25 text-blue-custom"></i>
                <span class="d-block fw-bold text-slate-muted">Belum Ada Data Piutang</span>
            </div>
        @endforelse
    </div>
</div>

{{-- MODAL CREDIT NOTE --}}
<div class="modal fade" id="modalTambahCreditNote" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('keuangan.creditNote.store') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            @csrf
            <input type="hidden" name="tipe" value="penjualan">
            <input type="hidden" name="referensi_id" id="mdl_referensi_id">
            <div class="modal-header border-0 py-3" style="background-color: #f59e0b;">
                <h6 class="modal-title fw-bold text-white"><i class="fas fa-receipt me-2"></i>Buat Credit Note</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-3 text-center" style="background-color: #fffbeb; border: 1px dashed #fcd34d; border-radius: 0.5rem;">
                        <div class="small fw-bold text-slate-muted mb-1">Target Pemotongan Customer:</div>
                        <div class="fw-bold text-slate-dark fs-6" id="mdl_nama_customer">-</div>
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
                <button type="submit" class="btn fw-bold shadow-sm px-4 w-100 text-white" style="background-color: #f59e0b;" onclick="return confirm('Eksekusi potongan piutang ini?')">
                    Simpan Potongan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl); });

    const tombolPotonganCN = document.querySelectorAll('.btn-potongan-cn');
    const modalElement = document.getElementById('modalTambahCreditNote');
    if (modalElement) {
        const modalFormCN = new bootstrap.Modal(modalElement);
        tombolPotonganCN.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault(); 
                document.getElementById('mdl_referensi_id').value = this.getAttribute('data-id');
                document.getElementById('mdl_nama_customer').innerText = this.getAttribute('data-customer');
                modalFormCN.show();
            });
        });
    }
});
</script>
@endsection