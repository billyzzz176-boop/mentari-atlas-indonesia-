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
    
    .stat-card-1 { background: linear-gradient(135deg, #10b981 0%, #047857 100%); box-shadow: 0 15px 25px -5px rgba(16, 185, 129, 0.4); }
    .stat-card-2 { background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%); box-shadow: 0 15px 25px -5px rgba(14, 165, 233, 0.4); }
    .stat-card-3 { background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%); box-shadow: 0 15px 25px -5px rgba(244, 63, 94, 0.4); }
    .stat-card-4 { background: linear-gradient(135deg, #8b5cf6 0%, #5b21b6 100%); box-shadow: 0 15px 25px -5px rgba(139, 92, 246, 0.4); }

    .stat-icon-box { width: 64px; height: 64px; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; flex-shrink: 0; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); background-color: rgba(255, 255, 255, 0.2) !important; color: white !important; backdrop-filter: blur(4px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
    .stat-card p, .stat-card h2, .stat-card i { color: white !important; }

    /* TEMA TABEL PREMIUM MENTARI ATLAS */
    .table-wrapper-mentari { border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; border: 1px solid #e2e8f0; background-color: white; margin-bottom: 1.5rem; }
    .table-mentari { width: 100%; margin-bottom: 0; border-collapse: collapse; }
    .table-mentari thead th { background: #f1f5f9 !important; color: #475569 !important; font-weight: 700 !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1rem !important; border-bottom: 2px solid #e2e8f0 !important; }
    .table-mentari tbody tr:hover { background-color: #f8fafc !important; }
    .table-mentari tbody td { padding: 1rem; color: #334155; vertical-align: middle; border-bottom: 1px solid #e2e8f0; }

    /* Status Badges */
    .badge-success-soft { background-color: #d1fae5 !important; color: #065f46 !important; }
    .badge-warning-soft { background-color: #fef3c7 !important; color: #92400e !important; }
    .badge-danger-soft { background-color: #fee2e2 !important; color: #991b1b !important; }
    .badge-secondary-soft { background-color: #f1f5f9 !important; color: #334155 !important; border: 1px solid #cbd5e1 !important; }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc;">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold">Ruang Kontrol Keuangan</h1>
            <p class="text-slate-muted small mb-0 mt-1">Pantau arus kas, tagihan piutang, dan kewajiban utang perusahaan.</p>
        </div>
        <span class="text-slate-muted fw-medium bg-white px-3 py-1.5 rounded border small shadow-sm d-none d-sm-inline-block">
            <i class="far fa-clock text-emerald-custom me-2"></i>{{ \Carbon\Carbon::now()->format('d F Y, H:i') }}
        </span>
    </div>

    <div class="row g-4 mb-4 align-items-stretch">
    <div class="row g-4 mb-4 align-items-stretch">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-1 p-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1 letter-spacing-wide opacity-75">Total Omzet Valid</p>
                        <h2 class="fw-bolder mb-0" style="font-size: 1.6rem;">Rp {{ number_format($totalOmzet ?? 0, 0, ',', '.') }}</h2>
                        <p class="small fw-bold mt-2 mb-0 opacity-75"><i class="fas fa-chart-line me-1"></i> Keuangan Masuk</p>
                    </div>
                    <div class="stat-icon-box">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-2 p-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1 letter-spacing-wide opacity-75">Piutang Berjalan</p>
                        <h2 class="fw-bolder mb-0" style="font-size: 1.6rem;">Rp {{ number_format($piutangBerjalan ?? 0, 0, ',', '.') }}</h2>
                        <p class="small fw-bold mt-2 mb-0 opacity-75"><i class="fas fa-hand-holding-usd me-1"></i> Tagihan Customer</p>
                    </div>
                    <div class="stat-icon-box">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-3 p-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1 letter-spacing-wide opacity-75">Kewajiban Utang</p>
                        <h2 class="fw-bolder mb-0" style="font-size: 1.6rem;">Rp {{ number_format($kewajibanUtang ?? 0, 0, ',', '.') }}</h2>
                        <p class="small fw-bold mt-2 mb-0 opacity-75"><i class="fas fa-file-invoice-dollar me-1"></i> Tagihan Supplier</p>
                    </div>
                    <div class="stat-icon-box">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-4 p-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1 letter-spacing-wide opacity-75">SO Disetujui (Aktif)</p>
                        <h2 class="fw-bolder mb-0" style="font-size: 1.6rem;">{{ $soDisetujui ?? 0 }}</h2>
                        <p class="small fw-bold mt-2 mb-0 opacity-75"><i class="fas fa-shopping-basket me-1"></i> Pesanan Proses</p>
                    </div>
                    <div class="stat-icon-box">
                        <i class="fas fa-shopping-basket"></i>
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
                        <i class="fas fa-chart-pie text-emerald-custom me-2"></i> Visualisasi Arus Kas (Overview)
                    </h6>
                </div>
                <div class="card-body bg-white" style="position: relative; height: 350px;">
                    <canvas id="keuanganChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden h-100">
                <div class="card-header py-3 bg-white border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-slate-dark d-flex align-items-center">
                        <i class="fas fa-exchange-alt text-emerald-custom me-2"></i> Log Transaksi Terakhir Masuk
                    </h6>
                </div>
                <div class="card-body p-0 bg-white flex-grow-1">
                    <div class="table-wrapper-mentari d-none d-lg-block">
                        <table class="table-mentari">
                            <thead>
                                <tr>
                                    <th>Waktu Input</th>
                                    <th>No Referensi</th>
                                    <th>Entitas</th>
                                    <th>Nilai Rupiah</th>
                                    <th class="text-center">Status Filter</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayat ?? [] as $trx)
                                <tr>
                                    <td class="text-slate-muted small fw-medium">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="fw-bold text-slate-dark">{{ $trx->no_invoice ?? ('#INV-'.$trx->id) }}</td>
                                    <td>
                                        <div class="fw-medium">{{ $trx->penjualan?->customer?->nama_customer ?? 'Umum' }}</div>
                                        <small class="text-muted d-block" style="font-size: 0.72rem;">Sales: {{ $trx->penjualan?->user?->name ?? '-' }}</small>
                                    </td>
                                    <td class="fw-bold text-emerald-custom">Rp {{ number_format($trx->total_tagihan ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge px-3 py-1.5 rounded-pill fw-bold text-uppercase tracking-wider {{ strtolower($trx->status_bayar ?? '') == 'lunas' ? 'badge-success-soft' : 'badge-warning-soft' }}" style="font-size: 0.65rem;">
                                            {{ $trx->status_bayar ?? 'BELUM BAYAR' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-slate-muted bg-white">Belum ada transaksi piutang/sales order yang tercatat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- MOBILE CARDS --}}
                    <div class="d-lg-none p-2 bg-light">
                        @forelse($riwayat ?? [] as $trx)
                        <div class="card card-custom mb-3 border-0 shadow-sm">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="fw-bold text-slate-dark">{{ $trx->no_invoice ?? ('#INV-'.$trx->id) }}</span>
                                    <span class="badge rounded-pill fw-bold text-uppercase {{ strtolower($trx->status_bayar ?? '') == 'lunas' ? 'badge-success-soft' : 'badge-warning-soft' }}" style="font-size: 0.65rem;">
                                        {{ $trx->status_bayar ?? 'BELUM BAYAR' }}
                                    </span>
                                </div>
                                <div class="text-slate-muted small fw-medium mb-2"><i class="far fa-clock me-1"></i> {{ $trx->created_at->format('d/m/Y H:i') }}</div>
                                <div class="mb-2">
                                    <div class="fw-medium text-dark"><i class="fas fa-building me-1 opacity-50"></i> {{ $trx->penjualan?->customer?->nama_customer ?? 'Umum' }}</div>
                                    <small class="text-muted"><i class="fas fa-user-tie me-1 opacity-50"></i> Sales: {{ $trx->penjualan?->user?->name ?? '-' }}</small>
                                </div>
                                <div class="pt-2 border-top">
                                    <span class="text-muted small">Nilai Tagihan:</span>
                                    <div class="fw-bold text-emerald-custom float-end">Rp {{ number_format($trx->total_tagihan ?? 0, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-slate-muted">Belum ada transaksi piutang/sales order yang tercatat.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden h-100 bg-white">
                <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-slate-dark d-flex align-items-center">
                        <i class="fas fa-bolt text-warning me-2"></i> Tindakan Finansial Cepat
                    </h6>
                </div>
                <div class="card-body p-4 d-flex flex-column gap-3">
                    <div class="p-3 rounded-3 border" style="background-color: #f8fafc;">
                        <h6 class="fw-bold text-slate-dark mb-1"><i class="fas fa-file-invoice-dollar text-info me-2"></i>Kelola Piutang Masuk</h6>
                        <p class="small text-slate-muted mb-3">Catat pembayaran dari customer untuk SO yang sudah selesai.</p>
                        <a href="{{ route('keuangan.piutang.index') }}" class="btn btn-sm btn-outline-info w-100 fw-medium">Buka Modul Piutang</a>
                    </div>

                    <div class="p-3 rounded-3 border" style="background-color: #fff1f2; border-color: #fecdd3 !important;">
                        <h6 class="fw-bold text-danger mb-1"><i class="fas fa-money-bill-wave me-2"></i>Bayar Utang Supplier</h6>
                        <p class="small text-slate-muted mb-3">Selesaikan kewajiban tagihan pengadaan stok barang gudang.</p>
                        <a href="{{ route('keuangan.utang.index') }}" class="btn btn-sm btn-outline-danger w-100 fw-medium">Buka Modul Utang</a>
                    </div>

                    <div class="mt-auto pt-3 border-top text-center">
                        <small class="text-slate-muted"><i class="fas fa-shield-alt text-emerald-custom me-1"></i> Data dienkripsi & dipantau secara real-time.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('keuanganChart').getContext('2d');
    
    // Gradien untuk warna
    let gradientOmzet = ctx.createLinearGradient(0, 0, 0, 400);
    gradientOmzet.addColorStop(0, 'rgba(16, 185, 129, 0.8)'); // Emerald
    gradientOmzet.addColorStop(1, 'rgba(16, 185, 129, 0.2)');

    let gradientPiutang = ctx.createLinearGradient(0, 0, 0, 400);
    gradientPiutang.addColorStop(0, 'rgba(14, 165, 233, 0.8)'); // Cyan/Blue
    gradientPiutang.addColorStop(1, 'rgba(14, 165, 233, 0.2)');

    let gradientUtang = ctx.createLinearGradient(0, 0, 0, 400);
    gradientUtang.addColorStop(0, 'rgba(244, 63, 94, 0.8)'); // Rose
    gradientUtang.addColorStop(1, 'rgba(244, 63, 94, 0.2)');

    const keuanganChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Total Omzet', 'Piutang Berjalan', 'Kewajiban Utang'],
            datasets: [{
                label: 'Nilai Rupiah',
                data: [{{ $totalOmzet ?? 0 }}, {{ $piutangBerjalan ?? 0 }}, {{ $kewajibanUtang ?? 0 }}],
                backgroundColor: [gradientOmzet, gradientPiutang, gradientUtang],
                borderColor: ['#10b981', '#0ea5e9', '#f43f5e'],
                borderWidth: 2,
                borderRadius: 8,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 14, family: "'Inter', sans-serif" },
                    bodyFont: { size: 14, family: "'Inter', sans-serif" },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) { label += ': '; }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: {
                        font: { family: "'Inter', sans-serif", size: 11 },
                        callback: function(value) {
                            if(value >= 1000000) return 'Rp ' + (value / 1000000) + ' Juta';
                            return 'Rp ' + value;
                        }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { font: { family: "'Inter', sans-serif", size: 13, weight: 'bold' } }
                }
            },
            animation: {
                y: { duration: 2000, easing: 'easeOutQuart' }
            }
        }
    });
});
</script>
@endsection