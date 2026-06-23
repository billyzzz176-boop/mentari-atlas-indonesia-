@extends('layouts.app')

@section('content')


<style>
    /* Global Overrides untuk Tema Premium Mentari Atlas */
    body { background-color: #f8fafc !important; }
    
    /* Warna Merah Khusus Return Pembelian / Utang */
    .text-rose-custom { color: #e11d48 !important; }
    .bg-rose-custom { background-color: #e11d48 !important; color: #ffffff !important; }
    .btn-rose-custom { background-color: #e11d48 !important; border-color: #e11d48 !important; color: #ffffff !important; font-weight: 500; transition: all 0.2s; }
    .btn-rose-custom:hover { background-color: #be123c !important; color: #ffffff !important; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(225, 29, 72, 0.2); }

    .text-slate-dark { color: #0f172a !important; }
    .text-slate-muted { color: #64748b !important; }
    
    /* Card & Table Styling */
    .card-custom { border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .table-mentari thead th, .table-mentari thead th:last-child { background: linear-gradient(135deg, #e11d48 0%, #be123c 100%) !important; color: #ffffff !important; border-bottom: none !important; font-weight: 600 !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; white-space: nowrap; }
    
    /* Soft Badges */
    .badge-success-soft { background-color: #d1fae5 !important; color: #065f46 !important; border: 1px solid #a7f3d0; }
    .badge-danger-soft { background-color: #fee2e2 !important; color: #991b1b !important; border: 1px solid #fecaca; }
    .badge-warning-soft { background-color: #fef3c7 !important; color: #92400e !important; border: 1px solid #fde68a; }
    .badge-secondary-soft { background-color: #f1f5f9 !important; color: #475569 !important; border: 1px solid #cbd5e1; }
    .badge-info-soft { background-color: #e0f2fe !important; color: #0369a1 !important; border: 1px solid #bae6fd; }

    /* Custom Styling untuk Select2 agar serasi dengan Bootstrap 5 */
    .select2-container .select2-selection--single { height: 38px !important; border: 1px solid #dee2e6 !important; border-radius: 0.375rem !important; display: flex; align-items: center; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { color: #0f172a !important; line-height: normal !important; padding-left: 0.75rem !important; width: 100%; overflow: hidden; text-overflow: ellipsis; }
    .select2-search__field { border-radius: 0.25rem !important; }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 80vh;">
    
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold"><i class="fas fa-truck-loading text-rose-custom me-2"></i>Return Pembelian & Debit Note</h1>
            <p class="text-slate-muted small mb-0 mt-1">Kelola pengembalian cacat fisik ke Supplier atau klaim potongan tagihan utang (Debit Note).</p>
        </div>
        <a href="{{ route('retur.pembelian.create') }}" class="btn btn-rose-custom shadow-sm rounded-pill px-4">
            <i class="fas fa-plus me-1"></i> Catat Return ke Supplier
        </a>
    </div>

    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert badge-success-soft alert-dismissible fade show border-0 shadow-sm rounded-3 px-4 py-3 mb-4" role="alert">
            <i class="fas fa-check-circle text-success me-2"></i><strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert badge-danger-soft alert-dismissible fade show border-0 shadow-sm rounded-3 px-4 py-3 mb-4" role="alert">
            <i class="fas fa-exclamation-triangle text-danger me-2"></i><strong>Gagal!</strong> {{ $errors->first() }}
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- TABEL RIWAYAT KLAIM --}}
    <h6 class="fw-bold text-slate-dark mb-3"><i class="fas fa-history me-2 text-rose-custom"></i>Riwayat Return & Pemotongan Utang Dagang</h6>
    <div class="table-wrapper-mentari d-none d-lg-block">
        <div class="table-responsive">
            {{-- MENGGUNAKAN TEMA MERAH KHUSUS UTANG/SUPPLIER --}}
            <table class="table table-mentari align-middle mb-0" style="font-size: 0.85rem; width: 100%;">
                <thead>
                    <tr>
                        <th class="ps-4">No. Klaim / Return</th>
                        <th>Nota PO & Supplier</th>
                        <th>Nama Barang</th>
                        <th class="text-center">Qty</th>
                        <th>Potongan Utang (DN)</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returs as $noRetur => $group)
                        @php $rowCount = count($group); @endphp
                        @foreach($group as $index => $retur)
                        <tr>
                            @if($index === 0)
                            <td class="ps-4 fw-bold text-rose-custom align-middle" rowspan="{{ $rowCount }}">{{ $retur->no_retur_beli }}</td>
                            <td class="align-middle" rowspan="{{ $rowCount }}">
                                <span class="badge badge-secondary-soft rounded-pill px-2 py-1 shadow-sm mb-1 d-inline-block">{{ $retur->pembelian->no_pembelian ?? 'N/A' }}</span><br>
                                <span class="small fw-semibold text-slate-dark"><i class="fas fa-building me-1 text-slate-muted"></i> {{ $retur->nama_supplier ?? 'N/A' }}</span>
                            </td>
                            @endif
                            <td class="fw-bold text-slate-dark">
                                {{ $retur->barang->nama_barang ?? 'N/A' }}
                            </td>
                            <td class="text-center fw-bold">{{ $retur->qty_retur }}</td>
                            <td>
                                <span class="text-slate-dark small fw-bold">
                                    <i class="fas fa-file-invoice-dollar me-1 text-rose-custom"></i>Rp {{ number_format($retur->nominal_potongan, 0, ',', '.') }}
                                </span>
                            </td>
                            @if($index === 0)
                            <td class="text-center align-middle" rowspan="{{ $rowCount }}">
                                @if($retur->status_retur === 'pending')
                                    <span class="badge badge-warning-soft px-2 py-1 rounded-pill"><i class="fas fa-clock me-1"></i>Tertunda</span>
                                @else
                                    <span class="badge badge-success-soft px-2 py-1 rounded-pill"><i class="fas fa-check-circle me-1"></i>Selesai</span>
                                @endif
                            </td>
                            <td class="text-center pe-4 align-middle" rowspan="{{ $rowCount }}">
                                @if($retur->status_retur === 'pending')
                                    <form action="{{ route('retur.pembelian.eksekusi', $retur->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning fw-bold text-dark shadow-sm rounded-pill" onclick="return confirm('Eksekusi Return ini? Sistem akan memotong Stok Karantina/Rusak dan menagihkan Debit Note secara otomatis ke tagihan Supplier.')">
                                            <i class="fas fa-bolt me-1"></i> Return Sekarang
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-light border fw-bold text-secondary shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalDetailRetur{{ $retur->id }}">
                                        <i class="fas fa-eye me-1"></i> Lihat Detail
                                    </button>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-slate-muted bg-white">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-truck-loading d-block fa-3x mb-3 text-rose-custom opacity-25"></i>
                                <span class="fw-bold text-slate-dark mb-1">Belum Ada Data Return Supplier</span>
                                <span class="small">Data barang yang dikembalikan ke supplier akan tercatat di sini.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MOBILE CARDS --}}
    <div class="d-lg-none p-2" id="retur-beli-mobile-list">
        @forelse($returs as $noRetur => $group)
            @php 
                $retur = $group->first(); 
                $totalQty = $group->sum('qty_retur');
                $totalNominal = $group->sum('nominal_potongan');
                $namaBarangArray = $group->pluck('barang.nama_barang')->unique()->toArray();
            @endphp
            <div class="card card-custom mb-3" style="border-left: 4px solid #f43f5e !important;">
                <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #e2e8f0;">
                    <span class="fw-bold text-rose-custom" style="font-size: 0.85rem;"><i class="fas fa-undo me-1"></i> {{ $retur->no_retur_beli }}</span>
                    <span class="badge badge-secondary-soft rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">{{ $retur->pembelian->no_pembelian ?? 'N/A' }}</span>
                </div>
                <div class="card-body p-3">
                    <h6 class="fw-bold text-slate-dark mb-1" style="font-size: 1rem;">
                        {{ implode(', ', $namaBarangArray) }}
                        @if(count($group) > 1)
                            <br><span class="badge badge-info-soft rounded-pill px-2 py-0 mt-1" style="font-size: 0.65rem;">Multi-Item Group</span>
                        @endif
                    </h6>
                    <div class="text-muted mb-3" style="font-size: 0.8rem;">
                        <i class="fas fa-building me-1"></i> Supplier: <strong>{{ $retur->nama_supplier ?? 'N/A' }}</strong>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 bg-light rounded text-center border">
                                <span class="d-block text-muted" style="font-size: 0.65rem;">Qty Return</span>
                                <span class="fw-bold text-slate-dark" style="font-size: 0.85rem;">{{ $retur->qty_retur }} Pcs</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-rose-subtle rounded text-center border border-rose-subtle">
                                <span class="d-block text-rose-custom" style="font-size: 0.65rem;">Potongan (DN)</span>
                                <span class="fw-bold text-rose-custom" style="font-size: 0.85rem;">Rp {{ number_format($retur->nominal_potongan, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($retur->status_retur === 'pending')
                                <span class="badge badge-warning-soft px-2 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;"><i class="fas fa-clock me-1"></i>Tertunda</span>
                            @else
                                <span class="badge badge-success-soft px-2 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;"><i class="fas fa-check-circle me-1"></i>Selesai</span>
                            @endif
                        </div>
                        <div>
                            @if($retur->status_retur === 'pending')
                                <form action="{{ route('retur.pembelian.eksekusi', $retur->id) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning fw-bold text-dark shadow-sm rounded-pill" style="font-size: 0.75rem;" onclick="return confirm('Eksekusi Return ini? Sistem akan memotong Stok Karantina/Rusak dan menagihkan Debit Note secara otomatis ke tagihan Supplier.')">
                                        <i class="fas fa-bolt me-1"></i> Return Sekarang
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-sm btn-light border fw-bold text-secondary shadow-sm rounded-pill" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#modalDetailRetur{{ $retur->id }}">
                                    <i class="fas fa-eye me-1"></i> Lihat Detail
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card card-custom p-5 text-center text-slate-muted bg-white mb-3">
                <div class="d-flex flex-column align-items-center">
                    <i class="fas fa-undo d-block fa-3x mb-3 text-rose-custom opacity-25"></i>
                    <span class="fw-bold text-slate-dark mb-1">Belum Ada Data Return Supplier</span>
                    <span class="small">Data barang yang dikembalikan ke supplier akan tercatat di sini.</span>
                </div>
            </div>
        @endforelse
    </div>
</div>



{{-- MODAL DETAIL RETUR PEMBELIAN --}}
@foreach($returs as $noRetur => $group)
    @php 
        $retur = $group->first(); 
        $totalQty = $group->sum('qty_retur');
        $totalNominal = $group->sum('nominal_potongan');
        $alasanGabungan = $group->pluck('alasan')->filter()->implode(', ');
    @endphp
<div class="modal fade" id="modalDetailRetur{{ $retur->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header bg-rose-custom text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-info-circle me-2"></i>Detail Return Supplier</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <ul class="list-group list-group-flush rounded-3 shadow-sm">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-slate-muted fw-bold">Tanggal Dibuat</span>
                        <span class="fw-bold text-slate-dark">{{ \Carbon\Carbon::parse($retur->created_at)->format('d M Y H:i') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-slate-muted fw-bold">Jenis Klaim</span>
                        <span>
                            @if($retur->jenis_retur == 'fisik')
                                <span class="badge badge-secondary-soft rounded-pill px-2 py-1"><i class="fas fa-box me-1"></i> Fisik Barang</span>
                            @else
                                <span class="badge badge-info-soft rounded-pill px-2 py-1"><i class="fas fa-tags me-1"></i> Koreksi Harga</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-slate-muted fw-bold">Dampak Stok Utama</span>
                        <span class="text-end">
                            @if($retur->jenis_retur == 'fisik')
                                @if($retur->status_retur === 'pending')
                                    <span class="text-warning small fw-bold"><i class="fas fa-pause-circle me-1"></i>Menunggu Eksekusi</span><br>
                                    <span class="text-slate-muted" style="font-size: 10px;">(Akan memotong Stok: {{ ucfirst($retur->status_kondisi) }})</span>
                                @else
                                    <span class="text-danger small fw-bold"><i class="fas fa-minus-circle me-1"></i>Keluar Gudang (-{{ $totalQty }})</span><br>
                                    <span class="text-slate-muted" style="font-size: 10px;">(Dari Stok: {{ ucfirst($retur->status_kondisi) }})</span>
                                @endif
                            @else
                                <span class="text-slate-muted small fst-italic">Tidak Mempengaruhi Stok</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-slate-muted fw-bold">Dampak Ke Utang</span>
                        <span class="text-end fw-bold text-success">
                            <i class="fas fa-file-invoice-dollar me-1"></i> (DN) Potong Utang Rp {{ number_format($totalNominal, 0, ',', '.') }}
                        </span>
                    </li>
                    <li class="list-group-item py-3">
                        <span class="text-slate-muted fw-bold d-block mb-1">Alasan / Keterangan</span>
                        <p class="mb-0 text-slate-dark">{{ $alasanGabungan ?: '-' }}</p>
                    </li>
                </ul>
            </div>
            <div class="modal-footer bg-white border-0 py-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach


@endsection