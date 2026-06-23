@extends('layouts.app')

@section('content')
<style>
    body { background-color: #f8fafc !important; }
    .text-slate-dark { color: #0f172a !important; }
    .text-slate-muted { color: #64748b !important; }
    .text-emerald { color: #10b981 !important; }
    .bg-emerald { background-color: #10b981 !important; }
    .bg-gradient-emerald { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-slate { background: linear-gradient(135deg, #334155 0%, #0f172a 100%); }
    .border-left-emerald { border-left: 4px solid #10b981 !important; }
    .border-left-info { border-left: 4px solid #0ea5e9 !important; }
    .border-left-warning { border-left: 4px solid #f59e0b !important; }
    .letter-spacing-wide { letter-spacing: 0.5px; }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bolder" style="letter-spacing: -0.5px;">
                <i class="fas fa-file-invoice-dollar text-emerald me-2"></i> Detail Sales Order
            </h1>
            <p class="text-slate-muted small mb-0 mt-1">Nomor Referensi: <span class="fw-bold text-slate-dark">{{ $penjualan->no_so }}</span></p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('penjualan.index') }}" class="btn btn-white border shadow-sm rounded-pill fw-medium px-4">
                <i class="fas fa-arrow-left me-2 text-muted"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Kolom Kiri -->
        <div class="col-lg-4">
            <!-- Informasi Order Card -->
            <div class="card card-custom border-left-emerald mb-4" style="min-height: 250px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="mb-0 fw-bold text-slate-dark text-uppercase letter-spacing-wide" style="font-size: 0.85rem;">
                        <i class="fas fa-info-circle text-emerald me-2"></i> Informasi Order
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mt-2">
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                            <span class="text-slate-muted small"><i class="fas fa-user-tie me-2"></i>Customer</span>
                            <span class="fw-bold text-slate-dark">{{ $penjualan->customer->nama_customer }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                            <span class="text-slate-muted small"><i class="fas fa-calendar-alt me-2"></i>Tanggal</span>
                            <span class="fw-bold text-slate-dark">{{ \Carbon\Carbon::parse($penjualan->tanggal_order)->format('d M Y') }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                            <span class="text-slate-muted small"><i class="fas fa-user-tag me-2"></i>Sales</span>
                            <span class="fw-bold text-slate-dark">{{ $penjualan->user->name }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                            <span class="text-slate-muted small"><i class="fas fa-chart-pie me-2"></i>Probabilitas</span>
                            <span class="fw-bolder text-emerald">{{ $penjualan->peluang }}%</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent border-bottom-0 pb-0">
                            <span class="text-slate-muted small"><i class="fas fa-flag me-2"></i>Status</span>
                            <span class="badge badge-gacor {{ $penjualan->status_approval == 'disetujui' ? 'bg-success bg-opacity-10 text-success border border-success' : ($penjualan->status_approval == 'pending' ? 'bg-warning bg-opacity-10 text-warning border border-warning' : 'bg-danger bg-opacity-10 text-danger border border-danger') }}">
                                {{ ucfirst($penjualan->status_approval) }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Audit Trail Card -->
            <div class="card card-custom border-left-info mt-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="mb-0 fw-bold text-slate-dark text-uppercase letter-spacing-wide" style="font-size: 0.85rem;">
                        <i class="fas fa-history text-info me-2"></i> Audit Trail Transaksi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="position-relative ms-3 mt-2 border-start border-2 pb-4" style="border-color: #cbd5e1 !important;">
                        <!-- Milestone 1 -->
                        <div class="position-relative mb-4 ms-4">
                            <span class="position-absolute top-0 start-0 translate-middle p-2 bg-white border border-info border-2 rounded-circle shadow-sm" style="left: -1.05rem !important; margin-top: 0.2rem;"></span>
                            <small class="text-info fw-bold d-block text-uppercase letter-spacing-wide" style="font-size: 0.7rem;">Dibuat Oleh Sales</small>
                            <span class="fw-bold text-slate-dark d-block mt-1">{{ $penjualan->sales_created_by ?? $penjualan->user->name }}</span>
                            <small class="text-slate-muted"><i class="far fa-clock me-1"></i> {{ $penjualan->sales_created_at ? $penjualan->sales_created_at->format('d M Y, H:i') : '-' }} WIB</small>
                        </div>
                        
                        <!-- Milestone 2 -->
                        <div class="position-relative ms-4">
                            @if($penjualan->status_approval === 'disetujui')
                                <span class="position-absolute top-0 start-0 translate-middle p-2 bg-success rounded-circle shadow-sm" style="left: -1.05rem !important; margin-top: 0.2rem;"></span>
                                <small class="text-success fw-bold d-block text-uppercase letter-spacing-wide" style="font-size: 0.7rem;">Telah Disetujui</small>
                                <span class="fw-bold text-slate-dark d-block mt-1">Oleh: {{ $penjualan->approver->name ?? 'Direktur' }}</span>
                                <small class="text-slate-muted"><i class="far fa-clock me-1"></i> {{ $penjualan->approved_at ? $penjualan->approved_at->format('d M Y, H:i') : '-' }} WIB</small>
                            @elseif($penjualan->status_approval === 'ditolak')
                                <span class="position-absolute top-0 start-0 translate-middle p-2 bg-danger rounded-circle shadow-sm" style="left: -1.05rem !important; margin-top: 0.2rem;"></span>
                                <small class="text-danger fw-bold d-block text-uppercase letter-spacing-wide" style="font-size: 0.7rem;">Ditolak</small>
                                <span class="fw-bold text-slate-dark d-block mt-1">Oleh: {{ $penjualan->approver->name ?? 'Direktur' }}</span>
                                <small class="text-slate-muted"><i class="far fa-clock me-1"></i> {{ $penjualan->approved_at ? $penjualan->approved_at->format('d M Y, H:i') : '-' }} WIB</small>
                            @else
                                <span class="position-absolute top-0 start-0 translate-middle p-2 bg-warning rounded-circle shadow-sm" style="left: -1.05rem !important; margin-top: 0.2rem;"></span>
                                <small class="text-warning text-dark fw-bold d-block text-uppercase letter-spacing-wide" style="font-size: 0.7rem;">Menunggu Review</small>
                                <span class="text-slate-muted d-block mt-1 fst-italic small">Belum ada tindakan dari Direktur.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan (Daftar Barang) -->
        <div class="col-lg-8">
            <div class="card card-custom overflow-hidden">
                <div class="card-header bg-gradient-emerald text-white py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bolder letter-spacing-wide"><i class="fas fa-boxes me-2 text-white"></i> Rincian Pesanan Barang</h6>
                    <span class="badge bg-white text-emerald rounded-pill shadow-sm px-3">{{ count($penjualan->details) }} Item</span>
                </div>
                <div class="card-body p-0">
                                        {{-- MOBILE CARDS --}}
                    <div class="d-lg-none p-3" style="background-color: var(--bg-page);">
                        @foreach($penjualan->details as $detail)
                        <div class="card mb-3 shadow-sm border-0" style="border-radius: 12px;">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-slate-dark mb-1" style="font-size: 0.95rem;">{{ $detail->barang->nama_barang }}</h6>
                                <div class="text-muted small mb-2"><i class="fas fa-barcode me-1 opacity-50"></i>{{ $detail->barang->kode_barang ?? '-' }}</div>
                                
                                <div class="d-flex justify-content-between align-items-center bg-light p-2 mb-2" style="border-radius: 8px;">
                                    <div>
                                        <span class="d-block text-muted" style="font-size: 0.65rem;">Harga Satuan</span>
                                        <span class="fw-bold text-slate-dark">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</span>
                                        @if(strtolower(Auth::user()->role) != 'sales' && ($detail->hpp > 0 || isset($detail->barang->harga_beli)))
                                        <div class="text-danger fw-bold" style="font-size: 0.7rem;">HPP: Rp {{ number_format($detail->hpp > 0 ? $detail->hpp : ($detail->barang->harga_beli ?? 0), 0, ',', '.') }}</div>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <span class="d-block text-muted" style="font-size: 0.65rem;">Qty</span>
                                        <span class="badge bg-white text-slate-dark border px-3 py-1 fs-6 rounded-pill shadow-sm">{{ $detail->jumlah }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                    <span class="text-slate-muted fw-bold" style="font-size: 0.8rem;">Subtotal</span>
                                    <span class="fw-bolder text-emerald fs-6">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="table-responsive d-none d-lg-block">
                        <table class="table table-gacor mb-0">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3">Nama Barang</th>
                                    <th class="text-center py-3">Qty</th>
                                    <th class="text-end py-3">Harga Satuan</th>
                                    <th class="text-end px-4 py-3">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penjualan->details as $detail)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3">
                                        <div class="fw-bold text-slate-dark">{{ $detail->barang->nama_barang }}</div>
                                        <div class="text-muted small"><i class="fas fa-barcode me-1 opacity-50"></i>{{ $detail->barang->kode_barang ?? '-' }}</div>
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="badge bg-light text-slate-dark border px-3 py-2 fs-6 rounded-pill shadow-sm">{{ $detail->jumlah }}</span>
                                    </td>
                                    <td class="text-end py-3">
                                        <div class="fw-bold text-slate-dark">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</div>
                                        @if($detail->hpp > 0 || isset($detail->barang->harga_beli))
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            HPP: <span class="text-danger fw-bold">Rp {{ number_format($detail->hpp > 0 ? $detail->hpp : ($detail->barang->harga_beli ?? 0), 0, ',', '.') }}</span>
                                        </div>
                                        @endif
                                    </td>
                                    <td class="text-end px-4 py-3 fw-bolder text-emerald fs-6">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light border-top-0 py-4 px-4" style="background-color: #f8fafc !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-slate-muted fw-bold text-uppercase letter-spacing-wide">Total Nilai Transaksi</span>
                        <h2 class="mb-0 fw-bolder text-slate-dark" style="letter-spacing: -1px;">
                            <span class="text-emerald fs-4 me-1">Rp</span>{{ number_format($penjualan->total_semua, 0, ',', '.') }}
                        </h2>
                    </div>
                </div>
            </div>

            <!-- Riwayat Pengiriman Card -->
            <div class="card card-custom border-left-warning mt-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="mb-0 fw-bold text-slate-dark text-uppercase letter-spacing-wide" style="font-size: 0.85rem;">
                        <i class="fas fa-truck text-warning me-2"></i> Riwayat Pengiriman Barang (Shipments)
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if($penjualan->pengirimans->isEmpty())
                        <div class="text-center py-4 text-muted bg-light rounded-3">
                            <i class="fas fa-shipping-fast fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">Belum ada pengiriman fisik untuk order ini.</p>
                        </div>
                    @else
                        @foreach($penjualan->pengirimans as $indexP => $p)
                            <div class="border rounded-3 p-3 mb-3 bg-white shadow-sm">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-2 mb-3">
                                    <div>
                                        <h6 class="fw-bold text-slate-dark mb-1"><i class="fas fa-box text-slate-muted me-1"></i> Pengiriman Ke-{{ $indexP + 1 }}</h6>
                                        <small class="text-slate-muted">
                                            <strong>Surat Jalan:</strong> {{ $p->no_pengiriman }} &nbsp;|&nbsp; 
                                            <strong>Invoice:</strong> {{ $p->no_invoice }} &nbsp;|&nbsp;
                                            <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($p->tanggal_kirim)->format('d M Y') }}
                                        </small>
                                    </div>
                                    <div class="mt-2 mt-md-0">
                                        @if(strtolower(Auth::user()->role) != 'sales')
                                            <a href="{{ route('penjualan.printSuratJalanPengiriman', $p->id) }}" target="_blank" class="btn btn-sm btn-success-soft rounded-pill fw-bold px-3 me-1">
                                                <i class="fas fa-truck me-1"></i> Surat Jalan
                                            </a>
                                            <a href="{{ route('penjualan.printFakturPengiriman', $p->id) }}" target="_blank" class="btn btn-sm btn-info-soft rounded-pill fw-bold px-3">
                                                <i class="fas fa-file-invoice-dollar me-1"></i> Faktur
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="table-responsive d-none d-md-block">
                                    <table class="table table-sm table-borderless mb-0">
                                        <thead>
                                            <tr class="text-muted small border-bottom">
                                                <th>Deskripsi Barang</th>
                                                <th class="text-center">Kuantitas</th>
                                                <th class="text-end">Harga Satuan</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($p->details as $pDetail)
                                                <tr>
                                                    <td class="fw-bold text-slate-dark">{{ $pDetail->barang->nama_barang }}</td>
                                                    <td class="text-center"><span class="badge bg-light text-dark border px-2 py-1 rounded-pill">{{ $pDetail->jumlah_kirim }}</span></td>
                                                    <td class="text-end">Rp {{ number_format($pDetail->harga_satuan, 0, ',', '.') }}</td>
                                                    <td class="text-end fw-bold text-emerald">Rp {{ number_format($pDetail->subtotal, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="border-top fw-bold text-slate-dark">
                                                <td colspan="3" class="text-end pt-2">Total Tagihan Pengiriman:</td>
                                                <td class="text-end text-emerald pt-2">Rp {{ number_format($p->details->sum('subtotal'), 0, ',', '.') }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                {{-- MOBILE VIEW FOR SHIPMENT ITEMS --}}
                                <div class="d-md-none mt-3">
                                    @foreach($p->details as $pDetail)
                                        <div class="border rounded-2 p-2 mb-2 bg-light">
                                            <div class="fw-bold text-slate-dark mb-1" style="font-size: 0.9rem;">{{ $pDetail->barang->nama_barang }}</div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="text-muted small">Kuantitas:</span>
                                                <span class="badge bg-white text-dark border px-2 py-1 rounded-pill">{{ $pDetail->jumlah_kirim }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="text-muted small">Harga Satuan:</span>
                                                <span class="text-dark small">Rp {{ number_format($pDetail->harga_satuan, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center border-top pt-1 mt-1">
                                                <span class="text-muted small fw-bold">Subtotal:</span>
                                                <span class="text-emerald fw-bold">Rp {{ number_format($pDetail->subtotal, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                        <span class="fw-bold text-slate-dark small">Total Tagihan:</span>
                                        <span class="text-emerald fw-bold">Rp {{ number_format($p->details->sum('subtotal'), 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
