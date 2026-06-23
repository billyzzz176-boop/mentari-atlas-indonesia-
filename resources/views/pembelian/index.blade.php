@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    /* Global Overrides untuk Tema Premium Mentari Atlas */
    body { background-color: #f8fafc !important; }
    
    .text-emerald-custom { color: #10b981 !important; }
    .text-slate-dark { color: #0f172a !important; }
    .text-slate-muted { color: #64748b !important; }
    
    .bg-emerald-custom { background-color: #10b981 !important; color: #ffffff !important; }
    .btn-emerald-custom { background-color: #10b981 !important; border-color: #10b981 !important; color: #ffffff !important; font-weight: 500; transition: all 0.2s; }
    .btn-emerald-custom:hover { background-color: #059669 !important; color: #ffffff !important; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); }
    
    .btn-outline-emerald { color: #10b981; border-color: #10b981; background-color: transparent; }
    .btn-outline-emerald:hover { color: #fff; background-color: #10b981; border-color: #10b981; }

    /* Card & Table Styling */
    .card-custom { border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    
    /* Diet Ketat Tabel */
    .table-mentari-compact th, .table-mentari-compact td { padding: 0.75rem 0.5rem !important; }

    /* Soft Badges & Focus Elements */
    .badge-success-soft { background-color: #d1fae5 !important; color: #065f46 !important; border: 1px solid #a7f3d0; }
    .badge-danger-soft { background-color: #fee2e2 !important; color: #991b1b !important; border: 1px solid #fecaca; }
    .badge-warning-soft { background-color: #fef3c7 !important; color: #92400e !important; border: 1px solid #fde68a; }
    .badge-secondary-soft { background-color: #f1f5f9 !important; color: #475569 !important; border: 1px solid #cbd5e1; }
    .form-control:focus { border-color: #10b981; box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.15); background-color: #ffffff !important; }

    /* Nominal Fonts */
    .font-monospace-custom { font-family: 'Courier New', Courier, monospace; font-weight: 700; letter-spacing: -0.5px; }

    /* Styling Select2 Emerald Theme */
    .select2-container--bootstrap-5 .select2-selection {
        border-color: #e2e8f0 !important; border-radius: 0.375rem !important;
        padding: 0.375rem 0.75rem !important; height: auto !important;
        font-size: 0.8rem; background-color: #f8fafc;
    }
    .select2-container--bootstrap-5 .select2-selection:focus,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #10b981 !important; box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.15) !important; background-color: #ffffff;
    }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 80vh;">
    
    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold"><i class="fas fa-shopping-cart text-emerald-custom me-2"></i>Pembelian & Restock Barang</h1>
            <p class="text-slate-muted small mb-0 mt-1">Catat transaksi pembelian (PO) dan lakukan Quality Control (QC) saat barang tiba.</p>
        </div>
        
        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-md-end">
            <form action="{{ url('/pembelian') }}" method="GET" class="m-0" style="min-width: 300px;">
                <div class="input-group shadow-sm rounded-pill overflow-hidden border bg-white focus-ring-emerald transition-all">
                    <input type="text" name="search" class="form-control border-0 search-input ps-4 pe-4 bg-white" placeholder="Cari No. PO atau Supplier..." value="{{ request('search') }}">
                    <button class="btn bg-white border-0 text-emerald-custom px-3" type="submit"><i class="fas fa-search"></i></button>
                    @if(request('search'))
                        <a href="{{ url('/pembelian') }}" class="btn bg-white border-0 text-danger px-3 border-start" title="Hapus Pencarian"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert badge-success-soft alert-dismissible fade show border-0 shadow-sm rounded-3 px-4 py-3 mb-4">
            <i class="fas fa-check-circle text-success me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert badge-danger-soft alert-dismissible fade show border-0 shadow-sm rounded-3 px-4 py-3 mb-4">
            <i class="fas fa-exclamation-triangle text-danger me-2"></i><strong>Gagal!</strong>
            <ul class="mb-0 mt-1 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- KOLOM KIRI: FORM INPUT PEMBELIAN (BUAT PO DENGAN TAMBAH BARIS) --}}
        <div class="col-12 mb-4">
            <div class="card card-custom bg-white overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-slate-dark d-flex align-items-center">
                        <i class="fas fa-file-invoice text-emerald-custom me-2"></i> Buat Order (PO)
                    </h6>
                </div>

                <div class="card-body p-4">
                    <form action="{{ url('/pembelian') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-12 mb-3">
                                <label class="small fw-bold text-slate-dark mb-1">Nama Supplier <span class="text-danger">*</span></label>
                                <select name="nama_supplier" id="nama_supplier" class="form-select select2-supplier" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $s)
                                        <option value="{{ $s->nama_supplier }}">{{ $s->kode_supplier }} - {{ $s->nama_supplier }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <label class="small fw-bold text-slate-dark mb-1">Foto / Scan Invoice (Opsional)</label>
                                <input type="file" name="foto_invoice" class="form-control bg-light" accept="image/*">
                                <div class="form-text" style="font-size: 0.7rem;">Maksimal file 5MB (JPG, JPEG, PNG).</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold text-slate-dark mb-2">Daftar Item Barang PO <span class="text-danger">*</span></label>
                            <div id="po-items-list">
                                <!-- Baris Pertama (Wajib) -->
                                <div class="po-item-row p-3 mb-3 bg-light rounded-3 border position-relative" style="border-color: #e2e8f0 !important;">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-6 col-12 mb-2 mb-md-0">
                                            <label class="small fw-bold text-slate-muted mb-1">Item Barang</label>
                                            <select name="barang_id[]" class="form-select select2-barang" onchange="updateRowHPP(this)" required>
                                                <option value="">-- Cari SKU/Nama Barang --</option>
                                                @foreach($barangs as $b)
                                                    @php
                                                        $kurangBO = \App\Models\BackOrder::where('barang_id', $b->id)
                                                                        ->where(function($query) {
                                                                            $query->where('status_bo', 'antrean')
                                                                                  ->orWhere('status_bo', 'pending');
                                                                        })
                                                                        ->sum('jumlah_kurang');
                                                        
                                                        $infoBO = $kurangBO > 0 ? " | ⚠️ Restock: $kurangBO" : "";
                                                    @endphp
                                                    <option value="{{ $b->id }}" data-hpp="{{ $b->harga_beli ?? 0 }}">
                                                        [{{ $b->kode_barang }}] {{ $b->nama_barang }} (Sisa: {{ $b->stok_akhir ?? $b->stok ?? 0 }}{{ $infoBO }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-4 mb-2 mb-md-0">
                                            <label class="small fw-bold text-slate-muted mb-1 d-block text-nowrap">Qty</label>
                                            <input type="number" name="jumlah_beli[]" class="form-control text-center bg-white fw-bold input-qty" min="1" placeholder="0" oninput="hitungTotalPO()" required>
                                        </div>
                                        <div class="col-md-4 col-8 mb-2 mb-md-0">
                                            <label class="small fw-bold text-slate-muted mb-1 d-block text-nowrap">HPP / Pcs (Rp)</label>
                                            <input type="number" name="harga_beli_hpp[]" class="form-control text-end bg-white fw-bold input-hpp" min="0" placeholder="0" oninput="hitungTotalPO()" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-emerald btn-sm w-100 fw-bold rounded-pill mb-3" onclick="tambahItemRow()">
                                <i class="fas fa-plus me-1"></i> Tambah Item Barang
                            </button>
                        </div>

                        <div class="row align-items-center mt-3">
                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                <div class="p-3 rounded-3 border-0 badge-success-soft shadow-sm d-flex flex-column align-items-center justify-content-center">
                                    <span class="small fw-bold text-success text-uppercase tracking-wider mb-1">Estimasi Total Utang PO</span>
                                    <h4 class="fw-extrabold mb-0 text-success" id="label-total">Rp 0</h4>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <button type="submit" class="btn btn-emerald-custom w-100 fw-bold py-3 rounded-pill shadow-sm">
                                    <i class="fas fa-paper-plane me-1"></i> Ajukan Pembelian (PO)
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: TABEL RIWAYAT PEMBELIAN & QC --}}
        <div class="col-12">
            <h6 class="fw-bold text-slate-dark mb-3"><i class="fas fa-boxes text-emerald-custom me-2"></i> Jurnal Order & Quality Control</h6>
            <div class="table-wrapper-mentari">
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-mentari table-mentari-compact align-middle" style="width: 100%; font-size: 0.8rem;">
                        <thead>
                            <tr>
                                <th class="ps-3 text-center text-nowrap" style="width: 15%;">No PO</th>
                                <th style="max-width: 130px; width: 20%;">Supplier</th>
                                <th style="max-width: 160px; width: 30%;">Item Barang</th>
                                <th class="text-center" style="width: 10%;">Order</th>
                                <th class="text-center" style="width: 10%;">Status QC</th>
                                <th class="text-center pe-3" style="width: 15%;">Aksi / Hasil Sortir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $no_pembelian => $items)
                            @php
                                $firstItem = $items->first();
                                $rowCount = count($items);
                            @endphp
                            @foreach($items as $index => $r)
                            <tr>
                                @if($index === 0)
                                    <td rowspan="{{ $rowCount }}" class="ps-3 text-center fw-bold text-emerald-custom" style="vertical-align: middle; white-space: nowrap !important; border-right: 1px solid #f1f5f9;">
                                        {{ $no_pembelian }}<br>
                                        <small class="text-slate-muted fw-normal">{{ \Carbon\Carbon::parse($firstItem->tanggal_beli)->format('d/m/y') }}</small>
                                        @if($firstItem->foto_invoice)
                                            <a href="{{ route('berkas', $firstItem->foto_invoice) }}" target="_blank" class="badge badge-success-soft d-block mx-auto mt-2 py-1" style="width: fit-content; text-decoration: none;">
                                                <i class="fas fa-paperclip me-1"></i> Invoice
                                            </a>
                                        @endif
                                    </td>
                                    <td rowspan="{{ $rowCount }}" style="vertical-align: middle; border-right: 1px solid #f1f5f9;">
                                        <span class="badge badge-secondary-soft px-2 py-1 text-wrap text-start shadow-sm" style="max-width: 130px; line-height: 1.4;">
                                            <i class="fas fa-building me-1 opacity-50"></i>{{ $firstItem->nama_supplier }}
                                        </span>
                                    </td>
                                @endif
                                
                                <td class="fw-bold text-slate-dark text-wrap" style="max-width: 160px; line-height: 1.3;">
                                    {{ $r->barang->nama_barang }}<br>
                                    <span class="text-muted fw-normal" style="font-size: 0.7rem;">Rp {{ number_format($r->harga_beli_hpp, 0, ',', '.') }} / pcs</span>
                                </td>
                                
                                <td class="text-center fw-bold text-dark fs-6">
                                    {{ $r->jumlah_beli }}
                                </td>
                                
                                <td class="text-center">
                                    @if($r->status_barang === 'pending')
                                        <span class="badge badge-warning-soft px-2 py-1 rounded-pill"><i class="fas fa-clock me-1"></i>Menunggu Sortir</span>
                                    @else
                                        <span class="badge badge-success-soft px-2 py-1 rounded-pill"><i class="fas fa-check-circle me-1"></i>Selesai QC</span>
                                    @endif
                                </td>

                                @if($index === 0)
                                    <td rowspan="{{ $rowCount }}" class="text-center pe-3" style="vertical-align: middle; border-left: 1px solid #f1f5f9;">
                                        @if($firstItem->status_barang === 'pending')
                                            <button class="btn btn-sm btn-warning shadow-sm fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#modalQC{{ str_replace('-', '_', $no_pembelian) }}">
                                                <i class="fas fa-box-open me-1"></i> Mulai Sortir
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-light border fw-bold text-secondary shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalDetailQC{{ str_replace('-', '_', $no_pembelian) }}">
                                                <i class="fas fa-eye me-1"></i> Lihat Detail
                                            </button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-slate-muted bg-white">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-clipboard-list fa-3x mb-3 text-muted opacity-25"></i>
                                        <span>Belum ada riwayat transaksi pembelian dari supplier.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION DESKTOP --}}
                <div class="d-none d-lg-block mt-3 px-3">
                    {{ $poList->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>

        {{-- MOBILE CARDS --}}
        <div class="d-lg-none p-2">
            @forelse($riwayat as $no_pembelian => $items)
            @php
                $firstItem = $items->first();
                $statusPO = $firstItem->status_barang;
            @endphp
            <div class="card card-custom mb-3" style="border-left: 4px solid {{ $statusPO === 'pending' ? '#f59e0b' : '#10b981' }} !important;">
                <div class="card-header bg-white py-3 px-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #f1f5f9;">
                    <div>
                        <h6 class="fw-bold text-emerald-custom mb-0" style="font-size: 0.95rem;">{{ $no_pembelian }}</h6>
                        <span class="text-slate-muted small" style="font-size: 0.7rem;"><i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($firstItem->tanggal_beli)->format('d M Y') }}</span>
                    </div>
                    <div>
                        <span class="badge badge-secondary-soft text-wrap shadow-sm" style="font-size: 0.75rem;"><i class="fas fa-building me-1 opacity-50"></i>{{ $firstItem->nama_supplier }}</span>
                        @if($firstItem->foto_invoice)
                            <a href="{{ route('berkas', $firstItem->foto_invoice) }}" target="_blank" class="badge badge-success-soft d-block mt-1 text-center py-0.5" style="font-size: 0.65rem; text-decoration: none;">
                                <i class="fas fa-paperclip me-1"></i> Lampiran
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-3">
                    <label class="small text-muted fw-bold text-uppercase d-block mb-2" style="font-size: 0.65rem;">Item Barang PO:</label>
                    <div class="po-items-list mb-3">
                        @foreach($items as $item)
                        <div class="p-2 mb-2 bg-light rounded-3 border" style="font-size: 0.85rem;">
                            <div class="fw-bold text-slate-dark">{{ $item->barang->nama_barang }}</div>
                            <div class="d-flex justify-content-between mt-1 text-muted" style="font-size: 0.75rem;">
                                <span>Order: <strong class="text-dark">{{ $item->jumlah_beli }} Pcs</strong></span>
                                <span>HPP: <strong class="text-dark">Rp {{ number_format($item->harga_beli_hpp, 0, ',', '.') }}</strong></span>
                            </div>
                            @if($item->status_barang === 'selesai')
                            <div class="mt-2 pt-2 border-top d-flex justify-content-between text-slate-muted" style="font-size: 0.7rem;">
                                <span>Bagus: <strong class="text-success">{{ $item->qty_bagus }}</strong></span>
                                <span>Rusak: <strong class="text-danger">{{ $item->qty_rusak }}</strong></span>
                                <span>Kurang: <strong class="text-warning">{{ $item->qty_kurang }}</strong></span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.65rem;">Status PO:</small>
                            @if($statusPO === 'pending')
                                <span class="badge badge-warning-soft px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;"><i class="fas fa-clock me-1"></i>Menunggu Sortir</span>
                            @else
                                <span class="badge badge-success-soft px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;"><i class="fas fa-check-circle me-1"></i>Selesai QC</span>
                            @endif
                        </div>
                        <div>
                            @if($statusPO === 'pending')
                                <button class="btn btn-sm btn-warning shadow-sm fw-bold rounded-pill px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#modalQC{{ str_replace('-', '_', $no_pembelian) }}">
                                    <i class="fas fa-box-open me-1"></i> Sortir
                                </button>
                            @else
                                <button class="btn btn-sm btn-light border fw-bold text-secondary shadow-sm rounded-pill px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#modalDetailQC{{ str_replace('-', '_', $no_pembelian) }}">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted bg-white shadow-sm" style="border-radius: 16px;">Belum ada riwayat transaksi pembelian.</div>
            @endforelse

            {{-- PAGINATION MOBILE --}}
            <div class="mt-3">
                {{ $poList->links('pagination::bootstrap-5') }}
            </div>
        </div>

    </div>
</div>

{{-- MODAL QUALITY CONTROL & DETAIL QC (DI LUAR TABEL) --}}
@foreach($riwayat as $no_pembelian => $items)
    @php
        $firstItem = $items->first();
        $statusPO = $firstItem->status_barang;
    @endphp

    {{-- 1. MODAL UNTUK PROSES SORTIR (STATUS PENDING) --}}
    @if($statusPO === 'pending')
    <div class="modal fade" id="modalQC{{ str_replace('-', '_', $no_pembelian) }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('pembelian.sortir', $no_pembelian) }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
                @csrf
                <div class="modal-header bg-light border-0 py-3">
                    <h6 class="modal-title fw-bold text-slate-dark"><i class="fas fa-tasks text-warning me-2"></i>Quality Control (QC) Kedatangan Barang</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white" style="max-height: 70vh; overflow-y: auto;">
                    <div class="alert alert-info border-0 shadow-sm py-2.5 px-3 mb-4 d-flex align-items-center">
                        <i class="fas fa-info-circle fs-4 me-3 text-info"></i>
                        <div>
                            <span class="d-block small text-muted">Purchase Order:</span>
                            <strong class="text-dark fs-6">{{ $no_pembelian }}</strong>
                        </div>
                    </div>

                    @foreach($items as $item)
                        <input type="hidden" name="pembelian_ids[]" value="{{ $item->id }}">
                        <div class="p-3 mb-3 bg-light rounded-3 border" style="border-color: #e2e8f0 !important;">
                            <div class="fw-bold text-slate-dark mb-2" style="font-size: 0.9rem;">
                                <i class="fas fa-box text-emerald-custom me-2"></i>{{ $item->barang->nama_barang }}
                            </div>
                            <div class="small text-muted mb-2">Order awal: <strong>{{ $item->jumlah_beli }} Pcs</strong></div>
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="form-label small fw-bold text-success mb-1" style="font-size: 0.75rem;"><i class="fas fa-check-circle me-1"></i> Qty Bagus</label>
                                    <input type="number" name="qty_bagus[{{ $item->id }}]" class="form-control text-center fw-bold text-success border-success" value="{{ $item->jumlah_beli }}" min="0" required>
                                </div>
                                <div class="col-4">
                                    <label class="form-label small fw-bold text-danger mb-1" style="font-size: 0.75rem;"><i class="fas fa-times-circle me-1"></i> Qty Rusak</label>
                                    <input type="number" name="qty_rusak[{{ $item->id }}]" class="form-control text-center fw-bold text-danger border-danger" value="0" min="0" required>
                                </div>
                                <div class="col-4">
                                    <label class="form-label small fw-bold text-warning mb-1" style="font-size: 0.75rem; color: #d97706 !important;"><i class="fas fa-minus-circle me-1"></i> Qty Kurang</label>
                                    <input type="number" name="qty_kurang[{{ $item->id }}]" class="form-control text-center fw-bold border-warning" value="0" min="0" required style="color: #d97706;">
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="form-check form-switch mt-4 bg-light p-3 rounded border">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="potong_tagihan" id="potongTagihan{{ str_replace('-', '_', $no_pembelian) }}" value="1" checked style="cursor: pointer;">
                        <label class="form-check-label small fw-bold text-slate-dark" for="potongTagihan{{ str_replace('-', '_', $no_pembelian) }}" style="cursor: pointer;">
                            Auto-Debit Note (Klaim Utang) & Return Pembelian
                        </label>
                        <div class="small text-muted mt-1" style="font-size: 0.7rem; margin-left: 2.2rem;">
                            Jika diaktifkan, barang yang Rusak/Kurang akan otomatis memotong total utang tagihan supplier ke kita dan masuk ke return pembelian.
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-secondary shadow-sm px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-emerald-custom shadow-sm px-4 rounded-pill fw-bold" onclick="return confirm('Sudah yakin dengan hitungan QC-nya? Stok akan difinalisasi dan tidak dapat diubah lagi.')">
                        Selesai & Masukkan ke Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- 2. MODAL UNTUK LIHAT DETAIL HASIL SORTIR & KALKULASI TOTAL (STATUS SELESAI) --}}
    @if($statusPO === 'selesai')
    <div class="modal fade" id="modalDetailQC{{ str_replace('-', '_', $no_pembelian) }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
                <div class="modal-header bg-light border-0 py-3">
                    <h6 class="modal-title fw-bold text-slate-dark"><i class="fas fa-clipboard-check text-emerald-custom me-2"></i>Rincian Hasil Pemilahan QC</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="small text-slate-muted fw-bold text-uppercase d-block mb-1">No. Purchase Order</label>
                            <div class="fw-bold text-dark fs-6">{{ $no_pembelian }}</div>
                        </div>
                        <div class="col-6 text-end">
                            <label class="small text-slate-muted fw-bold text-uppercase d-block mb-1">Supplier</label>
                            <div class="fw-bold text-dark">{{ $firstItem->nama_supplier }}</div>
                        </div>
                    </div>
                    
                    <hr class="border-secondary-subtle my-3">
                    
                    <label class="small text-slate-muted fw-bold text-uppercase d-block mb-2">Komposisi Hasil Fisik Sortiran per Item</label>
                    @php $grandTotalBayar = 0; @endphp
                    @foreach($items as $item)
                        @php $grandTotalBayar += $item->total_bayar; @endphp
                        <div class="p-3 mb-3 bg-light rounded-3 border" style="border-color: #e2e8f0 !important;">
                            <div class="fw-bold text-slate-dark mb-2" style="font-size: 0.9rem;">
                                <i class="fas fa-box text-emerald-custom me-2"></i>{{ $item->barang->nama_barang }}
                            </div>
                            <div class="row g-2 text-center" style="font-size: 0.85rem;">
                                <div class="col-4 border-end">
                                    <span class="text-success fw-bold d-block" style="font-size: 0.75rem;"><i class="fas fa-check-circle me-1"></i> Bagus (Stok)</span>
                                    <span class="fw-bold text-success fs-6">{{ $item->qty_bagus }} Pcs</span>
                                </div>
                                <div class="col-4 border-end">
                                    <span class="text-danger fw-bold d-block" style="font-size: 0.75rem;"><i class="fas fa-times-circle me-1"></i> Rusak (Karantina)</span>
                                    <span class="fw-bold text-danger fs-6">{{ $item->qty_rusak }} Pcs</span>
                                </div>
                                <div class="col-4">
                                    <span class="text-warning fw-bold d-block" style="font-size: 0.75rem; color: #d97706 !important;"><i class="fas fa-minus-circle me-1"></i> Kurang</span>
                                    <span class="fw-bold fs-6" style="color: #d97706 !important;">{{ $item->qty_kurang }} Pcs</span>
                                </div>
                            </div>
                            <div class="mt-2 text-end text-muted small" style="font-size: 0.75rem; border-top: 1px dashed #e2e8f0; padding-top: 5px;">
                                Subtotal Nota: Rp {{ number_format($item->harga_beli_hpp, 0, ',', '.') }} / pcs x {{ $item->jumlah_beli }} = <strong>Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    @endforeach

                    {{-- BAGIAN KALKULASI PRESET --}}
                    <div class="p-3 rounded-3 shadow-sm border-0 badge-success-soft">
                        <div class="small fw-bold text-success text-uppercase tracking-wider mb-2 text-center">Kalkulasi Nilai Pembelian Transaksi</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-success">Total Biaya Jurnal PO:</span>
                            <span class="fw-extrabold text-success font-monospace-custom fs-5">Rp {{ number_format($grandTotalBayar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-2">
                    <button type="button" class="btn btn-secondary btn-sm shadow-sm px-4 rounded-pill" data-bs-dismiss="modal">Tutup Detail</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2-supplier').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Supplier --',
            allowClear: true,
            width: '100%'
        });

        $('.select2-barang').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Cari SKU/Nama Barang --',
            allowClear: true,
            width: '100%'
        });
    });

    function tambahItemRow() {
        const container = document.getElementById('po-items-list');
        
        // Buat element row baru
        const newRow = document.createElement('div');
        newRow.className = 'po-item-row p-3 mb-3 bg-light rounded-3 border position-relative';
        newRow.style.borderColor = '#e2e8f0';
        
        // Template untuk row baru (dengan tombol hapus di pojok kanan atas)
        newRow.innerHTML = `
            <button type="button" class="btn-close position-absolute" style="top: 10px; right: 10px; font-size: 0.75rem;" onclick="hapusItemRow(this)" title="Hapus Item"></button>
            <div class="row g-2 align-items-center">
                <div class="col-md-6 col-12 mb-2 mb-md-0">
                    <label class="small fw-bold text-slate-muted mb-1">Item Barang</label>
                    <select name="barang_id[]" class="form-select select-barang-po" onchange="updateRowHPP(this)" required>
                        <option value="">-- Cari SKU/Nama Barang --</option>
                        @foreach($barangs as $b)
                            @php
                                $kurangBO = \App\Models\BackOrder::where('barang_id', $b->id)
                                                ->where(function($query) {
                                                    $query->where('status_bo', 'antrean')
                                                          ->orWhere('status_bo', 'pending');
                                                })
                                                ->sum('jumlah_kurang');
                                
                                $infoBO = $kurangBO > 0 ? " | ⚠️ Restock: $kurangBO" : "";
                            @endphp
                            <option value="{{ $b->id }}" data-hpp="{{ $b->harga_beli ?? 0 }}">
                                [{{ $b->kode_barang }}] {{ $b->nama_barang }} (Sisa: {{ $b->stok_akhir ?? $b->stok ?? 0 }}{{ $infoBO }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-4 mb-2 mb-md-0">
                    <label class="small fw-bold text-slate-muted mb-1 d-block text-nowrap">Qty</label>
                    <input type="number" name="jumlah_beli[]" class="form-control text-center bg-white fw-bold input-qty" min="1" placeholder="0" oninput="hitungTotalPO()" required>
                </div>
                <div class="col-md-4 col-8 mb-2 mb-md-0">
                    <label class="small fw-bold text-slate-muted mb-1 d-block text-nowrap">HPP / Pcs (Rp)</label>
                    <input type="number" name="harga_beli_hpp[]" class="form-control text-end bg-white fw-bold input-hpp" min="0" placeholder="0" oninput="hitungTotalPO()" required>
                </div>
            </div>
        `;
        
        container.appendChild(newRow);
        
        // Inisialisasi select2 pada select baru
        $(newRow).find('.select-barang-po').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Cari SKU/Nama Barang --',
            allowClear: true,
            width: '100%'
        });
    }

    function hapusItemRow(buttonElement) {
        const row = buttonElement.closest('.po-item-row');
        row.remove();
        hitungTotalPO();
    }

    function updateRowHPP(selectElement) {
        if (!selectElement || selectElement.selectedIndex === -1) return;
        
        const row = selectElement.closest('.po-item-row');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const hpp = selectedOption.getAttribute('data-hpp');
        
        const hppInput = row.querySelector('.input-hpp');
        if (hpp !== null && hpp !== '') {
            hppInput.value = hpp;
        } else {
            hppInput.value = '';
        }
        hitungTotalPO();
    }

    function hitungTotalPO() {
        let grandTotal = 0;
        const rows = document.querySelectorAll('.po-item-row');
        
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
            const hpp = parseFloat(row.querySelector('.input-hpp').value) || 0;
            grandTotal += qty * hpp;
        });
        
        document.getElementById('label-total').innerText = "Rp " + parseInt(grandTotal).toLocaleString('id-ID');
    }
</script>
@endsection