@extends('layouts.app')

@section('content')
<style>
    /* CSS Khusus untuk tombol aksi */
    .btn-action-square {
        width: 32px !important;
        height: 32px !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        padding: 0 !important;
        border-radius: 0.375rem;
        flex-shrink: 0 !important;
        background-color: #f0fdfa !important; /* Warna hijau sangat muda khas emerald */
        border: 1px solid #a7f3d0 !important; /* Border hijau lembut */
        transition: all 0.2s ease;
    }
    .btn-action-square:hover {
        background-color: #10b981 !important;
        border-color: #10b981 !important;
    }
    .btn-action-square:hover i {
        color: #ffffff !important;
    }
    .btn-action-square.dropdown-toggle::after {
        display: none;
    }
    .table-mentari-compact th, .table-mentari-compact td {
        padding: 0.75rem 0.5rem !important;
    }
    .text-emerald-custom { color: #10b981 !important; }
    .bg-gradient-emerald {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }
</style>

<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-slate-dark fw-bold">Riwayat & Laporan Sales Order</h1>
        
        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-md-end">
            <form action="{{ route('penjualan.index') }}" method="GET" class="m-0" style="min-width: 300px;">
                <div class="input-group shadow-sm rounded-pill overflow-hidden border bg-white focus-ring-emerald transition-all">
                    <input type="text" name="search" class="form-control border-0 search-input ps-4 pe-4 bg-white" placeholder="Cari No. SO atau Customer..." value="{{ request('search') }}">
                    <button class="btn bg-white border-0 text-emerald-custom px-3" type="submit"><i class="fas fa-search"></i></button>
                    @if(request('search'))
                        <a href="{{ route('penjualan.index') }}" class="btn bg-white border-0 text-danger px-3 border-start" title="Hapus Pencarian"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- KOTAK NOTIFIKASI SUKSES / ERROR --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4 border-start border-success border-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4 border-start border-danger border-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <strong>Gagal!</strong> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4 border-start border-danger border-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- TABEL PREMIUM MENTARI ATLAS --}}
    <div class="table-wrapper-mentari d-none d-lg-block">
        <div class="table-responsive" style="overflow: visible;">
            <table class="table table-mentari table-mentari-compact align-middle mb-0" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th class="ps-3 text-nowrap" style="width: 10%;">No. SO</th>
                        <th style="width: 10%;">Tanggal</th>
                        <th style="max-width: 130px;">Customer</th>
                        <th>Sales</th>
                        <th class="text-end text-nowrap">Total Nilai</th>
                        <th class="text-center">Skor SPK</th>
                        <th class="text-center">Approval</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-3" style="width: 150px; white-space: nowrap;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuan as $so)
                    <tr>
                        <td class="fw-bold text-emerald-custom ps-3 text-nowrap">{{ $so->no_so }}</td>
                        <td>
                            @if($so->sales_created_at)
                                <span class="d-block fw-bold text-dark">{{ \Carbon\Carbon::parse($so->sales_created_at)->format('d/m/y') }}</span>
                                <span class="text-muted" style="font-size: 0.7rem;"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($so->sales_created_at)->format('H:i') }}</span>
                            @else
                                <span class="d-block fw-bold text-dark">{{ date('d/m/y', strtotime($so->tanggal_order)) }}</span>
                            @endif
                        </td>
                        <td class="text-wrap" style="max-width: 130px; line-height: 1.2;">
                            <span class="fw-bold d-block text-dark">{{ $so->customer->nama_customer }}</span>
                        </td>
                        <td><span class="badge bg-white text-dark border shadow-sm">{{ $so->user->name }}</span></td>
                        <td class="text-end text-nowrap">
                            <div class="d-flex justify-content-between fw-bold text-dark">
                                <span class="text-muted fw-normal me-1">Rp</span>
                                <span>{{ number_format($so->total_semua, 0, ',', '.') }}</span>
                            </div>
                        </td>
                        
                        <td class="text-center fw-bold">
                            @php $skor = $so->skor_spk ?? 0; @endphp
                            @if($skor >= 70)
                                <span class="text-success" title="Sangat Layak"><i class="fas fa-shield-check me-1"></i>{{ number_format($skor, 0) }}%</span>
                            @elseif($skor >= 40)
                                <span class="text-warning" title="Kurang Layak"><i class="fas fa-exclamation-circle me-1"></i>{{ number_format($skor, 0) }}%</span>
                            @else
                                <span class="text-danger" title="Beresiko"><i class="fas fa-times-circle me-1"></i>{{ number_format($skor, 0) }}%</span>
                            @endif
                        </td>
                        
                        <td class="text-center align-middle">
                            @php
                                $approvalBadgeClass = match(strtolower($so->status_approval)) {
                                    'disetujui' => 'bg-success',
                                    'ditolak' => 'bg-danger',
                                    'dibatalkan' => 'bg-secondary',
                                    default => 'bg-warning text-dark'
                                };
                            @endphp
                            
                            <div class="position-relative d-inline-block text-center">
                                <span class="badge {{ $approvalBadgeClass }} px-3 py-1 rounded-pill shadow-sm">
                                    {{ strtoupper($so->status_approval) }}
                                </span>

                                @if($so->catatan)
                                    <div class="position-absolute w-100 text-center" style="top: 100%; left: 0; margin-top: 2px;">
                                        <button type="button" class="btn btn-sm btn-link text-decoration-none border-0 p-0 text-warning fw-bold shadow-none btn-lihat-catatan" data-catatan="{{ $so->catatan }}" style="font-size: 0.65rem;">
                                            <i class="fas fa-comment-dots"></i> Catatan
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </td>

                        <td class="text-center">
                            @if($so->status == 'draft')
                                <span class="badge bg-secondary px-2 py-1 shadow-sm"><i class="fas fa-box"></i> Packing</span>
                            @elseif($so->status == 'ready_to_invoice')
                                <span class="badge bg-primary px-2 py-1 shadow-sm"><i class="fas fa-truck"></i> Kirim</span>
                            @elseif($so->status == 'menunggu_restock')
                                <span class="badge bg-danger px-2 py-1 shadow-sm"><i class="fas fa-clock"></i> Back Order</span>
                            @elseif(in_array(strtolower($so->status), ['batal', 'dibatalkan']))
                                <span class="badge bg-danger px-2 py-1 shadow-sm"><i class="fas fa-times-circle"></i> Dibatalkan</span>
                            @else
                                <span class="badge bg-info px-2 py-1 shadow-sm">{{ ucfirst(str_replace('_', ' ', $so->status ?: 'Kosong')) }}</span>
                            @endif
                        </td>

                        <td class="text-center align-middle pe-3">
                            <div class="d-flex flex-nowrap justify-content-center align-items-center gap-1 mx-auto">
                                
                                {{-- Tombol Detail: Selalu muncul --}}
                                <a href="{{ route('penjualan.show', $so->id) }}" class="btn btn-action-square" title="Lihat Detail">
                                    <i class="fas fa-eye text-emerald-custom"></i>
                                </a>

                                {{-- Logika Edit & Hapus: MUNCUL JIKA STATUS PENDING --}}
                                @if($so->status_approval == 'pending' && (Auth::user()->role == 'direktur' || $so->user_id == Auth::id()))
                                    
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('penjualan.edit', $so->id) }}" class="btn btn-action-square" title="Edit Data">
                                        <i class="fas fa-edit text-emerald-custom"></i>
                                    </a>
                                    
                                    {{-- Tombol Batal --}}
                                    <form action="{{ route('penjualan.destroy', $so->id) }}" method="POST" class="m-0" onsubmit="return confirm('Yakin ingin membatalkan SO ini? SO yang dibatalkan tidak dapat dikembalikan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-action-square" title="Batalkan SO">
                                            <i class="fas fa-ban text-emerald-custom"></i>
                                        </button>
                                    </form>

                                @endif

                                {{-- FITUR KONFIRMASI PACKING: Disembunyikan untuk role sales --}}
                                @if(strtolower(Auth::user()->role) != 'sales' && $so->status_approval == 'disetujui' && in_array($so->status, ['diproses', 'menunggu_restock']))
                                    @php
                                        $totalShippable = 0;
                                        foreach($so->details as $detail) {
                                            $stok = $detail->barang->stok_akhir ?? 0;
                                            $totalShippable += max(0, min($detail->jumlah, $stok));
                                        }
                                    @endphp

                                    @if($totalShippable > 0)
                                        <form action="{{ route('penjualan.packingSelesai', $so->id) }}" method="POST" class="m-0" onsubmit="return confirm('Stok tersedia untuk dipacking. Konfirmasi: Potong stok sekarang?')">
                                            @csrf
                                            <button type="submit" class="btn btn-action-square" title="Konfirmasi Packing Selesai">
                                                <i class="fas fa-box-open text-emerald-custom"></i>
                                            </button>
                                        </form>
                                    @else
                                        @if($so->status == 'diproses')
                                            <form action="{{ route('penjualan.sendToBackorder', $so->id) }}" method="POST" class="m-0" onsubmit="return confirm('Stok kosong (0). Pindahkan ke antrean Back Order agar Purchasing bisa restock?')">
                                                @csrf
                                                <button type="submit" class="btn btn-action-square border-danger" title="Stok Kosong! Masukkan ke Back Order">
                                                    <i class="fas fa-box-open text-danger"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> BO</span>
                                        @endif
                                    @endif
                                @endif

                                {{-- Tombol Cetak / Printer: Hilang untuk Sales --}}
                                @if(in_array($so->status, ['ready_to_invoice', 'selesai']) && strtolower(Auth::user()->role) != 'sales')
                                    <div class="d-flex gap-1 justify-content-center m-0">
                                        @if($so->pengirimans->count() <= 1)
                                            <a href="{{ route('penjualan.printSuratJalan', $so->id) }}" target="_blank" class="btn btn-action-square" title="Cetak Surat Jalan">
                                                <i class="fas fa-truck text-emerald-custom"></i>
                                            </a>
                                            <a href="{{ route('penjualan.printFaktur', $so->id) }}" target="_blank" class="btn btn-action-square" title="Cetak Faktur">
                                                <i class="fas fa-file-invoice-dollar text-emerald-custom"></i>
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-action-square" data-bs-toggle="modal" data-bs-target="#modalPrint{{ $so->id }}" title="Pilih Batch Pengiriman untuk Dicetak">
                                                <i class="fas fa-print text-emerald-custom"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted bg-white">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">Belum ada data penjualan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION DESKTOP --}}
        <div class="mt-3 px-3">
            {{ $pengajuan->links('pagination::bootstrap-5') }}
        </div>
    </div>

              {{-- MOBILE CARDS --}}
              <div class="d-lg-none p-3" style="background-color: var(--bg-page);">
                  @forelse($pengajuan as $so)
                  <div class="card mb-3 shadow-sm border-0 overflow-hidden" style="border-radius: 12px; border: 1px solid #e2e8f0 !important; border-left: 4px solid #10b981 !important;">
                      <div class="card-header bg-gradient-emerald text-white py-2 px-3 d-flex justify-content-between align-items-center">
                          <h6 class="m-0 fw-bold" style="font-size: 0.95rem;"><i class="fas fa-file-invoice me-1"></i> {{ $so->no_so }}</h6>
                          <span class="small opacity-75"><i class="far fa-calendar-alt me-1"></i>{{ $so->sales_created_at ? \Carbon\Carbon::parse($so->sales_created_at)->format('d/m/y H:i') : date('d/m/y', strtotime($so->tanggal_order)) }}</span>
                      </div>
                      <div class="card-body p-3">
                          
                          <h6 class="fw-bold text-slate-dark mb-1" style="font-size: 1.05rem;">{{ $so->customer->nama_customer }}</h6>
                          <div class="text-muted mb-2" style="font-size: 0.8rem;">Sales: <strong>{{ $so->user->name }}</strong></div>
                          
                          <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-2" style="border-radius: 12px; border: 1px solid var(--border-panel);">
                              <div><small class="text-muted d-block" style="font-size:0.65rem;">Total Nilai</small><span class="fw-bold text-emerald-custom" style="font-size:0.9rem;">Rp {{ number_format($so->total_semua, 0, ',', '.') }}</span></div>
                              <div class="text-end">
                                  <small class="text-muted d-block" style="font-size:0.65rem;">Skor SPK</small>
                                  @php $skor = $so->skor_spk ?? 0; @endphp
                                  @if($skor >= 70) <span class="text-success fw-bold"><i class="fas fa-shield-check me-1"></i>{{ number_format($skor, 0) }}%</span>
                                  @elseif($skor >= 40) <span class="text-warning fw-bold"><i class="fas fa-exclamation-circle me-1"></i>{{ number_format($skor, 0) }}%</span>
                                  @else <span class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i>{{ number_format($skor, 0) }}%</span> @endif
                              </div>
                          </div>
                          
                          @if($so->catatan)
                          <div class="mb-3 p-2 bg-light rounded" style="border: 1px dashed #f59e0b;">
                              <button type="button" class="btn btn-sm btn-link text-decoration-none border-0 p-0 text-warning fw-bold shadow-none btn-lihat-catatan w-100 text-start" data-catatan="{{ $so->catatan }}" style="font-size: 0.8rem;">
                                  <i class="fas fa-comment-dots me-1"></i> Lihat Catatan Approval
                              </button>
                          </div>
                          @endif
                          
                          <div class="d-flex justify-content-between align-items-center mb-3">
                              <div>
                                  <small class="text-muted d-block mb-1" style="font-size:0.65rem;">Approval</small>
                                  @php
                                      $approvalBadgeClass = match(strtolower($so->status_approval)) {
                                          'disetujui' => 'bg-success',
                                          'ditolak' => 'bg-danger',
                                          default => 'bg-warning text-dark'
                                      };
                                  @endphp
                                  <span class="badge {{ $approvalBadgeClass }} px-2 py-1" style="border-radius: 6px; font-size: 0.7rem;">{{ strtoupper($so->status_approval) }}</span>
                                  @if($so->catatan) <span class="text-warning ms-1" title="Ada Catatan"><i class="fas fa-comment-dots"></i></span> @endif
                              </div>
                              <div class="text-end">
                                  <small class="text-muted d-block mb-1" style="font-size:0.65rem;">Status SO</small>
                                  @if($so->status == 'selesai')
                                      <span class="badge bg-success-subtle text-success px-2 py-1" style="border-radius: 6px; font-size: 0.7rem;"><i class="fas fa-check-circle"></i> Selesai</span>
                                  @elseif($so->status == 'diproses')
                                      <span class="badge bg-info-subtle text-info px-2 py-1" style="border-radius: 6px; font-size: 0.7rem;"><i class="fas fa-spinner fa-spin"></i> Diproses</span>
                                  @elseif($so->status == 'draft')
                                      <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="border-radius: 6px; font-size: 0.7rem;"><i class="fas fa-file-alt"></i> Draft</span>
                                  @elseif($so->status == 'dibatalkan')
                                      <span class="badge bg-danger-subtle text-danger px-2 py-1" style="border-radius: 6px; font-size: 0.7rem;"><i class="fas fa-times-circle"></i> Dibatalkan</span>
                                  @elseif($so->status == 'menunggu_restock')
                                      <span class="badge bg-warning-subtle text-warning px-2 py-1" style="border-radius: 6px; font-size: 0.7rem;"><i class="fas fa-box-open"></i> Menunggu Restock</span>
                                  @elseif($so->status == 'ready_to_invoice')
                                      <span class="badge bg-primary-subtle text-primary px-2 py-1" style="border-radius: 6px; font-size: 0.7rem;"><i class="fas fa-file-invoice-dollar"></i> Siap Faktur</span>
                                  @endif
                              </div>
                          </div>
                          
                          <div class="d-flex flex-wrap gap-2">
                              <a href="{{ route('penjualan.show', $so->id) }}" class="btn btn-sm btn-primary-soft flex-fill fw-bold py-2 text-center" style="border-radius: 10px; text-decoration: none;"><i class="fas fa-eye me-1"></i>Detail</a>
                              
                              @if(strtolower($so->status_approval) == 'pending' && (strtolower(Auth::user()->role) == 'direktur' || $so->user_id == Auth::id()))
                                  <a href="{{ route('penjualan.edit', $so->id) }}" class="btn btn-sm btn-warning-soft flex-fill fw-bold py-2 text-center" style="border-radius: 10px; text-decoration: none;"><i class="fas fa-edit me-1"></i>Edit</a>
                                  <form action="{{ route('penjualan.destroy', $so->id) }}" method="POST" class="m-0 flex-fill d-flex" onsubmit="return confirm('Yakin ingin membatalkan dan menghapus SO ini?')">
                                      @csrf
                                      @method('DELETE')
                                      <button type="submit" class="btn btn-sm btn-danger-soft flex-fill fw-bold py-2 w-100" style="border-radius: 10px;"><i class="fas fa-trash me-1"></i>Batal</button>
                                  </form>
                              @endif
                              
                              @if(strtolower($so->status_approval) == 'disetujui' && in_array(strtolower($so->status), ['diproses', 'menunggu']))
                                  <button type="button" class="btn btn-sm btn-info-soft flex-fill fw-bold py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalUbahStatus{{ $so->id }}"><i class="fas fa-sync-alt me-1"></i>Status</button>
                              @endif

                              {{-- PACKING LOGIC --}}
                              @if(strtolower($so->status_approval) == 'disetujui' && in_array($so->status, ['diproses', 'menunggu_restock']))
                                  @php
                                      $totalShippable = 0;
                                      foreach($so->details as $detail) {
                                          $stok = $detail->barang->stok_akhir ?? 0;
                                          $totalShippable += max(0, min($detail->jumlah, $stok));
                                      }
                                  @endphp

                                  @if($totalShippable > 0)
                                      <form action="{{ route('penjualan.packingSelesai', $so->id) }}" method="POST" class="m-0 w-100 d-flex mt-1" onsubmit="return confirm('Stok tersedia untuk dipacking. Konfirmasi: Potong stok sekarang?')">
                                          @csrf
                                          <button type="submit" class="btn btn-sm btn-emerald-custom flex-fill fw-bold py-2 w-100" style="border-radius: 10px;">
                                              <i class="fas fa-box-open me-1"></i> Konfirmasi Packing Selesai
                                          </button>
                                      </form>
                                  @else
                                      @if($so->status == 'diproses')
                                          <form action="{{ route('penjualan.sendToBackorder', $so->id) }}" method="POST" class="m-0 w-100 d-flex mt-1" onsubmit="return confirm('Stok kosong (0). Pindahkan ke antrean Back Order agar Purchasing bisa restock?')">
                                              @csrf
                                              <button type="submit" class="btn btn-sm btn-outline-danger flex-fill fw-bold py-2 w-100" style="border-radius: 10px;">
                                                  <i class="fas fa-exclamation-triangle me-1"></i> Stok Kosong! Kirim ke BO
                                              </button>
                                          </form>
                                      @else
                                          <div class="w-100 mt-1">
                                              <span class="badge bg-warning text-dark w-100 py-2" style="border-radius: 10px;"><i class="fas fa-clock me-1"></i> Menunggu Back Order</span>
                                          </div>
                                      @endif
                                  @endif
                              @endif
                              
                              @if(in_array(strtolower($so->status), ['ready_to_invoice', 'selesai']) && strtolower(Auth::user()->role) != 'sales')
                                  @if($so->pengirimans->count() <= 1)
                                      <a href="{{ route('penjualan.printSuratJalan', $so->id) }}" target="_blank" class="btn btn-sm btn-success-soft flex-fill fw-bold py-2 text-center" style="border-radius: 10px; text-decoration: none;" title="Cetak Surat Jalan">
                                          <i class="fas fa-truck me-1"></i> Surat Jalan
                                      </a>
                                      <a href="{{ route('penjualan.printFaktur', $so->id) }}" target="_blank" class="btn btn-sm btn-info-soft flex-fill fw-bold py-2 text-center" style="border-radius: 10px; text-decoration: none;" title="Cetak Faktur">
                                          <i class="fas fa-file-invoice-dollar me-1"></i> Faktur
                                      </a>
                                  @else
                                      <button type="button" class="btn btn-sm btn-success-soft flex-fill fw-bold py-2 text-center" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalPrint{{ $so->id }}" title="Cetak Surat Jalan/Faktur">
                                          <i class="fas fa-print me-1"></i> Cetak Dokumen
                                      </button>
                                  @endif
                              @endif
                          </div>
                      </div>
                  </div>
                  @empty
                  <div class="text-center py-5 text-muted bg-white shadow-sm" style="border-radius: 16px;">Belum ada data penjualan.</div>
                  @endforelse

                  {{-- PAGINATION MOBILE --}}
                  <div class="mt-3">
                      {{ $pengajuan->links('pagination::bootstrap-5') }}
                  </div>
              </div>

    @foreach($pengajuan as $so)
        @if(in_array($so->status, ['ready_to_invoice', 'selesai']) && $so->pengirimans->count() > 1)
            <!-- Modal Pilih Pengiriman -->
            <div class="modal fade" id="modalPrint{{ $so->id }}" tabindex="-1" aria-labelledby="modalPrintLabel{{ $so->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow-lg">
                        <div class="modal-header bg-success text-white rounded-top-4 py-3">
                            <h5 class="modal-title fw-bold" id="modalPrintLabel{{ $so->id }}"><i class="fas fa-print me-2"></i> Pilih Pengiriman (Batch)</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 text-start text-dark">
                            <p class="text-muted small mb-3">Pilih pengiriman dari <strong>{{ $so->no_so }}</strong> yang ingin Anda cetak:</p>
                            <div class="d-flex flex-column gap-3">
                                @foreach($so->pengirimans as $indexP => $p)
                                    <div class="p-3 border rounded-3 bg-light hover-shadow transition">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="d-block fw-bold text-slate-dark">Pengiriman Ke-{{ $indexP + 1 }}</span>
                                                <span class="d-block text-muted small">SJ: {{ $p->no_pengiriman }}</span>
                                                <span class="d-block text-muted small">INV: {{ $p->no_invoice }}</span>
                                            </div>
                                            <span class="badge bg-emerald text-white rounded-pill px-2 py-1 small">{{ \Carbon\Carbon::parse($p->tanggal_kirim)->format('d M Y') }}</span>
                                        </div>
                                        <div class="d-flex gap-2 mt-2">
                                            <a href="{{ route('penjualan.printSuratJalanPengiriman', $p->id) }}" target="_blank" class="btn btn-sm btn-success flex-fill rounded-pill fw-bold text-white text-center" style="text-decoration: none;">
                                                <i class="fas fa-truck me-1"></i> Surat Jalan
                                            </a>
                                            <a href="{{ route('penjualan.printFakturPengiriman', $p->id) }}" target="_blank" class="btn btn-sm btn-info text-white flex-fill rounded-pill fw-bold text-center" style="text-decoration: none;">
                                                <i class="fas fa-file-invoice-dollar me-1"></i> Faktur
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0 pb-4">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnCatatans = document.querySelectorAll('.btn-lihat-catatan');
        
        btnCatatans.forEach(btn => {
            btn.addEventListener('click', function() {
                const isiCatatan = this.getAttribute('data-catatan');
                
                Swal.fire({
                    title: '<span style="font-size: 1.25rem;">Catatan Approval</span>',
                    html: `<div class="p-3 bg-light rounded text-start text-dark border shadow-sm" style="font-size: 0.95rem;">${isiCatatan}</div>`,
                    icon: 'info',
                    confirmButtonColor: '#10b981',
                    confirmButtonText: '<i class="fas fa-check me-1"></i> Tutup',
                    customClass: {
                        popup: 'rounded-4'
                    }
                });
            });
        });
    });
</script>
@endsection



