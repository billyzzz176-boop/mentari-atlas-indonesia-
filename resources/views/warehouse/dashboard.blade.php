@extends('layouts.app')

@section('content')
<style>
    /* Mengikuti Tema Emerald Premium Mentari Atlas */
    body { background-color: #f8fafc !important; }
    .text-emerald-custom { color: #10b981 !important; }
    .bg-emerald-custom { background-color: #10b981 !important; color: white; }
    .text-slate-dark { color: #0f172a !important; }
    .text-slate-muted { color: #64748b !important; }
    
    /* Premium Gradient Stat Cards */
    .stat-card { border: none; border-radius: 1.25rem; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden; height: 100%; display: flex; flex-direction: column; justify-content: center; position: relative; z-index: 1; color: white; }
    .stat-card::after { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 60%); transform: rotate(30deg); z-index: -1; pointer-events: none; }
    .stat-card:hover { transform: translateY(-8px) scale(1.02); }
    .stat-card:hover .stat-icon-box { transform: scale(1.1) rotate(10deg); background-color: rgba(255,255,255,0.3) !important; color: white !important; }
    
    .stat-card-1 { background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%); box-shadow: 0 15px 25px -5px rgba(245, 158, 11, 0.4); }
    .stat-card-2 { background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%); box-shadow: 0 15px 25px -5px rgba(14, 165, 233, 0.4); }
    .stat-card-3 { background: linear-gradient(135deg, #10b981 0%, #047857 100%); box-shadow: 0 15px 25px -5px rgba(16, 185, 129, 0.4); }
    .stat-card-4 { background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%); box-shadow: 0 15px 25px -5px rgba(244, 63, 94, 0.4); }

    .stat-icon-box { width: 64px; height: 64px; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; flex-shrink: 0; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); background-color: rgba(255, 255, 255, 0.2) !important; color: white !important; backdrop-filter: blur(4px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
    .stat-card p, .stat-card h2, .stat-card i { color: white !important; }

    /* TEMA TABEL PREMIUM MENTARI ATLAS */
    .table-wrapper-mentari { border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; border: 1px solid #e2e8f0; background-color: white; margin-bottom: 1.5rem; }
    .table-mentari { width: 100%; margin-bottom: 0; border-collapse: collapse; }
    .table-mentari thead th { background: #f1f5f9 !important; color: #475569 !important; font-weight: 700 !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1rem !important; border-bottom: 2px solid #e2e8f0 !important; }
    .table-mentari tbody tr:hover { background-color: #f8fafc !important; }
    .table-mentari tbody td { padding: 1rem; color: #334155; vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc;">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold">Ruang Kontrol Warehouse</h1>
            <p class="text-slate-muted small mb-0 mt-1">Pantau antrean packing, status backorder, dan pergerakan stok inventaris.</p>
        </div>
        <span class="text-slate-muted fw-medium bg-white px-3 py-1.5 rounded border small shadow-sm d-none d-sm-inline-block">
            <i class="far fa-clock text-emerald-custom me-2"></i>{{ \Carbon\Carbon::now()->format('d F Y, H:i') }}
        </span>
    </div>

    <div class="row g-4 mb-4 align-items-stretch">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-1 p-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1 letter-spacing-wide opacity-75">Antrean Packing</p>
                        <h2 class="fw-bolder mb-0" style="font-size: 1.6rem;">{{ $pesananMenungguPacking ?? 0 }}</h2>
                        <p class="small fw-bold mt-2 mb-0 opacity-75"><i class="fas fa-boxes me-1"></i> Sales Order</p>
                    </div>
                    <div class="stat-icon-box">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-2 p-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1 letter-spacing-wide opacity-75">Antrean Back Order</p>
                        <h2 class="fw-bolder mb-0" style="font-size: 1.6rem;">{{ $antreanBackOrder ?? 0 }}</h2>
                        <p class="small fw-bold mt-2 mb-0 opacity-75"><i class="fas fa-truck-loading me-1"></i> Item Tertunda</p>
                    </div>
                    <div class="stat-icon-box">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-3 p-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1 letter-spacing-wide opacity-75">Total Item Inventaris</p>
                        <h2 class="fw-bolder mb-0" style="font-size: 1.6rem;">{{ number_format($totalProduk ?? 0, 0, ',', '.') }}</h2>
                        <p class="small fw-bold mt-2 mb-0 opacity-75"><i class="fas fa-clipboard-list me-1"></i> Master Data</p>
                    </div>
                    <div class="stat-icon-box">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-4 p-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1 letter-spacing-wide opacity-75">Stok Kritis</p>
                        <h2 class="fw-bolder mb-0" style="font-size: 1.6rem;">{{ $stokKritis ?? 0 }}</h2>
                        <p class="small fw-bold mt-2 mb-0 opacity-75"><i class="fas fa-exclamation-triangle me-1"></i> Peringatan Sistem</p>
                    </div>
                    <div class="stat-icon-box">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- CHART.JS AREA -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header py-3 bg-white border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-slate-dark d-flex align-items-center">
                        <i class="fas fa-chart-doughnut text-warning me-2"></i> Proporsi Aktivitas Gudang Terkini
                    </h6>
                </div>
                <div class="card-body bg-white d-flex justify-content-center align-items-center" style="position: relative; height: 350px;">
                    <canvas id="gudangChart" style="max-height: 100%;"></canvas>
                </div>
            </div>
        </div>

        {{-- KIRI: TABEL PRIORITAS PACKING --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden h-100">
                <div class="card-header py-3 bg-white border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-slate-dark d-flex align-items-center">
                        <i class="fas fa-clipboard-check text-emerald-custom me-2"></i> Prioritas Packing Hari Ini
                    </h6>
                    <a href="{{ route('penjualan.index') }}" class="btn btn-sm btn-light text-slate-muted border rounded-pill px-3 py-1" style="font-size: 0.75rem;">Lihat Semua</a>
                </div>
                <div class="card-body p-0 bg-white flex-grow-1">
                    <div class="table-wrapper-mentari d-none d-lg-block">
                        <table class="table-mentari">
                            <thead>
                                <tr>
                                    <th>Waktu Masuk</th>
                                    <th>No Referensi</th>
                                    <th>Pelanggan</th>
                                    <th class="text-center">Status Filter</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tabelPacking as $tp)
                                <tr>
                                    <td class="text-slate-muted small fw-medium">{{ $tp->updated_at->diffForHumans() }}</td>
                                    <td class="fw-bold text-slate-dark">{{ $tp->no_so }}</td>
                                    <td>
                                        <div class="fw-medium">{{ $tp->customer ? $tp->customer->nama_customer : '-' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge px-3 py-1.5 rounded-pill fw-bold text-uppercase tracking-wider" style="font-size: 0.65rem; background-color: #fef3c7; color: #92400e;">
                                            MENUNGGU
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-slate-muted bg-white">
                                        Belum ada antrean packing untuk hari ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- MOBILE CARDS --}}
                    <div class="d-lg-none p-2 bg-light">
                        @forelse($tabelPacking as $tp)
                        <div class="card card-custom mb-3 border-0 shadow-sm">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="fw-bold text-slate-dark">{{ $tp->no_so }}</span>
                                    <span class="badge rounded-pill fw-bold text-uppercase" style="font-size: 0.65rem; background-color: #fef3c7; color: #92400e;">
                                        MENUNGGU
                                    </span>
                                </div>
                                <div class="text-slate-muted small fw-medium mb-2"><i class="far fa-clock me-1"></i> {{ $tp->updated_at->diffForHumans() }}</div>
                                <div class="mb-2">
                                    <div class="fw-medium text-dark"><i class="fas fa-user-tie me-1 opacity-50"></i> {{ $tp->customer ? $tp->customer->nama_customer : '-' }}</div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-slate-muted">Belum ada antrean packing untuk hari ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- KANAN: TINDAKAN OPERASIONAL CEPAT --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden h-100 bg-white">
                <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-slate-dark d-flex align-items-center">
                        <i class="fas fa-bolt text-warning-custom me-2"></i> Tindakan Operasional Cepat
                    </h6>
                </div>
                <div class="card-body p-4 d-flex flex-column gap-3">
                    
                    <div class="p-3 rounded-3 border" style="background-color: #f8fafc;">
                        <h6 class="fw-bold text-slate-dark mb-1"><i class="fas fa-box text-emerald-custom me-2"></i>Kelola Packing</h6>
                        <p class="small text-slate-muted mb-3">Siapkan dan kemas barang untuk pesanan yang sudah disetujui Direktur.</p>
                        <a href="{{ route('penjualan.index') }}" class="btn btn-sm btn-outline-success w-100 fw-medium">Buka Modul Packing</a>
                    </div>

                    <div class="p-3 rounded-3 border" style="background-color: #f0f9ff; border-color: #bae6fd !important;">
                        <h6 class="fw-bold text-cyan-custom mb-1"><i class="fas fa-dolly me-2"></i>Proses Back Order</h6>
                        <p class="small text-slate-muted mb-3">Penuhi kekurangan barang pesanan dari stok yang baru direstock.</p>
                        <a href="{{ url('/backorder') }}" class="btn btn-sm btn-outline-info w-100 fw-medium">Buka Modul Backorder</a>
                    </div>

                    <div class="p-3 rounded-3 border" style="background-color: #fff1f2; border-color: #fecdd3 !important;">
                        <h6 class="fw-bold text-rose-custom mb-1"><i class="fas fa-exclamation-circle me-2"></i>Barang Stok Kritis</h6>
                        <p class="small text-slate-muted mb-3">Terdapat <b>{{ $stokKritis }} item</b> menipis. Laporkan untuk pengadaan.</p>
                        <a href="{{ url('/barang') }}" class="btn btn-sm btn-outline-danger w-100 fw-medium">Cek Master Barang</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('gudangChart').getContext('2d');
    
    // Gradien untuk warna Doughnut
    let gradientPacking = ctx.createLinearGradient(0, 0, 0, 400);
    gradientPacking.addColorStop(0, '#f59e0b'); // Warning/Orange
    gradientPacking.addColorStop(1, '#d97706');

    let gradientBackorder = ctx.createLinearGradient(0, 0, 0, 400);
    gradientBackorder.addColorStop(0, '#0ea5e9'); // Cyan
    gradientBackorder.addColorStop(1, '#0284c7');

    let gradientKritis = ctx.createLinearGradient(0, 0, 0, 400);
    gradientKritis.addColorStop(0, '#f43f5e'); // Rose
    gradientKritis.addColorStop(1, '#e11d48');

    const gudangChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Antrean Packing', 'Item Backorder', 'Stok Kritis'],
            datasets: [{
                data: [{{ $pesananMenungguPacking ?? 0 }}, {{ $antreanBackOrder ?? 0 }}, {{ $stokKritis ?? 0 }}],
                backgroundColor: [gradientPacking, gradientBackorder, gradientKritis],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        font: { family: "'Inter', sans-serif", size: 13, weight: '500' },
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 14, family: "'Inter', sans-serif" },
                    bodyFont: { size: 14, family: "'Inter', sans-serif" },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) { label += ': '; }
                            if (context.parsed !== null) {
                                label += context.parsed + ' Item';
                            }
                            return label;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 2000,
                easing: 'easeOutQuart'
            }
        }
    });
});
</script>
@endsection