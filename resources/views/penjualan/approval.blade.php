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
    
    /* Card & Table Styling */
    .card-custom { border: 1px solid #e2e8f0; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .table-custom-header th { background-color: #f1f5f9 !important; color: #334155 !important; font-weight: 600 !important; border-bottom: 2px solid #e2e8f0 !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    
    /* Soft Badges */
    .badge-success-soft { background-color: #d1fae5 !important; color: #065f46 !important; border: 1px solid #a7f3d0; }
    .badge-warning-soft { background-color: #fef3c7 !important; color: #92400e !important; border: 1px solid #fde68a; }
    .badge-danger-soft { background-color: #fee2e2 !important; color: #991b1b !important; border: 1px solid #fecaca; }
    .badge-secondary-soft { background-color: #f1f5f9 !important; color: #475569 !important; border: 1px solid #cbd5e1; }

    /* Custom Progress Bar */
    .progress-wrapper { background-color: #e2e8f0; border-radius: 999px; height: 18px; overflow: hidden; position: relative; }
    .progress-fill { height: 100%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: bold; color: white; transition: width 0.5s ease; }
    .fill-safe { background-color: #10b981; }
    .fill-warn { background-color: #f59e0b; }
    .fill-risk { background-color: #ef4444; }

    /* Custom Input Edit Harga & QTY (Diet Mode) */
    .input-harga-edit { background-color: #f0fdfa !important; border: 1px solid #a7f3d0 !important; color: #047857 !important; font-size: 0.85rem !important; padding: 0.25rem 0.5rem !important; height: auto !important; }
    .input-harga-edit:focus { border-color: #10b981 !important; box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25) !important; }
    
    .input-qty-edit { background-color: #fffbeb !important; border: 1px solid #fde68a !important; color: #b45309 !important; width: 60px !important; margin: 0 auto; font-size: 0.85rem !important; padding: 0.25rem !important; height: auto !important; }
    .input-qty-edit:focus { border-color: #f59e0b !important; box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.25) !important; }

    /* Tombol Pilihan Harga */
    .btn-apply-harga { cursor: pointer; transition: transform 0.1s ease, box-shadow 0.1s ease; font-size: 0.65rem !important; padding: 0.2rem 0.4rem !important; }
    .btn-apply-harga:hover { transform: translateY(-2px); box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
    .badge-bronze { background-color: #cd7f32 !important; color: white !important; }

    /* Modal Minimalist Overrides */
    .modal-compact-body { padding: 1rem !important; }
    .table-compact td, .table-compact th { padding: 0.5rem !important; font-size: 0.8rem !important; }
    
    /* Plafon Warning Box */
    .plafon-warning-box { background-color: #fff1f2; border: 1px dashed #fecaca; color: #be123c; border-radius: 0.5rem; padding: 0.5rem; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem; }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 80vh;">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold">Otorisasi Sales Order</h1>
            <p class="text-slate-muted small mb-0 mt-1">Review rincian, periksa sisa limit, dan berikan persetujuan pesanan.</p>
        </div>
        

    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-custom bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center">
                    <i class="fas fa-clipboard-check fs-4 text-emerald-custom me-3"></i>
                    <h6 class="mb-0 fw-bold text-slate-dark">Antrean Dokumen Approval</h6>
                </div>
                
                <div class="card-body p-0">
                    {{-- DESKTOP VIEW --}}
                    <div class="table-wrapper-mentari">
                        <div class="table-responsive d-none d-lg-block">
                            <table class="table table-mentari table-mentari-compact align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="text-center">
                                    <tr>
                                        <th class="py-3 px-3">No SO</th>
                                        <th class="py-3">Waktu Input</th>
                                        <th class="py-3 text-start">Customer</th>
                                        <th class="py-3">Sales</th>
                                        <th class="py-3 text-end px-4">Nilai Order</th>
                                        <th class="py-3" style="width: 140px;">Kelayakan (SPK)</th>
                                        <th class="py-3">Status</th>
                                        <th class="py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pengajuan as $item)
                                    <tr>
                                        <td class="fw-bold text-emerald-custom text-center px-3">{{ $item->no_so }}</td>
                                        
                                        <td class="text-center text-slate-muted small fw-medium">
                                            @if($item->sales_created_at)
                                                {{ $item->sales_created_at->format('d/m/Y') }}<br>
                                                <span style="font-size: 0.7rem;">{{ $item->sales_created_at->format('H:i') }} WIB</span>
                                            @else
                                                {{ \Carbon\Carbon::parse($item->tanggal_order)->format('d/m/Y') }}
                                            @endif
                                        </td>
                                        
                                        <td class="text-start">
                                            <span class="fw-bold text-slate-dark d-block">{{ $item->customer->nama_customer }}</span>
                                            <span class="text-slate-muted" style="font-size: 0.75rem;"><i class="fas fa-id-card me-1"></i>ID: {{ $item->customer->id_cust }}</span>
                                        </td>
                                        
                                        <td class="text-center">
                                            <span class="badge badge-secondary-soft px-2 py-1 rounded">{{ $item->user->name }}</span>
                                        </td>
                                        
                                        <td class="text-end px-4 fw-bold text-slate-dark" style="font-size: 0.95rem;">
                                            Rp {{ number_format($item->total_semua, 0, ',', '.') }}
                                        </td>
                                        
                                        <td class="text-center px-2">
                                            @php 
                                                $skor = $item->skor_spk ?? 0;
                                                $warna = ($skor >= 70) ? 'fill-safe' : (($skor >= 40) ? 'fill-warn' : 'fill-risk');
                                            @endphp
                                            <div class="progress-wrapper shadow-sm">
                                                <div class="progress-fill {{ $warna }}" style="width: {{ $skor }}%;">
                                                    @if($skor >= 30) {{ number_format($skor, 0) }}% @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            @if($item->status_approval == 'pending')
                                                <span class="badge badge-warning-soft px-3 py-1.5 rounded-pill fw-bold">PENDING</span>
                                            @elseif($item->status_approval == 'disetujui')
                                                <span class="badge badge-success-soft px-3 py-1.5 rounded-pill fw-bold">DISETUJUI</span>
                                            @else
                                                <span class="badge badge-danger-soft px-3 py-1.5 rounded-pill fw-bold">DITOLAK</span>
                                            @endif
                                        </td>
                                        
                                        <td class="text-center px-3">
                                            @if($item->status_approval == 'pending')
                                                <button type="button" class="btn btn-sm btn-emerald-custom px-3 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalApprove{{ $item->id }}">
                                                    <i class="fas fa-search me-1"></i> Rincian
                                                </button>

                                                {{-- MODAL GACORRR (COMPACT & PROFESSIONAL) --}}
                                                <div class="modal fade" id="modalApprove{{ $item->id }}" tabindex="-1" aria-hidden="true" data-sisa-limit="{{ $item->customer->sisa_plafon ?? 0 }}">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered text-start">
                                                        <form action="{{ url('/penjualan/approve/'.$item->id) }}" method="POST" class="w-100">
                                                            @csrf
                                                            <div class="modal-content premium-modal border-0 shadow-lg">
                                                                
                                                                <div class="modal-header bg-white border-bottom py-3">
                                                                    <h5 class="modal-title fw-bolder mb-0 fs-6 text-slate-dark" style="letter-spacing: 0.3px;">
                                                                        <i class="fas fa-file-signature text-emerald-custom me-2"></i> Otorisasi Sales Order: <span class="text-emerald-custom">{{ $item->no_so }}</span>
                                                                    </h5>
                                                                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
                                                                </div>

                                                                <div class="modal-body modal-compact-body bg-slate-50 p-3">
                                                                    
                                                                    @php
                                                                        $tier = strtolower($item->customer->tingkat_customer ?? 'reguler');
                                                                        $plafon = $item->customer->plafon ?? 0;
                                                                        $piutang = $item->customer->piutang_berjalan ?? 0;
                                                                        $sisa_limit = $item->customer->sisa_plafon ?? 0;
                                                                    @endphp
                                                                    
                                                                    <div class="row g-2 mb-3">
                                                                        <div class="col-md-5">
                                                                            {{-- INFO CUSTOMER CARD --}}
                                                                            <div class="card card-custom h-100 border-left-emerald shadow-sm">
                                                                                <div class="card-body p-2 d-flex flex-column justify-content-center">
                                                                                    <div class="d-flex align-items-center">
                                                                                        <div class="bg-emerald-soft rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 36px; height: 36px; flex-shrink: 0;">
                                                                                            <i class="fas fa-store text-emerald-custom fs-6"></i>
                                                                                        </div>
                                                                                        <div>
                                                                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                                                                <span class="fw-bolder text-slate-dark" style="font-size: 0.85rem;">{{ $item->customer->nama_customer }}</span>
                                                                                                @if($tier == 'gold')
                                                                                                    <span class="badge bg-warning text-dark shadow-sm border border-warning" style="font-size: 0.55rem;"><i class="fas fa-crown text-danger"></i> GOLD</span>
                                                                                                @elseif($tier == 'silver')
                                                                                                    <span class="badge bg-light text-dark shadow-sm border" style="font-size: 0.55rem;"><i class="fas fa-award text-secondary"></i> SILVER</span>
                                                                                                @elseif($tier == 'bronze')
                                                                                                    <span class="badge badge-bronze shadow-sm" style="font-size: 0.55rem;"><i class="fas fa-medal"></i> BRONZE</span>
                                                                                                @else
                                                                                                    <span class="badge bg-secondary text-white shadow-sm" style="font-size: 0.55rem;"><i class="fas fa-user"></i> REGULER</span>
                                                                                                @endif
                                                                                            </div>
                                                                                            <div class="d-flex gap-2 small text-slate-muted fw-medium" style="font-size: 0.65rem;">
                                                                                                <span><i class="fas fa-user-tag text-emerald-custom me-1"></i>{{ $item->user->name }}</span>
                                                                                                <span><i class="fas fa-calendar-alt text-emerald-custom me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal_order)->format('d M Y') }}</span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-7">
                                                                            {{-- METRIK KEUANGAN --}}
                                                                            <div class="card card-custom h-100 shadow-sm border-0 bg-white">
                                                                                <div class="card-body p-2">
                                                                                    <div class="row g-1 text-center">
                                                                                        <div class="col-4 border-end">
                                                                                            <span class="text-slate-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.6rem;">Plafon Kredit</span>
                                                                                            <strong class="text-slate-dark" style="font-size: 0.75rem;">Rp {{ number_format($plafon, 0, ',', '.') }}</strong>
                                                                                        </div>
                                                                                        <div class="col-4 border-end">
                                                                                            <span class="text-slate-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.6rem;">Piutang Berjalan</span>
                                                                                            <strong class="text-danger" style="font-size: 0.75rem;">Rp {{ number_format($piutang, 0, ',', '.') }}</strong>
                                                                                        </div>
                                                                                        <div class="col-4">
                                                                                            <span class="text-slate-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.6rem;">Sisa Limit</span>
                                                                                            <strong class="{{ $sisa_limit < $item->total_semua ? 'text-danger' : 'text-success' }}" style="font-size: 0.75rem;">Rp {{ number_format($sisa_limit, 0, ',', '.') }}</strong>
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    {{-- Warning Overlimit --}}
                                                                                    <div class="plafon-warning-box warning-box-{{ $item->id }} {{ $sisa_limit < $item->total_semua ? '' : 'd-none' }}">
                                                                                        <i class="fas fa-exclamation-triangle"></i>
                                                                                        <span class="warning-text-{{ $item->id }}">OVERLIMIT! Nilai SO melebihi sisa plafon sebesar Rp {{ number_format($item->total_semua - $sisa_limit, 0, ',', '.') }}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    {{-- TABEL RINCIAN ITEM SO --}}
                                                                    <div class="card card-custom border-0 shadow-sm mb-3">
                                                                        <div class="card-header bg-emerald-soft text-slate-dark py-2 border-bottom">
                                                                            <span class="fw-bold" style="font-size: 0.75rem;"><i class="fas fa-box-open me-2"></i>Rincian Item SO</span>
                                                                        </div>
                                                                        <div class="card-body p-0">
                                                                            <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                                                                                <table class="table table-sm table-compact align-middle mb-0">
                                                                                    <thead>
                                                                                        <tr class="bg-light">
                                                                                            <th class="ps-3" style="width: 30%;">Nama Barang</th>
                                                                                            <th class="text-center" style="width: 15%;">Qty</th>
                                                                                            <th class="text-end" style="width: 35%;">Harga Satuan</th>
                                                                                            <th class="text-end pe-3" style="width: 20%;">Subtotal</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        @foreach($item->details as $detail)
                                                                                        @php
                                                                                            $hargaNormal = $detail->barang->harga_jual ?? 0;
                                                                                            $hargaBronze = $hargaNormal * 0.98;
                                                                                            $hargaSilver = $hargaNormal * 0.95;
                                                                                            $hargaGold = $hargaNormal * 0.90;
                                                                                        @endphp
                                                                                        <tr>
                                                                                            <td class="ps-3 py-2">
                                                                                                <span class="fw-bold d-block text-slate-dark">{{ $detail->barang->nama_barang }}</span>
                                                                                                <span class="text-slate-muted" style="font-size: 0.65rem;"><i class="fas fa-barcode me-1"></i>SKU: {{ $detail->barang->kode_barang }}</span>
                                                                                                <span class="d-block text-slate-muted mt-1" style="font-size: 0.65rem;">
                                                                                                    <i class="fas fa-warehouse me-1 text-emerald-custom"></i>Stok Tersedia: <strong class="text-emerald-custom">{{ $detail->barang->stok_akhir ?? 0 }}</strong> {{ $detail->barang->satuan ?? 'Unit' }}
                                                                                                </span>
                                                                                            </td>
                                                                                            <td class="text-center py-2">
                                                                                                <input type="number" 
                                                                                                       name="qty[{{ $detail->id }}]" 
                                                                                                       id="input-qty-{{ $item->id }}-{{ $detail->id }}" 
                                                                                                       class="form-control input-qty-edit text-center input-qty-{{ $item->id }}" 
                                                                                                       value="{{ $detail->jumlah }}" 
                                                                                                       data-detail-id="{{ $detail->id }}"
                                                                                                       min="1" required>
                                                                                            </td>
                                                                                            <td class="text-end py-2">
                                                                                                <input type="number" 
                                                                                                       name="harga[{{ $detail->id }}]" 
                                                                                                       id="input-harga-{{ $item->id }}-{{ $detail->id }}" 
                                                                                                       class="form-control input-harga-edit text-end input-harga-{{ $item->id }}" 
                                                                                                       value="{{ intval($detail->harga_satuan) }}" 
                                                                                                       data-detail-id="{{ $detail->id }}"
                                                                                                       data-hpp="{{ $detail->hpp > 0 ? $detail->hpp : ($detail->barang->harga_beli ?? 0) }}"
                                                                                                       style="max-width: 90px; margin-left: auto;"
                                                                                                       required>
                                                                                                
                                                                                                <div class="d-flex flex-wrap justify-content-end gap-1 mt-1" style="font-size:0.65rem;">
                                                                                                    <span class="badge bg-secondary border-0 btn-apply-harga p-1 {{ $tier == 'reguler' ? 'border border-dark shadow' : '' }}" data-target="input-harga-{{ $item->id }}-{{ $detail->id }}" data-harga="{{ $hargaNormal }}" title="Reguler : Tidak ada potongan" style="cursor: pointer;"><i class="fas fa-tag d-none d-md-inline me-1"></i>R</span>
                                                                                                    <span class="badge badge-bronze border-0 btn-apply-harga p-1 {{ $tier == 'bronze' ? 'border border-dark shadow' : '' }}" data-target="input-harga-{{ $item->id }}-{{ $detail->id }}" data-harga="{{ $hargaBronze }}" title="Bronze : Potongan 2%" style="cursor: pointer;"><i class="fas fa-medal d-none d-md-inline me-1"></i>B</span>
                                                                                                    <span class="badge bg-light text-dark border btn-apply-harga p-1 {{ $tier == 'silver' ? 'border-dark shadow bg-white' : '' }}" data-target="input-harga-{{ $item->id }}-{{ $detail->id }}" data-harga="{{ $hargaSilver }}" title="Silver : Potongan 5%" style="cursor: pointer;"><i class="fas fa-award d-none d-md-inline me-1"></i>S</span>
                                                                                                    <span class="badge bg-warning text-dark border-0 btn-apply-harga p-1 {{ $tier == 'gold' ? 'border border-dark shadow' : '' }}" data-target="input-harga-{{ $item->id }}-{{ $detail->id }}" data-harga="{{ $hargaGold }}" title="Gold : Potongan 10%" style="cursor: pointer;"><i class="fas fa-crown d-none d-md-inline me-1"></i>G</span>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td class="text-end pe-3 py-2 fw-bolder text-emerald-custom subtotal-text-{{ $item->id }}-{{ $detail->id }}" style="font-size: 0.8rem;">
                                                                                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                                                                            </td>
                                                                                        </tr>
                                                                                        @endforeach
                                                                                    </tbody>
                                                                                    <tfoot class="bg-light">
                                                                                        <tr>
                                                                                            <td colspan="3" class="text-end fw-bold px-3 py-2 text-slate-muted text-uppercase" style="font-size: 0.7rem;">Grand Total Order:</td>
                                                                                            <td class="text-end pe-3 py-2 fw-bolder text-slate-dark" id="grand-total-{{ $item->id }}" style="font-size: 0.9rem;">
                                                                                                Rp {{ number_format($item->total_semua, 0, ',', '.') }}
                                                                                            </td>
                                                                                        </tr>
                                                                                        @if(in_array(strtolower(Auth::user()->role), ['direktur', 'superadmin']))
                                                                                        <tr>
                                                                                            <td colspan="3" class="text-end fw-bold px-3 py-1 text-slate-muted text-uppercase border-top-0" style="font-size: 0.65rem;"><i class="fas fa-chart-line text-info me-1"></i>Laba Kotor:</td>
                                                                                            <td class="text-end pe-3 py-1 fw-bolder text-info border-top-0" id="laba-kotor-{{ $item->id }}" style="font-size: 0.8rem;">
                                                                                                @php 
                                                                                                    $totalHPP = 0;
                                                                                                    foreach($item->details as $d) {
                                                                                                        $hppItem = $d->hpp > 0 ? $d->hpp : ($d->barang->harga_beli ?? 0);
                                                                                                        $totalHPP += ($hppItem * $d->jumlah);
                                                                                                    }
                                                                                                    $laba = $item->total_semua - $totalHPP;
                                                                                                @endphp
                                                                                                Rp {{ number_format($laba, 0, ',', '.') }}
                                                                                            </td>
                                                                                        </tr>
                                                                                        @endif
                                                                                    </tfoot>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    {{-- CATATAN EKSEKUTIF --}}
                                                                    <div class="mb-1">
                                                                        <label class="form-label fw-bold text-slate-dark mb-1" style="font-size: 0.7rem;"><i class="fas fa-pen-nib text-emerald-custom me-1"></i> Catatan Persetujuan (Opsional)</label>
                                                                        <textarea name="catatan" class="form-control form-control-sm bg-white shadow-sm" rows="1" placeholder="Masukkan pesan khusus..." style="font-size: 0.75rem; border-radius: 8px; border-color: #cbd5e1;"></textarea>
                                                                    </div>

                                                                </div>

                                                                {{-- FOOTER MODAL --}}
                                                                <div class="modal-footer bg-white py-2 px-3 d-flex justify-content-between border-top">
                                                                    <div class="small text-slate-muted fw-medium" style="font-size: 0.65rem;"><i class="fas fa-shield-alt text-emerald-custom me-1"></i> Tercatat di Audit Trail.</div>
                                                                    <div class="d-flex flex-column align-items-end gap-1">
                                                                        <div class="d-flex gap-2">
                                                                            <button type="submit" name="status" value="ditolak" class="btn btn-sm btn-outline-danger fw-bolder px-3 rounded-pill shadow-sm">Tolak</button>
                                                                            @if(strtolower(Auth::user()->role) === 'manager' && $sisa_limit < $item->total_semua)
                                                                                <button type="submit" name="status" value="disetujui" id="btn-approve-{{ $item->id }}" class="btn btn-sm btn-emerald-custom fw-bolder px-3 rounded-pill shadow-sm" disabled><i class="fas fa-check-circle me-1"></i> Setujui</button>
                                                                            @else
                                                                                <button type="submit" name="status" value="disetujui" id="btn-approve-{{ $item->id }}" class="btn btn-sm btn-emerald-custom fw-bolder px-3 rounded-pill shadow-sm"><i class="fas fa-check-circle me-1"></i> Setujui</button>
                                                                            @endif
                                                                        </div>
                                                                        <small class="text-danger text-approve-warning-{{ $item->id }} {{ (strtolower(Auth::user()->role) === 'manager' && $sisa_limit < $item->total_semua) ? '' : 'd-none' }}" style="font-size: 0.7rem; font-weight: 600;">
                                                                            <i class="fas fa-ban me-1"></i> Manager tidak bisa menyetujui pesanan over limit.
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center p-2 rounded bg-light border" style="line-height: 1.2;">
                                                    <span class="text-slate-muted" style="font-size: 0.65rem;">Diproses oleh:</span><br>
                                                    <strong class="text-slate-dark" style="font-size: 0.75rem;">{{ $item->approver->name ?? 'Direktur' }}</strong>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-slate-muted bg-white">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-inbox fa-3x mb-3 text-muted opacity-25"></i>
                                                <span>Tidak ada dokumen Sales Order yang perlu direview saat ini.</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- MOBILE CARDS --}}
                    <div class="d-lg-none p-2">
                        @forelse($pengajuan as $item)
                        @php
                            $borderColor = match(strtolower($item->status_approval)) {
                                'disetujui' => '#10b981',
                                'ditolak' => '#ef4444',
                                default => '#f59e0b'
                            };
                        @endphp
                        <div class="card card-custom mb-3" style="border-left: 4px solid {{ $borderColor }} !important;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-emerald-custom" style="font-size: 0.95rem;">{{ $item->no_so }}</span>
                                    <span class="small text-muted">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        @if($item->sales_created_at)
                                            {{ $item->sales_created_at->format('d/m/y H:i') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($item->tanggal_order)->format('d/m/y') }}
                                        @endif
                                    </span>
                                </div>
                                
                                <h6 class="fw-bold text-slate-dark mb-1" style="font-size: 1.05rem;">{{ $item->customer->nama_customer }}</h6>
                                <div class="text-muted mb-2" style="font-size: 0.8rem;">
                                    <span>Sales: <strong>{{ $item->user->name }}</strong></span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-2" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                                    <div>
                                        <small class="text-muted d-block" style="font-size:0.65rem;">Nilai Order</small>
                                        <span class="fw-bold text-slate-dark" style="font-size:0.9rem;">Rp {{ number_format($item->total_semua, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block" style="font-size:0.65rem;">Kelayakan (SPK)</small>
                                        @php 
                                            $skor = $item->skor_spk ?? 0;
                                            $skorColor = ($skor >= 70) ? 'text-success' : (($skor >= 40) ? 'text-warning' : 'text-danger');
                                        @endphp
                                        <span class="fw-bold {{ $skorColor }}" style="font-size: 0.85rem;">{{ number_format($skor, 0) }}%</span>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        @if($item->status_approval == 'pending')
                                            <span class="badge bg-warning text-dark px-2 py-1" style="border-radius: 6px; font-size: 0.7rem;">PENDING</span>
                                        @elseif($item->status_approval == 'disetujui')
                                            <span class="badge bg-success text-white px-2 py-1" style="border-radius: 6px; font-size: 0.7rem;">DISETUJUI</span>
                                        @else
                                            <span class="badge bg-danger text-white px-2 py-1" style="border-radius: 6px; font-size: 0.7rem;">DITOLAK</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($item->status_approval == 'pending')
                                            <button type="button" class="btn btn-sm btn-emerald-custom px-3 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalApprove{{ $item->id }}">
                                                <i class="fas fa-search me-1"></i> Rincian
                                            </button>
                                        @else
                                            <div class="text-end" style="line-height: 1.1;">
                                                <small class="text-muted" style="font-size: 0.65rem;">Diproses oleh:</small><br>
                                                <strong class="text-slate-dark" style="font-size: 0.75rem;">{{ $item->approver->name ?? 'Direktur' }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5 text-slate-muted bg-white rounded-3 shadow-sm border">
                            <i class="fas fa-inbox fa-3x mb-3 text-muted opacity-25"></i>
                            <p class="mb-0">Tidak ada dokumen Sales Order yang perlu direview saat ini.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // FUNGSI UMUM UNTUK MENGHITUNG ULANG SUBTOTAL & GRAND TOTAL DI DALAM SATU MODAL
    const isManager = {{ strtolower(Auth::user()->role) === 'manager' ? 'true' : 'false' }};

    function hitungUlangModal(modalId) {
        let grandTotal = 0;
        let totalHpp = 0;
        const semuaHarga = document.querySelectorAll(`.input-harga-${modalId}`);
        
        semuaHarga.forEach(function(inputHarga) {
            const detailId = inputHarga.dataset.detailId;
            const hpp = parseFloat(inputHarga.dataset.hpp) || 0;
            const inputQty = document.getElementById(`input-qty-${modalId}-${detailId}`);
            const qty = parseFloat(inputQty.value) || 0;
            const harga = parseFloat(inputHarga.value) || 0;
            
            const subtotal = qty * harga;
            const subtotalHpp = qty * hpp;
            
            const subtotalEl = document.querySelector(`.subtotal-text-${modalId}-${detailId}`);
            if(subtotalEl) { subtotalEl.innerText = 'Rp ' + subtotal.toLocaleString('id-ID'); }
            
            grandTotal += subtotal;
            totalHpp += subtotalHpp;
        });
        
        const grandTotalEl = document.getElementById(`grand-total-${modalId}`);
        if(grandTotalEl) { grandTotalEl.innerText = 'Rp ' + grandTotal.toLocaleString('id-ID'); }

        const labaKotorEl = document.getElementById(`laba-kotor-${modalId}`);
        if(labaKotorEl) {
            const laba = grandTotal - totalHpp;
            labaKotorEl.innerText = 'Rp ' + laba.toLocaleString('id-ID');
            
            if(laba < 0) {
                labaKotorEl.classList.remove('text-info');
                labaKotorEl.classList.add('text-danger');
            } else {
                labaKotorEl.classList.remove('text-danger');
                labaKotorEl.classList.add('text-info');
            }
        }

        // Hitung ulang status limit & disable button untuk manager
        const modalContainer = document.getElementById(`modalApprove${modalId}`);
        if (modalContainer) {
            const sisaLimit = parseFloat(modalContainer.dataset.sisaLimit) || 0;
            
            // Toggle Warning Overlimit Box
            const warningBoxEl = document.querySelector(`.warning-box-${modalId}`);
            const warningTextEl = document.querySelector(`.warning-text-${modalId}`);
            if (warningBoxEl && warningTextEl) {
                if (grandTotal > sisaLimit) {
                    const diff = grandTotal - sisaLimit;
                    warningTextEl.innerText = 'OVERLIMIT! Nilai SO melebihi sisa plafon sebesar Rp ' + parseInt(diff).toLocaleString('id-ID');
                    warningBoxEl.classList.remove('d-none');
                } else {
                    warningBoxEl.classList.add('d-none');
                }
            }

            // Disable/Enable Approve Button
            if (isManager) {
                const btnApprove = document.getElementById(`btn-approve-${modalId}`);
                const warningEl = document.querySelector(`.text-approve-warning-${modalId}`);
                if (grandTotal > sisaLimit) {
                    if (btnApprove) btnApprove.disabled = true;
                    if (warningEl) warningEl.classList.remove('d-none');
                } else {
                    if (btnApprove) btnApprove.disabled = false;
                    if (warningEl) warningEl.classList.add('d-none');
                }
            }
        }
    }

    const inputHargas = document.querySelectorAll('input[class*="input-harga-edit"]');
    inputHargas.forEach(function(input) {
        input.addEventListener('input', function() {
            const modalIdMatch = this.className.match(/input-harga-(\d+)/);
            if (modalIdMatch) hitungUlangModal(modalIdMatch[1]);
        });
    });

    const inputQtys = document.querySelectorAll('input[class*="input-qty-edit"]');
    inputQtys.forEach(function(input) {
        input.addEventListener('input', function() {
            const modalIdMatch = this.className.match(/input-qty-(\d+)/);
            if (modalIdMatch) hitungUlangModal(modalIdMatch[1]);
        });
    });

    const btnApplyHargas = document.querySelectorAll('.btn-apply-harga');
    btnApplyHargas.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const hargaTier = this.dataset.harga;
            const targetInput = document.getElementById(targetId);
            if (targetInput) {
                targetInput.value = hargaTier;
                targetInput.dispatchEvent(new Event('input'));
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) })
});
</script>
@endsection