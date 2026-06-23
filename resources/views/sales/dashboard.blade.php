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
    
    .stat-card-1 { background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%); box-shadow: 0 15px 25px -5px rgba(14, 165, 233, 0.4); }
    .stat-card-2 { background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%); box-shadow: 0 15px 25px -5px rgba(245, 158, 11, 0.4); }
    .stat-card-3 { background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%); box-shadow: 0 15px 25px -5px rgba(244, 63, 94, 0.4); }
    .stat-card-4 { background: linear-gradient(135deg, #10b981 0%, #047857 100%); box-shadow: 0 15px 25px -5px rgba(16, 185, 129, 0.4); }

    .stat-icon-box { width: 64px; height: 64px; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; flex-shrink: 0; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); background-color: rgba(255, 255, 255, 0.2) !important; color: white !important; backdrop-filter: blur(4px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
    .stat-card p, .stat-card h2, .stat-card i { color: white !important; }
    
    .chart-panel { background: white; border: none; border-radius: 1.25rem; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); padding: 1.5rem; }

    .btn-gacor {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        border-radius: 50rem;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        transition: all 0.3s ease;
    }
    .btn-gacor:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
        color: white;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header Area -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold">Ruang Kerja Sales</h1>
            <p class="text-slate-muted small mb-0 mt-1">Pantau performa target penjualan dan kelola order Anda hari ini.</p>
        </div>
        <div>
            <a href="{{ route('penjualan.create') }}" class="btn btn-gacor">
                <i class="fas fa-plus-circle me-2"></i> Buat Order Baru
            </a>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="row g-4 mb-4 align-items-stretch">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-1 p-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1 letter-spacing-wide opacity-75">Total Order Saya</p>
                        <h2 class="fw-bolder mb-0" style="font-size: 1.6rem;">{{ $totalSO }}</h2>
                        <p class="small fw-bold mt-2 mb-0 opacity-75"><i class="fas fa-shopping-cart me-1"></i> Keseluruhan</p>
                    </div>
                    <div class="stat-icon-box">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-2 p-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1 letter-spacing-wide opacity-75">Sedang Diproses</p>
                        <h2 class="fw-bolder mb-0" style="font-size: 1.6rem;">{{ $menungguApproval }}</h2>
                        <p class="small fw-bold mt-2 mb-0 opacity-75"><i class="fas fa-clock me-1"></i> Menunggu Approval</p>
                    </div>
                    <div class="stat-icon-box">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-3 p-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1 letter-spacing-wide opacity-75">Order Ditolak</p>
                        <h2 class="fw-bolder mb-0" style="font-size: 1.6rem;">{{ $statusData[2] ?? 0 }}</h2>
                        <p class="small fw-bold mt-2 mb-0 opacity-75"><i class="fas fa-times-circle me-1"></i> Perlu Direvisi</p>
                    </div>
                    <div class="stat-icon-box">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-4 p-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1 letter-spacing-wide opacity-75">Omzet Saya</p>
                        <h2 class="fw-bolder mb-0" style="font-size: 1.4rem;">Rp {{ number_format($omzetBulanIni, 0, ',', '.') }}</h2>
                        <p class="small fw-bold mt-2 mb-0 opacity-75"><i class="fas fa-calendar-check me-1"></i> Bulan Ini</p>
                    </div>
                    <div class="stat-icon-box">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Tables -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="chart-panel h-100">
                <h6 class="fw-bold text-slate-dark mb-4">Tren Penjualan Pribadi (6 Bulan Terakhir)</h6>
                <div id="salesChart" style="min-height: 300px;"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-panel h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold text-slate-dark mb-0">SO Terkini Saya</h6>
                    <a href="{{ route('penjualan.index') }}" class="small text-emerald-custom text-decoration-none fw-bold">Lihat Semua</a>
                </div>
                
                @forelse($recentSO as $so)
                    <div class="d-flex align-items-center mb-3 p-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0; transition: transform 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                        <div class="me-3">
                            <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 40px; height: 40px; background-color: #d1fae5; color: #059669;">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-slate-dark small">{{ $so->no_so }}</span>
                                @if($so->status_approval == 'pending')
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning" style="font-size: 0.65rem;">Pending</span>
                                @elseif($so->status_approval == 'disetujui')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success" style="font-size: 0.65rem;">Disetujui</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger" style="font-size: 0.65rem;">Ditolak</span>
                                @endif
                            </div>
                            <div class="text-slate-muted small">{{ $so->customer ? $so->customer->nama_customer : '-' }}</div>
                            
                        </div>
                    </div>
                @empty
                    <div class="text-center p-4">
                        <div class="text-slate-muted mb-2"><i class="fas fa-box-open fa-2x opacity-50"></i></div>
                        <span class="small text-slate-muted">Belum ada SO terbaru.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var salesOptions = {
            series: [
                { name: 'Omzet', type: 'area', data: @json($omzetData) },
                { name: 'Total Pesanan (SO)', type: 'line', data: @json($salesData) }
            ],
            chart: { height: 320, type: 'line', fontFamily: 'inherit', toolbar: { show: false }, zoom: { enabled: false } },
            colors: ['#10b981', '#0ea5e9'], 
            fill: { 
                type: ['gradient', 'solid'], 
                gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] } 
            },
            dataLabels: { enabled: false }, 
            stroke: { curve: 'smooth', width: [3, 4] },
            xaxis: { categories: @json($salesBulan), axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: [
                {
                    title: { text: 'Omzet (Rp)' },
                    labels: {
                        formatter: function (value) {
                            if(value >= 1000000) return (value / 1000000).toFixed(1) + " Jt";
                            if(value >= 1000) return (value / 1000).toFixed(1) + " Rb";
                            return value;
                        }
                    }
                },
                {
                    opposite: true,
                    title: { text: 'Jumlah Pesanan' }
                }
            ],
            tooltip: { shared: true, intersect: false, y: { formatter: function (y) { if (typeof y !== "undefined") { return y.toLocaleString('id-ID'); } return y; } } },
            legend: { position: 'top', horizontalAlign: 'right' }
        };
        new ApexCharts(document.querySelector("#salesChart"), salesOptions).render();
    });
</script>
@endsection
