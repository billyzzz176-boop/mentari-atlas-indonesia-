@extends('layouts.app')

@section('content')
<style>
    body { background-color: #f8fafc !important; }
    .text-slate-dark { color: #0f172a !important; }
    .text-slate-muted { color: #64748b !important; }
    .card-custom { border: 1px solid #e2e8f0; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    
    /* Styling Nav Tabs Premium */
    .nav-tabs-custom { 
        border-bottom: 2px solid #e2e8f0; 
        gap: 0.5rem; 
        flex-wrap: nowrap; 
        justify-content: center;
        overflow-x: auto; 
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none; /* Firefox */
    }
    .nav-tabs-custom::-webkit-scrollbar {
        display: none; /* Safari and Chrome */
    }
    .nav-tabs-custom .nav-link { 
        border: none; color: #64748b; font-weight: 600; padding: 0.75rem 1.25rem; 
        border-radius: 0.5rem 0.5rem 0 0; transition: all 0.2s; position: relative;
        white-space: nowrap; font-size: 0.9rem;
    }
    .nav-tabs-custom .nav-link:hover { color: #10b981; background-color: #f1f5f9; }
    .nav-tabs-custom .nav-link.active { 
        color: #10b981; background-color: transparent; border: none;
    }
    .nav-tabs-custom .nav-link.active::after {
        content: ''; position: absolute; bottom: -2px; left: 0; width: 100%; 
        height: 3px; background-color: #10b981; border-radius: 3px 3px 0 0;
    }
    
    /* Tabel Laba */
    .table-laba th { background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; color: #ffffff !important; font-weight: 600 !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: none !important; white-space: nowrap; }
    .table-laba td { padding: 1rem 0.75rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; }
    .table-laba tbody tr:hover { background-color: #f8fafc !important; }
    .text-emerald { color: #10b981 !important; }
    
    /* Premium Gradient Stat Cards (Like Dashboard) */
    .stat-card { border: none; border-radius: 1.25rem; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden; height: 100%; display: flex; flex-direction: column; justify-content: center; position: relative; z-index: 1; color: white; padding: 1.5rem; }
    .stat-card::after { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 60%); transform: rotate(30deg); z-index: -1; pointer-events: none; }
    .stat-card:hover { transform: translateY(-8px) scale(1.02); }
    .stat-card:hover .stat-icon-box { transform: scale(1.1) rotate(10deg); background-color: rgba(255,255,255,0.3) !important; color: white !important; }
    
    .stat-card-1 { background: linear-gradient(135deg, #10b981 0%, #047857 100%); box-shadow: 0 15px 25px -5px rgba(16, 185, 129, 0.4); }
    .stat-card-2 { background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%); box-shadow: 0 15px 25px -5px rgba(245, 158, 11, 0.4); }
    .stat-card-3 { background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%); box-shadow: 0 15px 25px -5px rgba(14, 165, 233, 0.4); }
    .stat-card-4 { background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%); box-shadow: 0 15px 25px -5px rgba(244, 63, 94, 0.4); }

    .stat-icon-box { width: 56px; height: 56px; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); background-color: rgba(255, 255, 255, 0.2) !important; color: white !important; backdrop-filter: blur(4px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
    .stat-card p, .stat-card h3, .stat-card span, .stat-card small, .stat-card i { color: white !important; }

    /* Filter Form */
    .filter-group { background: white; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }

    /* ============================================================ */
    /* MODE CETAK KERTAS (PRINT MEDIA QUERY) */
    /* ============================================================ */
    @media print {
        /* 1. Sembunyikan elemen yang tidak perlu di kertas */
        .sidebar, .navbar, .btn, form, header, footer, .nav-tabs-custom, .filter-group {
            display: none !important;
        }
        
        /* 2. Bersihkan background & margin agar pas di kertas A4 */
        body { background-color: white !important; margin: 0; padding: 0; -webkit-print-color-adjust: exact; }
        .container-fluid { padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        .card-custom { border: none !important; box-shadow: none !important; }
        
        /* Box statistik penyesuaian untuk print (4 Kolom) */
        .row.g-3 { display: flex !important; flex-wrap: nowrap !important; margin-bottom: 2rem !important; }
        .col-lg-3 { width: 25% !important; flex: 0 0 25% !important; max-width: 25% !important; padding: 0 8px !important; }
        
        /* Gacor Print Cards */
        .stat-box { border: 2px solid #e2e8f0 !important; border-radius: 12px !important; padding: 1rem !important; box-shadow: none !important; background-color: #f8fafc !important; page-break-inside: avoid; }
        .border-left-emerald { border-left: 6px solid #10b981 !important; }
        .border-left-warning { border-left: 6px solid #f59e0b !important; }
        .border-left-cyan { border-left: 6px solid #06b6d4 !important; }
        .bg-gradient-emerald { background-color: #d1fae5 !important; border: 2px solid #10b981 !important; border-left: 6px solid #10b981 !important; }
        .bg-gradient-emerald h3.fw-bolder, .bg-gradient-emerald span, .bg-gradient-emerald i { color: #047857 !important; }
        
        h3.fw-bolder { font-size: 1.25rem !important; margin-top: 5px !important; color: #0f172a !important; }
        .text-white { color: #0f172a !important; }
        
        /* 3. PAKSA SEMUA TAB UNTUK TAMPIL BERSAMAAN DI KERTAS */
        .tab-content > .tab-pane {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            page-break-inside: avoid;
            margin-bottom: 2rem !important;
        }

        /* Styling Tabel Print Gacor */
        .table-laba { border-collapse: collapse !important; width: 100% !important; margin-bottom: 1.5rem !important; }
        .table-laba th, .table-laba td { border: 1px solid #cbd5e1 !important; padding: 8px 12px !important; color: #0f172a !important; font-size: 0.85rem !important; }
        .table-laba th { background-color: #f1f5f9 !important; font-weight: bold !important; text-transform: uppercase; border-bottom: 2px solid #94a3b8 !important; }
        .table-laba tbody tr:nth-child(even) { background-color: #f8fafc !important; }

        /* 4. Beri Judul otomatis di atas tiap tabel saat dicetak */
        #transaksi::before { content: "LAPORAN LABA: PER TRANSAKSI"; font-weight: 800; font-size: 1.15rem; color: #0f172a; display: block; margin-bottom: 12px; border-bottom: 3px solid #10b981; padding-bottom: 5px; }
        #sales::before { content: "LAPORAN LABA: PER SALES"; font-weight: 800; font-size: 1.15rem; color: #0f172a; display: block; margin-bottom: 12px; border-bottom: 3px solid #10b981; padding-bottom: 5px; }
        #produk::before { content: "LAPORAN LABA: PER PRODUK"; font-weight: 800; font-size: 1.15rem; color: #0f172a; display: block; margin-bottom: 12px; border-bottom: 3px solid #10b981; padding-bottom: 5px; }
        #merek::before { content: "LAPORAN LABA: PER MEREK"; font-weight: 800; font-size: 1.15rem; color: #0f172a; display: block; margin-bottom: 12px; border-bottom: 3px solid #10b981; padding-bottom: 5px; }
        #customer::before { content: "LAPORAN LABA: PER CUSTOMER"; font-weight: 800; font-size: 1.15rem; color: #0f172a; display: block; margin-bottom: 12px; border-bottom: 3px solid #10b981; padding-bottom: 5px; }
        
        /* Sembunyikan div filter date agar rapi di print */
        #customDateRange { display: none !important; }
    }
            /* Fix untuk Nav Tabs Mobile menampilkan 3 judul sekaligus */
      @media (max-width: 991.98px) {
          .nav-tabs-custom {
              justify-content: flex-start;
              scroll-snap-type: x mandatory;
              gap: 0;
          }
          .nav-tabs-custom .nav-item {
              flex: 0 0 33.3333%;
              scroll-snap-align: start;
          }
          .nav-tabs-custom .nav-link {
              width: 100%;
              padding: 0.75rem 0.25rem;
              font-size: 0.8rem;
              text-align: center;
              white-space: nowrap;
              overflow: hidden;
              text-overflow: ellipsis;
              border-radius: 0;
          }
          .nav-tabs-custom .nav-link i {
              display: none; /* Sembunyikan icon supaya teks muat */
          }
      }
      /* Fix untuk Nav Tabs Mobile menampilkan 3 judul sekaligus */
      @media (max-width: 991.98px) {
          .nav-tabs-custom {
              justify-content: flex-start;
              scroll-snap-type: x mandatory;
              gap: 0;
          }
          .nav-tabs-custom .nav-item {
              flex: 0 0 33.3333%;
              scroll-snap-align: start;
          }
          .nav-tabs-custom .nav-link {
              width: 100%;
              padding: 0.75rem 0.25rem;
              font-size: 0.8rem;
              text-align: center;
              white-space: nowrap;
              overflow: hidden;
              text-overflow: ellipsis;
              border-radius: 0;
          }
          .nav-tabs-custom .nav-link i {
              display: none; /* Sembunyikan icon supaya teks muat */
          }
      }
      /* ============================================================ */
      /* MOBILE CARD LAYOUT (< lg breakpoint) */
      /* ============================================================ */
      .card-custom { background: #fff; border-radius: 1rem; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 1rem; transition: box-shadow 0.2s; }
      .card-custom:hover { box-shadow: 0 6px 16px rgba(16,185,129,0.12); }
      .card-custom .card-header-mobile { background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center; }
      .card-custom .card-header-mobile.pembelian-header { background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%); }
      .card-custom .card-header-mobile.supplier-header { background: linear-gradient(135deg, #64748b 0%, #334155 100%); }
      .card-custom .card-body-mobile { padding: 0.85rem 1rem; }
      .card-custom .data-row { display: flex; justify-content: space-between; align-items: center; padding: 0.4rem 0; border-bottom: 1px solid #f1f5f9; font-size: 0.82rem; }
      .card-custom .data-row:last-child { border-bottom: none; }
      .card-custom .data-label { color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.3px; }
      .card-custom .data-value { color: #0f172a; font-weight: 500; text-align: right; max-width: 60%; }
      .card-custom .laba-positif { color: #10b981 !important; font-weight: 700; }
      .card-custom .laba-negatif { color: #ef4444 !important; font-weight: 700; }
      .card-custom .pengeluaran-val { color: #f59e0b !important; font-weight: 700; }
      .card-custom .card-action-mobile { padding: 0.65rem 1rem; background: #f8fafc; border-top: 1px solid #e2e8f0; text-align: center; }
</style>

<div class="container-fluid py-4">
    
    {{-- HEADER KHUSUS CETAK (Hanya tampil saat di-print) --}}
    <div class="d-none d-print-block mb-4 pb-3" style="border-bottom: 2px solid #e2e8f0;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bolder mb-1 text-uppercase" style="color: #0f172a !important; letter-spacing: 1px;">Laporan Profitabilitas</h2>
                <p class="mb-0 text-muted fw-bold" style="font-size: 0.95rem;">PT. Mentari Atlas - Sistem Sales Order</p>
            </div>
            <div class="text-end">
                <p class="mb-1 text-slate-dark fw-bold" style="font-size: 0.85rem;">
                    Periode: 
                    @if($filter_tipe == 'semua') Semua Waktu
                    @elseif($filter_tipe == 'bulan_ini') Bulan Ini
                    @else Custom Rentang
                    @endif
                </p>
                <p class="mb-0 text-muted" style="font-size: 0.8rem;">Dicetak: {{ date('d M Y H:i') }} WIB</p>
            </div>
        </div>
    </div>

    {{-- HEADER & FORM FILTER (Versi Web) --}}
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold"><i class="fas fa-chart-line text-emerald me-2"></i>Analitik Profitabilitas</h1>
            <p class="text-slate-muted small mb-0 mt-1">Laporan Laba Bersih (Net Profit) dan Status Arus Kas Perusahaan.</p>
        </div>
        
        <div class="d-flex flex-column flex-md-row gap-2 align-items-md-center">
            {{-- FORM PENCARIAN RENTANG WAKTU --}}
            <form action="{{ route('laba.index') }}" method="GET" class="filter-group d-flex flex-column flex-md-row align-items-center gap-2 m-0">
                <select name="filter_tipe" id="filter_tipe" class="form-select form-select-sm border-0 fw-bold text-slate-dark shadow-none" style="background-color: #f8fafc; cursor:pointer;" onchange="toggleCustomDate()">
                    <option value="custom" {{ $filter_tipe == 'custom' ? 'selected' : '' }}>Custom Tanggal</option>
                    <option value="bulan_ini" {{ $filter_tipe == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="tahun_ini" {{ $filter_tipe == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="semua" {{ $filter_tipe == 'semua' ? 'selected' : '' }}>Semua Waktu</option>
                </select>

                <div id="custom_date_wrapper" class="d-flex gap-2 align-items-center" style="display: {{ $filter_tipe == 'custom' ? 'flex' : 'none !important' }};">
                    <input type="date" name="start_date" class="form-control form-control-sm border-0" style="background-color: #f8fafc;" value="{{ $start_date }}">
                    <span class="text-muted small">s/d</span>
                    <input type="date" name="end_date" class="form-control form-control-sm border-0" style="background-color: #f8fafc;" value="{{ $end_date }}">
                </div>

                <button type="submit" class="btn btn-sm btn-emerald text-white fw-bold px-3" style="background-color: #10b981;">Filter</button>
            </form>

            <button class="btn btn-light shadow-sm fw-bold border py-2" onclick="window.print()"><i class="fas fa-print me-2 text-slate-muted"></i>Cetak</button>
        </div>
    </div>

    {{-- KOTAK STATISTIK GRAND TOTAL (4 KOLOM) --}}
    <div class="row g-3 mb-4">
        {{-- Box 1: Omzet --}}
        <div class="col-md-6 col-lg-3">
            <div class="stat-card stat-card-3">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1 letter-spacing-wide opacity-75">Total Omzet Bersih</p>
                        <h3 class="fw-bolder mb-0">Rp {{ number_format($grandTotalPendapatan, 0, ',', '.') }}</h3>
                        <small class="d-block mt-2 opacity-75">Setelah potong retur customer</small>
                    </div>
                    <div class="stat-icon-box">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Box 2: Modal (HPP) --}}
        <div class="col-md-6 col-lg-3">
            <div class="stat-card stat-card-2">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1 letter-spacing-wide opacity-75">Modal (HPP)</p>
                        <h3 class="fw-bolder mb-0">Rp {{ number_format($grandTotalHpp, 0, ',', '.') }}</h3>
                        <small class="d-block mt-2 opacity-75">Harga pokok barang terjual</small>
                    </div>
                    <div class="stat-icon-box">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Box 3: Laba Bersih / Kerugian (Dinamic Color) --}}
        <div class="col-md-6 col-lg-3">
            <div class="stat-card {{ $grandTotalLabaBersih >= 0 ? 'stat-card-1' : 'stat-card-4' }}">
                <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1 letter-spacing-wide opacity-75">
                            {{ $grandTotalLabaBersih >= 0 ? 'Laba Bersih Akhir' : 'Kerugian Perusahaan' }}
                        </p>
                        <h3 class="fw-bolder mb-0" style="letter-spacing: -0.5px;">
                            {{ $grandTotalLabaBersih < 0 ? '-' : '' }} Rp {{ number_format(abs($grandTotalLabaBersih), 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="stat-icon-box">
                        <i class="fas {{ $grandTotalLabaBersih >= 0 ? 'fa-hand-holding-usd' : 'fa-skull-crossbones' }}"></i>
                    </div>
                </div>
                @if($grandTotalLabaBersih >= 0 && $grandTotalPendapatan > 0)
                    <div class="mt-2">
                        <span class="badge bg-white text-success fw-bold px-2 py-1 rounded-pill shadow-sm" style="font-size: 0.7rem; color: #10b981 !important;">
                            Margin: {{ number_format(($grandTotalLabaBersih / $grandTotalPendapatan) * 100, 1) }}%
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Box 4: Piutang vs Utang (Status Cash Flow) --}}
        <div class="col-md-6 col-lg-3">
            <div class="stat-card stat-card-4" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); box-shadow: 0 15px 25px -5px rgba(99, 102, 241, 0.4);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="d-block small fw-bold text-uppercase letter-spacing-wide opacity-75">Piutang vs Utang</span>
                    <i class="fas fa-balance-scale text-white opacity-50"></i>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="opacity-75">Piutang (Masuk):</small>
                    <small class="fw-bold">+ Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</small>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="opacity-75">Utang (Keluar):</small>
                    <small class="fw-bold">- Rp {{ number_format($totalUtang ?? 0, 0, ',', '.') }}</small>
                </div>
                
                <hr class="my-2 border-white" style="opacity: 0.2">
                
                @php
                    $netCashFlow = ($totalPiutang ?? 0) - ($totalUtang ?? 0);
                @endphp
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold" style="font-size: 0.8rem;">Arus Kas Bersih:</span>
                    <span class="fw-bold {{ $netCashFlow >= 0 ? 'text-white' : 'text-danger' }} border border-white bg-white bg-opacity-25 px-2 py-1 rounded" style="font-size: 0.85rem;">
                        {{ $netCashFlow >= 0 ? '+' : '-' }} Rp {{ number_format(abs($netCashFlow), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- CHART VISUALIZATION GACOR --}}
    @if($labaSales->count() > 0 || $labaProduk->count() > 0)
    <div class="row mb-4">
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="card card-custom bg-white border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-slate-dark mb-3"><i class="fas fa-chart-bar text-emerald me-2"></i>Top 5 Sales Pencetak Laba</h6>
                    <div style="height: 250px; position: relative;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="card card-custom bg-white border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-slate-dark mb-3"><i class="fas fa-chart-pie text-emerald me-2"></i>Top 5 Produk Paling Menguntungkan</h6>
                    <div style="height: 250px; position: relative;">
                        <canvas id="produkChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($labaMerek->count() > 0 || $labaCustomer->count() > 0)
    <div class="row mb-4">
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="card card-custom bg-white border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-slate-dark mb-3"><i class="fas fa-tags text-emerald me-2"></i>Top 5 Merek Paling Laris</h6>
                    <div style="height: 250px; position: relative;">
                        <canvas id="merekChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-custom bg-white border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-slate-dark mb-3"><i class="fas fa-crown text-emerald me-2"></i>Top 5 Customer VIP (Laba Terbesar)</h6>
                    <div style="height: 250px; position: relative;">
                        <canvas id="customerChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- KONTEN TAB UNTUK ANALITIK LABA KOTOR --}}
    <div class="card card-custom bg-white border-0">
        <div class="card-header bg-white pt-3 pb-0 border-bottom-0">
            <ul class="nav nav-tabs nav-tabs-custom" id="labaTabs" role="tablist">
                <li class="nav-item d-none d-lg-block" role="presentation">
                      <button class="nav-link" id="transaksi-tab" data-bs-toggle="tab" data-bs-target="#transaksi" type="button" role="tab"><i class="fas fa-file-invoice-dollar me-2"></i>Penjualan</button>
                </li>
                <li class="nav-item d-none d-lg-block" role="presentation">
                      <button class="nav-link" id="pembelian-tab" data-bs-toggle="tab" data-bs-target="#pembelian" type="button" role="tab"><i class="fas fa-shopping-cart me-2"></i>Pembelian</button>
                </li>
                <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab"><i class="fas fa-user-tie me-2"></i>Sales</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="produk-tab" data-bs-toggle="tab" data-bs-target="#produk" type="button" role="tab"><i class="fas fa-box me-2"></i>Produk</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="merek-tab" data-bs-toggle="tab" data-bs-target="#merek" type="button" role="tab"><i class="fas fa-tags me-2"></i>Merek</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" type="button" role="tab"><i class="fas fa-building me-2"></i>Customer</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="supplier-tab" data-bs-toggle="tab" data-bs-target="#supplier" type="button" role="tab"><i class="fas fa-truck-loading me-2"></i>Supplier</button>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content" id="labaTabsContent">
                
                {{-- TAB 0: PER TRANSAKSI (BARU) --}}
                <div class="tab-pane fade p-0" id="transaksi" role="tabpanel">
                    <div class="table-responsive d-none d-lg-block">
                        <table class="table table-laba w-100 mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-center" width="5%">Peringkat</th>
                                    <th width="15%">No SO / Waktu</th>
                                    <th>Customer</th>
                                    <th class="text-center">Pemasukan Akhir</th>
                                    <th class="text-center">Modal (HPP) Akhir</th>
                                    <th class="text-center">Charge</th>
                                    <th class="text-center" width="15%">Laba Bersih</th>
                                    <th class="text-center pe-4" width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($labaTransaksi as $index => $item)
                                <tr>
                                    <td class="ps-4 text-center align-middle">
                                        @if($index == 0) <span class="badge bg-warning text-dark"><i class="fas fa-crown me-1"></i>#1</span>
                                        @elseif($index == 1) <span class="badge bg-secondary"><i class="fas fa-medal me-1"></i>#2</span>
                                        @elseif($index == 2) <span class="badge bg-dark text-white"><i class="fas fa-award me-1"></i>#3</span>
                                        @else <span class="fw-bold text-slate-muted">#{{ $index + 1 }}</span> @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-slate-dark">{{ $item->no_so }}</div>
                                        <div class="text-slate-muted small">{{ \Carbon\Carbon::parse($item->tanggal_so)->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="fw-bold text-slate-dark align-middle">{{ $item->nama }}</td>
                                    <td class="text-center fw-medium text-slate-dark align-middle">
                                        Rp {{ number_format($item->omzet_awal - $item->omzet_retur, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center text-slate-muted align-middle">
                                        - Rp {{ number_format($item->total_hpp, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($item->qty_retur > 0)
                                            @php $charge = max(0, $item->omzet_retur - $item->potongan_cn); @endphp
                                            <div class="{{ $charge > 0 ? 'text-emerald' : 'text-slate-muted' }} fw-bold small">+ Rp {{ number_format($charge, 0, ',', '.') }}</div>
                                            <div class="text-warning small" style="font-size: 0.65rem;"><i class="fas fa-undo me-1"></i>Retur: {{ $item->qty_retur }} pcs</div>
                                        @else
                                            <div class="text-slate-muted">-</div>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="fw-bold {{ $item->total_laba >= 0 ? 'text-emerald' : 'text-danger' }} fs-6">
                                            {{ $item->total_laba >= 0 ? '+' : '' }} Rp {{ number_format($item->total_laba, 0, ',', '.') }}
                                        </div>
                                        @if($item->total_pendapatan > 0)
                                            <div>
                                                <span class="badge {{ $item->total_laba >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $item->total_laba >= 0 ? 'text-success' : 'text-danger' }} border mt-1" style="font-size: 0.65rem;">
                                                    Margin: {{ number_format(($item->total_laba / $item->total_pendapatan) * 100, 1) }}%
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4 align-middle">
                                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill py-1 px-3 fw-bold" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#detailLabaModal{{ str_replace('-', '', $item->no_so) }}">
                                            <i class="fas fa-search"></i> Detail
                                        </button>

                                        {{-- MODAL DETAIL LABA (PANGGIL FILE TERPISAH) --}}
                                        @include('laba.modal_detail', ['item' => $item, 'modal_id' => 'detailLabaModal'.str_replace('-', '', $item->no_so), 'modal_title' => 'Kalkulator Laba: '.$item->no_so])
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi pada rentang waktu ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- MOBILE CARDS --}}
                    <div class="d-lg-none p-2">
                        @forelse($labaTransaksi as $index => $item)
                        <div class="card card-custom mb-3 border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-slate-dark d-block">{{ $item->no_so }}</span>
                                    <span class="text-slate-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($item->tanggal_so)->format('d/m/Y H:i') }}</span>
                                </div>
                                <div>
                                    @if($index == 0) <span class="badge bg-warning text-dark"><i class="fas fa-crown me-1"></i>#1</span>
                                    @elseif($index == 1) <span class="badge bg-secondary"><i class="fas fa-medal me-1"></i>#2</span>
                                    @elseif($index == 2) <span class="badge bg-dark text-white"><i class="fas fa-award me-1"></i>#3</span>
                                    @else <span class="badge bg-light text-secondary">#{{ $index + 1 }}</span> @endif
                                </div>
                            </div>
                            <div class="card-body py-2">
                                <div class="mb-2">
                                    <span class="fw-bold text-slate-dark d-block" style="font-size: 0.9rem;">{{ $item->nama }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem;">
                                    <span class="text-slate-muted">Pemasukan Akhir</span>
                                    <span class="text-slate-dark fw-bold">Rp {{ number_format($item->omzet_awal - $item->omzet_retur, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem;">
                                    <span class="text-slate-muted">Modal (HPP)</span>
                                    <span class="text-danger">- Rp {{ number_format($item->total_hpp, 0, ',', '.') }}</span>
                                </div>
                                @if($item->qty_retur > 0)
                                    @php $charge = max(0, $item->omzet_retur - $item->potongan_cn); @endphp
                                    <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem;">
                                        <span class="text-slate-muted">Charge (Retur: {{ $item->qty_retur }})</span>
                                        <span class="{{ $charge > 0 ? 'text-emerald' : 'text-slate-muted' }} fw-bold">+ Rp {{ number_format($charge, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between mt-2 pt-2" style="font-size: 0.85rem; border-top: 1px dashed #e2e8f0;">
                                    <span class="text-slate-dark fw-bold">Laba Bersih</span>
                                    <span class="fw-bold {{ $item->total_laba >= 0 ? 'text-emerald' : 'text-danger' }}">
                                        {{ $item->total_laba >= 0 ? '+' : '' }} Rp {{ number_format($item->total_laba, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0 pb-3 pt-0">
                                <button type="button" class="btn btn-sm btn-outline-info w-100 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#detailLabaModal{{ str_replace('-', '', $item->no_so) }}">
                                    <i class="fas fa-search me-1"></i> Detail
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted bg-white rounded shadow-sm border"><i class="fas fa-file-invoice fa-2x mb-2 d-block opacity-25"></i>Belum ada transaksi pada rentang waktu ini.</div>
                        @endforelse
                    </div>
                </div>

                {{-- TAB: PER PEMBELIAN --}}
                <div class="tab-pane fade p-0" id="pembelian" role="tabpanel">
                    <div class="table-responsive d-none d-lg-block">
                        <table class="table table-laba w-100 mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-center" width="5%">Peringkat</th>
                                    <th width="15%">No PO / Waktu</th>
                                    <th>Supplier</th>
                                    <th class="text-center">Pengeluaran Awal</th>
                                    <th class="text-center">Klaim Retur (DN)</th>
                                    <th class="text-center" width="15%">Pengeluaran Bersih</th>
                                    <th class="text-center pe-4" width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($labaPembelian as $index => $item)
                                <tr>
                                    <td class="ps-4 text-center align-middle">
                                        @if($index == 0) <span class="badge bg-danger text-white"><i class="fas fa-exclamation-triangle me-1"></i>#1</span>
                                        @elseif($index == 1) <span class="badge bg-warning text-dark"><i class="fas fa-exclamation me-1"></i>#2</span>
                                        @elseif($index == 2) <span class="badge bg-secondary"><i class="fas fa-info me-1"></i>#3</span>
                                        @else <span class="fw-bold text-slate-muted">#{{ $index + 1 }}</span> @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-slate-dark">{{ $item->no_po }}</div>
                                        <div class="text-slate-muted small">{{ \Carbon\Carbon::parse($item->tanggal_po)->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="fw-bold text-slate-dark align-middle"><i class="fas fa-truck text-secondary me-1"></i> {{ $item->nama }}</td>
                                    <td class="text-center fw-medium text-slate-dark align-middle">
                                        Rp {{ number_format($item->pengeluaran_awal, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($item->qty_retur > 0)
                                            <div class="text-emerald fw-bold small">+ Rp {{ number_format($item->potongan_cn, 0, ',', '.') }}</div>
                                            <div class="text-warning small" style="font-size: 0.65rem;"><i class="fas fa-undo me-1"></i>Retur: {{ $item->qty_retur }} pcs</div>
                                        @else
                                            <div class="text-slate-muted">-</div>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="fw-bold text-danger fs-6">
                                            - Rp {{ number_format($item->pengeluaran_akhir, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="text-center pe-4 align-middle">
                                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill py-1 px-3 fw-bold" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#detailPembelianModal{{ str_replace('-', '', $item->no_po) }}">
                                            <i class="fas fa-search"></i> Detail
                                        </button>

                                        {{-- MODAL DETAIL PEMBELIAN --}}
                                        @include('laba.modal_pembelian', ['item' => $item, 'modal_id' => 'detailPembelianModal'.str_replace('-', '', $item->no_po), 'modal_title' => 'Arus Kas Pembelian: '.$item->no_po])
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada pembelian pada rentang waktu ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- MOBILE CARDS --}}
                    <div class="d-lg-none p-2">
                        @forelse($labaPembelian as $index => $item)
                        <div class="card card-custom mb-3 border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-slate-dark d-block">{{ $item->no_po }}</span>
                                    <span class="text-slate-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($item->tanggal_po)->format('d/m/Y') }}</span>
                                </div>
                                <div>
                                    @if($index == 0) <span class="badge bg-danger text-white"><i class="fas fa-exclamation-triangle me-1"></i>#1</span>
                                    @elseif($index == 1) <span class="badge bg-warning text-dark"><i class="fas fa-exclamation me-1"></i>#2</span>
                                    @elseif($index == 2) <span class="badge bg-secondary"><i class="fas fa-info me-1"></i>#3</span>
                                    @else <span class="badge bg-light text-secondary">#{{ $index + 1 }}</span> @endif
                                </div>
                            </div>
                            <div class="card-body py-2">
                                <div class="mb-2">
                                    <span class="fw-bold text-slate-dark d-block" style="font-size: 0.9rem;"><i class="fas fa-truck text-secondary me-1"></i>{{ $item->nama }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem;">
                                    <span class="text-slate-muted">Pengeluaran Awal</span>
                                    <span class="text-slate-dark fw-bold">Rp {{ number_format($item->pengeluaran_awal, 0, ',', '.') }}</span>
                                </div>
                                @if($item->qty_retur > 0)
                                    <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem;">
                                        <span class="text-slate-muted">Klaim Retur ({{ $item->qty_retur }})</span>
                                        <span class="text-emerald fw-bold">+ Rp {{ number_format($item->potongan_cn, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between mt-2 pt-2" style="font-size: 0.85rem; border-top: 1px dashed #e2e8f0;">
                                    <span class="text-slate-dark fw-bold">Pengeluaran Bersih</span>
                                    <span class="fw-bold text-danger">
                                        - Rp {{ number_format($item->pengeluaran_akhir, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0 pb-3 pt-0">
                                <button type="button" class="btn btn-sm btn-outline-info w-100 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#detailPembelianModal{{ str_replace('-', '', $item->no_po) }}">
                                    <i class="fas fa-search me-1"></i> Detail
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted bg-white rounded shadow-sm border"><i class="fas fa-shopping-cart fa-2x mb-2 d-block opacity-25"></i>Belum ada pembelian pada rentang waktu ini.</div>
                        @endforelse
                    </div>
                </div>

                {{-- TAB 1: PER SALES --}}
                <div class="tab-pane fade show active p-0" id="sales" role="tabpanel">
                      <div class="table-responsive d-none d-lg-block">
                        <table class="table table-laba w-100 mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4" width="5%">Peringkat</th>
                                    <th>Nama Sales / Staff</th>
                                    <th class="text-center">Qty Bersih</th>
                                    <th class="text-center">Omzet (Rp)</th>
                                    <th class="text-center">Laba Bersih (Rp)</th>
                                    <th class="text-center" width="10%">Margin</th>
                                    <th class="text-center pe-4" width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($labaSales as $index => $item)
                                <tr>
                                    <td class="ps-4 text-center align-middle">
                                        @if($index == 0) <span class="badge bg-warning text-dark"><i class="fas fa-crown me-1"></i>#1</span>
                                        @elseif($index == 1) <span class="badge bg-secondary"><i class="fas fa-medal me-1"></i>#2</span>
                                        @elseif($index == 2) <span class="badge bg-dark text-white"><i class="fas fa-award me-1"></i>#3</span>
                                        @else <span class="fw-bold text-slate-muted">#{{ $index + 1 }}</span> @endif
                                    </td>
                                    <td class="fw-bold text-slate-dark">{{ $item->nama }}</td>
                                    <td class="text-center text-slate-muted">{{ $item->total_qty }} pcs</td>
                                    <td class="text-center fw-medium text-slate-dark align-middle">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
                                    <td class="text-center fw-bold text-emerald align-middle">+ Rp {{ number_format($item->total_laba, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-slate-dark border px-2 py-1">{{ $item->total_pendapatan > 0 ? number_format(($item->total_laba / $item->total_pendapatan) * 100, 1) : 0 }}%</span>
                                    </td>
                                    <td class="text-center pe-4 align-middle">
                                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill py-1 px-3 fw-bold" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#detailLabaSalesModal{{ $index }}">
                                            <i class="fas fa-search"></i> Detail
                                        </button>
                                        @include('laba.modal_detail', ['item' => $item, 'modal_id' => 'detailLabaSalesModal'.$index, 'modal_title' => 'Kalkulator Laba Sales: '.$item->nama])
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data penjualan pada rentang waktu ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                      
                      {{-- MOBILE CARDS --}}
                      <div class="d-lg-none p-2">
                          @forelse($labaSales as $index => $item)
                          <div class="card card-custom mb-3 border-0 shadow-sm">
                              <div class="card-header-mobile">
                                  <div>
                                      <div class="fw-bold text-white" style="font-size:0.95rem;"><i class="fas fa-user-tie me-2"></i>{{ $item->nama }}</div>
                                      <div class="text-white opacity-75" style="font-size:0.75rem;">Sales / Staff</div>
                                  </div>
                                  <div>
                                      @if($index == 0) <span class="badge bg-warning text-dark"><i class="fas fa-crown me-1"></i>#1</span>
                                      @elseif($index == 1) <span class="badge bg-light text-secondary"><i class="fas fa-medal me-1"></i>#2</span>
                                      @elseif($index == 2) <span class="badge bg-light text-dark"><i class="fas fa-award me-1"></i>#3</span>
                                      @else <span class="badge bg-white bg-opacity-25 text-white">#{{ $index + 1 }}</span> @endif
                                  </div>
                              </div>
                              <div class="card-body-mobile">
                                  <div class="data-row">
                                      <span class="data-label">Qty Bersih</span>
                                      <span class="data-value">{{ $item->total_qty }} pcs</span>
                                  </div>
                                  <div class="data-row">
                                      <span class="data-label">Omzet</span>
                                      <span class="data-value">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</span>
                                  </div>
                                  <div class="data-row" style="border-top: 2px dashed #e2e8f0; margin-top:0.5rem; padding-top:0.5rem;">
                                      <span class="data-label">Laba Bersih</span>
                                      <span class="data-value {{ $item->total_laba >= 0 ? 'laba-positif' : 'laba-negatif' }}" style="font-size:1rem;">
                                          {{ $item->total_laba >= 0 ? '+' : '' }} Rp {{ number_format($item->total_laba, 0, ',', '.') }}
                                          @if($item->total_pendapatan > 0)
                                              <span class="badge {{ $item->total_laba >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $item->total_laba >= 0 ? 'text-success' : 'text-danger' }} border ms-1" style="font-size:0.65rem;">{{ number_format(($item->total_laba / $item->total_pendapatan) * 100, 1) }}%</span>
                                          @endif
                                      </span>
                                  </div>
                              </div>
                              <div class="card-action-mobile">
                                  <button type="button" class="btn btn-sm btn-outline-info rounded-pill py-1 px-4 fw-bold" style="font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#detailLabaSalesModal{{ $index }}">
                                      <i class="fas fa-search me-1"></i> Lihat Detail
                                  </button>
                              </div>
                          </div>
                          @empty
                          <div class="text-center py-5 text-muted"><i class="fas fa-chart-line fa-2x mb-2 d-block"></i>Belum ada data penjualan.</div>
                          @endforelse
                      </div>
                </div>

                {{-- TAB 2: PER PRODUK --}}
                <div class="tab-pane fade p-0" id="produk" role="tabpanel">
                      <div class="table-responsive d-none d-lg-block">
                        <table class="table table-laba w-100 mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4" width="5%">Top</th>
                                    <th>Kode - Nama Barang</th>
                                    <th class="text-center">Qty Bersih</th>
                                    <th class="text-center">Total Omzet</th>
                                    <th class="text-center">Laba Bersih (Rp)</th>
                                    <th class="text-center pe-4" width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($labaProduk as $index => $item)
                                <tr>
                                    <td class="ps-4 text-center align-middle">
                                        @if($index == 0) <span class="badge bg-warning text-dark"><i class="fas fa-crown me-1"></i>#1</span>
                                        @elseif($index == 1) <span class="badge bg-secondary"><i class="fas fa-medal me-1"></i>#2</span>
                                        @elseif($index == 2) <span class="badge bg-dark text-white"><i class="fas fa-award me-1"></i>#3</span>
                                        @else <span class="fw-bold text-slate-muted">#{{ $index + 1 }}</span> @endif
                                    </td>
                                    <td class="fw-bold text-slate-dark"><span class="badge bg-light text-secondary border me-2">{{ $item->kode_barang }}</span> {{ $item->nama }}</td>
                                    <td class="text-center text-slate-muted">{{ $item->total_qty }} pcs</td>
                                    <td class="text-center fw-medium text-slate-dark align-middle">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
                                    <td class="text-center fw-bold text-emerald align-middle">+ Rp {{ number_format($item->total_laba, 0, ',', '.') }}</td>
                                    <td class="text-center pe-4 align-middle">
                                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill py-1 px-3 fw-bold" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#detailLabaProdukModal{{ $index }}">
                                            <i class="fas fa-search"></i> Detail
                                        </button>
                                        @include('laba.modal_detail', ['item' => $item, 'modal_id' => 'detailLabaProdukModal'.$index, 'modal_title' => 'Kalkulator Laba Produk: '.$item->nama])
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data penjualan pada rentang waktu ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                      
                      {{-- MOBILE CARDS --}}
                      <div class="d-lg-none p-2">
                          @forelse($labaProduk as $index => $item)
                          <div class="card card-custom mb-3 border-0 shadow-sm">
                              <div class="card-header-mobile">
                                  <div>
                                      <div class="fw-bold text-white" style="font-size:0.95rem;"><span class="badge bg-white text-dark me-2 border">{{ $item->kode_barang }}</span>{{ $item->nama }}</div>
                                  </div>
                                  <div>
                                      @if($index == 0) <span class="badge bg-warning text-dark"><i class="fas fa-crown me-1"></i>#1</span>
                                      @elseif($index == 1) <span class="badge bg-light text-secondary"><i class="fas fa-medal me-1"></i>#2</span>
                                      @elseif($index == 2) <span class="badge bg-light text-dark"><i class="fas fa-award me-1"></i>#3</span>
                                      @else <span class="badge bg-white bg-opacity-25 text-white">#{{ $index + 1 }}</span> @endif
                                  </div>
                              </div>
                              <div class="card-body-mobile">
                                  <div class="data-row">
                                      <span class="data-label">Qty Bersih</span>
                                      <span class="data-value">{{ $item->total_qty }} pcs</span>
                                  </div>
                                  <div class="data-row">
                                      <span class="data-label">Total Omzet</span>
                                      <span class="data-value">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</span>
                                  </div>
                                  <div class="data-row" style="border-top: 2px dashed #e2e8f0; margin-top:0.5rem; padding-top:0.5rem;">
                                      <span class="data-label">Laba Bersih</span>
                                      <span class="data-value {{ $item->total_laba >= 0 ? 'laba-positif' : 'laba-negatif' }}" style="font-size:1rem;">
                                          {{ $item->total_laba >= 0 ? '+' : '' }} Rp {{ number_format($item->total_laba, 0, ',', '.') }}
                                      </span>
                                  </div>
                              </div>
                              <div class="card-action-mobile">
                                  <button type="button" class="btn btn-sm btn-outline-info rounded-pill py-1 px-4 fw-bold" style="font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#detailLabaProdukModal{{ $index }}">
                                      <i class="fas fa-search me-1"></i> Lihat Detail
                                  </button>
                              </div>
                          </div>
                          @empty
                          <div class="text-center py-5 text-muted"><i class="fas fa-box-open fa-2x mb-2 d-block"></i>Belum ada data penjualan produk.</div>
                          @endforelse
                      </div>
                </div>

                {{-- TAB 3: PER MEREK --}}
                <div class="tab-pane fade p-0" id="merek" role="tabpanel">
                      <div class="table-responsive d-none d-lg-block">
                        <table class="table table-laba w-100 mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4" width="5%">No</th>
                                    <th>Merek / Brand Produk</th>
                                    <th class="text-center">Qty Bersih</th>
                                    <th class="text-center">Laba Bersih (Rp)</th>
                                    <th class="text-center pe-4" width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($labaMerek as $index => $item)
                                <tr>
                                    <td class="ps-4 text-center align-middle">
                                        @if($index == 0) <span class="badge bg-warning text-dark"><i class="fas fa-crown me-1"></i>#1</span>
                                        @elseif($index == 1) <span class="badge bg-secondary"><i class="fas fa-medal me-1"></i>#2</span>
                                        @elseif($index == 2) <span class="badge bg-dark text-white"><i class="fas fa-award me-1"></i>#3</span>
                                        @else <span class="fw-bold text-slate-muted">#{{ $index + 1 }}</span> @endif
                                    </td>
                                    <td class="fw-bold text-slate-dark"><i class="fas fa-tag text-emerald me-2"></i> {{ $item->nama ?: 'Tanpa Merek' }}</td>
                                    <td class="text-center text-slate-muted">{{ $item->total_qty }} pcs</td>
                                    <td class="text-center fw-bold text-emerald align-middle">+ Rp {{ number_format($item->total_laba, 0, ',', '.') }}</td>
                                    <td class="text-center pe-4 align-middle">
                                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill py-1 px-3 fw-bold" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#detailLabaMerekModal{{ $index }}">
                                            <i class="fas fa-search"></i> Detail
                                        </button>
                                        @include('laba.modal_detail', ['item' => $item, 'modal_id' => 'detailLabaMerekModal'.$index, 'modal_title' => 'Kalkulator Laba Merek: '.($item->nama ?: 'Tanpa Merek')])
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data penjualan pada rentang waktu ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                      
                      {{-- MOBILE CARDS --}}
                      <div class="d-lg-none p-2">
                          @forelse($labaMerek as $index => $item)
                          <div class="card card-custom mb-3 border-0 shadow-sm">
                              <div class="card-header-mobile">
                                  <div>
                                      <div class="fw-bold text-white" style="font-size:0.95rem;"><i class="fas fa-tag me-2"></i>{{ $item->nama ?: 'Tanpa Merek' }}</div>
                                  </div>
                                  <div>
                                      @if($index == 0) <span class="badge bg-warning text-dark"><i class="fas fa-crown me-1"></i>#1</span>
                                      @elseif($index == 1) <span class="badge bg-light text-secondary"><i class="fas fa-medal me-1"></i>#2</span>
                                      @elseif($index == 2) <span class="badge bg-light text-dark"><i class="fas fa-award me-1"></i>#3</span>
                                      @else <span class="badge bg-white bg-opacity-25 text-white">#{{ $index + 1 }}</span> @endif
                                  </div>
                              </div>
                              <div class="card-body-mobile">
                                  <div class="data-row">
                                      <span class="data-label">Qty Bersih</span>
                                      <span class="data-value">{{ $item->total_qty }} pcs</span>
                                  </div>
                                  <div class="data-row" style="border-top: 2px dashed #e2e8f0; margin-top:0.5rem; padding-top:0.5rem;">
                                      <span class="data-label">Laba Bersih</span>
                                      <span class="data-value {{ $item->total_laba >= 0 ? 'laba-positif' : 'laba-negatif' }}" style="font-size:1rem;">
                                          {{ $item->total_laba >= 0 ? '+' : '' }} Rp {{ number_format($item->total_laba, 0, ',', '.') }}
                                      </span>
                                  </div>
                              </div>
                              <div class="card-action-mobile">
                                  <button type="button" class="btn btn-sm btn-outline-info rounded-pill py-1 px-4 fw-bold" style="font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#detailLabaMerekModal{{ $index }}">
                                      <i class="fas fa-search me-1"></i> Lihat Detail
                                  </button>
                              </div>
                          </div>
                          @empty
                          <div class="text-center py-5 text-muted"><i class="fas fa-tags fa-2x mb-2 d-block"></i>Belum ada data merek.</div>
                          @endforelse
                      </div>
                </div>

                {{-- TAB 4: PER CUSTOMER --}}
                <div class="tab-pane fade p-0" id="customer" role="tabpanel">
                      <div class="table-responsive d-none d-lg-block">
                        <table class="table table-laba w-100 mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4" width="5%">Top</th>
                                    <th>Nama Customer / Toko</th>
                                    <th class="text-center">Total Item Kotor</th>
                                    <th class="text-center">Pemasukan Bersih</th>
                                    <th class="text-center pe-4" width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($labaCustomer as $index => $item)
                                <tr>
                                    <td class="ps-4 text-center align-middle">
                                        @if($index == 0) <span class="badge bg-warning text-dark"><i class="fas fa-crown me-1"></i>#1</span>
                                        @elseif($index == 1) <span class="badge bg-secondary"><i class="fas fa-medal me-1"></i>#2</span>
                                        @elseif($index == 2) <span class="badge bg-dark text-white"><i class="fas fa-award me-1"></i>#3</span>
                                        @else <span class="fw-bold text-slate-muted">#{{ $index + 1 }}</span> @endif
                                    </td>
                                    <td class="fw-bold text-slate-dark"><i class="fas fa-store text-emerald me-2"></i> {{ $item->nama }}</td>
                                    <td class="text-center align-middle">
                                        <div class="text-slate-muted">{{ $item->qty_awal }} item</div>
                                        @if($item->qty_retur > 0)
                                            <div class="text-warning small" style="font-size: 0.65rem;"><i class="fas fa-undo me-1"></i>Retur: {{ $item->qty_retur }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="fw-bold text-emerald fs-6">
                                            + Rp {{ number_format($item->total_laba, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="text-center pe-4 align-middle">
                                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill py-1 px-3 fw-bold" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#detailLabaCustomerModal{{ $index }}">
                                            <i class="fas fa-search"></i> Detail
                                        </button>
                                        @include('laba.modal_detail', ['item' => $item, 'modal_id' => 'detailLabaCustomerModal'.$index, 'modal_title' => 'Kalkulator Laba Customer: '.$item->nama])
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data penjualan pada rentang waktu ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                      
                      {{-- MOBILE CARDS --}}
                      <div class="d-lg-none p-2">
                          @forelse($labaCustomer as $index => $item)
                          <div class="card card-custom mb-3 border-0 shadow-sm">
                              <div class="card-header-mobile">
                                  <div>
                                      <div class="fw-bold text-white" style="font-size:0.95rem;"><i class="fas fa-store me-2"></i>{{ $item->nama }}</div>
                                      <div class="text-white opacity-75" style="font-size:0.75rem;">Customer</div>
                                  </div>
                                  <div>
                                      @if($index == 0) <span class="badge bg-warning text-dark"><i class="fas fa-crown me-1"></i>#1</span>
                                      @elseif($index == 1) <span class="badge bg-light text-secondary"><i class="fas fa-medal me-1"></i>#2</span>
                                      @elseif($index == 2) <span class="badge bg-light text-dark"><i class="fas fa-award me-1"></i>#3</span>
                                      @else <span class="badge bg-white bg-opacity-25 text-white">#{{ $index + 1 }}</span> @endif
                                  </div>
                              </div>
                              <div class="card-body-mobile">
                                  <div class="data-row">
                                      <span class="data-label">Total Item Kotor</span>
                                      <span class="data-value">{{ $item->qty_awal }} item
                                          @if($item->qty_retur > 0)
                                              <div class="text-warning small" style="font-size: 0.65rem;"><i class="fas fa-undo me-1"></i>Retur: {{ $item->qty_retur }}</div>
                                          @endif
                                      </span>
                                  </div>
                                  <div class="data-row" style="border-top: 2px dashed #e2e8f0; margin-top:0.5rem; padding-top:0.5rem;">
                                      <span class="data-label">Pemasukan Bersih</span>
                                      <span class="data-value laba-positif" style="font-size:1rem;">
                                          + Rp {{ number_format($item->total_laba, 0, ',', '.') }}
                                      </span>
                                  </div>
                              </div>
                              <div class="card-action-mobile">
                                  <button type="button" class="btn btn-sm btn-outline-info rounded-pill py-1 px-4 fw-bold" style="font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#detailLabaCustomerModal{{ $index }}">
                                      <i class="fas fa-search me-1"></i> Lihat Detail
                                  </button>
                              </div>
                          </div>
                          @empty
                          <div class="text-center py-5 text-muted"><i class="fas fa-users fa-2x mb-2 d-block"></i>Belum ada data customer.</div>
                          @endforelse
                      </div>
                </div>

                {{-- TAB 5: PER SUPPLIER --}}
                <div class="tab-pane fade p-0" id="supplier" role="tabpanel">
                      <div class="table-responsive d-none d-lg-block">
                        <table class="table table-laba w-100 mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-center" width="5%">Peringkat</th>
                                    <th>Nama Supplier / Vendor</th>
                                    <th class="text-center">Total Item Kotor</th>
                                    <th class="text-center">Pengeluaran Bersih</th>
                                    <th class="text-center pe-4" width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($labaSupplier as $index => $item)
                                <tr>
                                    <td class="ps-4 text-center align-middle">
                                        @if($index == 0) <span class="badge bg-danger text-white"><i class="fas fa-exclamation-triangle me-1"></i>#1</span>
                                        @elseif($index == 1) <span class="badge bg-warning text-dark"><i class="fas fa-exclamation me-1"></i>#2</span>
                                        @elseif($index == 2) <span class="badge bg-secondary"><i class="fas fa-info me-1"></i>#3</span>
                                        @else <span class="fw-bold text-slate-muted">#{{ $index + 1 }}</span> @endif
                                    </td>
                                    <td class="fw-bold text-slate-dark"><i class="fas fa-truck text-secondary me-2"></i> {{ $item->nama }}</td>
                                    <td class="text-center">
                                        <div class="text-slate-muted">{{ $item->qty_awal }} item</div>
                                        @if($item->qty_retur > 0)
                                            <div class="text-warning small" style="font-size: 0.65rem;"><i class="fas fa-undo me-1"></i>Retur: {{ $item->qty_retur }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="fw-bold text-danger fs-6">
                                            - Rp {{ number_format($item->pengeluaran_akhir, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="text-center pe-4 align-middle">
                                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill py-1 px-3 fw-bold" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#detailLabaSupplierModal{{ $index }}">
                                            <i class="fas fa-search"></i> Detail
                                        </button>
                                        @include('laba.modal_pembelian', ['item' => $item, 'modal_id' => 'detailLabaSupplierModal'.$index, 'modal_title' => 'Arus Kas Supplier: '.$item->nama])
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data pembelian pada rentang waktu ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                      
                      {{-- MOBILE CARDS --}}
                      <div class="d-lg-none p-2">
                          @forelse($labaSupplier as $index => $item)
                          <div class="card card-custom mb-3 border-0 shadow-sm">
                              <div class="card-header-mobile supplier-header">
                                  <div>
                                      <div class="fw-bold text-white" style="font-size:0.95rem;"><i class="fas fa-truck me-2"></i>{{ $item->nama }}</div>
                                      <div class="text-white opacity-75" style="font-size:0.75rem;">Supplier / Vendor</div>
                                  </div>
                                  <div>
                                      @if($index == 0) <span class="badge bg-danger text-white"><i class="fas fa-exclamation-triangle me-1"></i>#1</span>
                                      @elseif($index == 1) <span class="badge bg-warning text-dark"><i class="fas fa-exclamation me-1"></i>#2</span>
                                      @elseif($index == 2) <span class="badge bg-secondary"><i class="fas fa-info me-1"></i>#3</span>
                                      @else <span class="badge bg-white bg-opacity-25 text-white">#{{ $index + 1 }}</span> @endif
                                  </div>
                              </div>
                              <div class="card-body-mobile">
                                  <div class="data-row">
                                      <span class="data-label">Total Item Kotor</span>
                                      <span class="data-value">{{ $item->qty_awal }} item
                                          @if($item->qty_retur > 0)
                                              <div class="text-warning small" style="font-size: 0.65rem;"><i class="fas fa-undo me-1"></i>Retur: {{ $item->qty_retur }}</div>
                                          @endif
                                      </span>
                                  </div>
                                  <div class="data-row" style="border-top: 2px dashed #e2e8f0; margin-top:0.5rem; padding-top:0.5rem;">
                                      <span class="data-label">Pengeluaran Bersih</span>
                                      <span class="data-value laba-negatif" style="font-size:1rem;">
                                          - Rp {{ number_format($item->pengeluaran_akhir, 0, ',', '.') }}
                                      </span>
                                  </div>
                              </div>
                              <div class="card-action-mobile">
                                  <button type="button" class="btn btn-sm btn-outline-info rounded-pill py-1 px-4 fw-bold" style="font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#detailLabaSupplierModal{{ $index }}">
                                      <i class="fas fa-search me-1"></i> Lihat Detail
                                  </button>
                              </div>
                          </div>
                          @empty
                          <div class="text-center py-5 text-muted"><i class="fas fa-building fa-2x mb-2 d-block"></i>Belum ada data supplier.</div>
                          @endforelse
                      </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // JS Sederhana untuk memunculkan input tanggal jika memilih "Custom Tanggal"
    function toggleCustomDate() {
        var tipe = document.getElementById('filter_tipe').value;
        var wrapper = document.getElementById('custom_date_wrapper');
        
        if(tipe === 'custom') {
            wrapper.style.setProperty('display', 'flex', 'important');
        } else {
            wrapper.style.setProperty('display', 'none', 'important');
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Cek apakah data tersedia sebelum me-render chart
        @if($labaSales->count() > 0 || $labaProduk->count() > 0)
            // Setup Chart.js Defaults
            Chart.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif";
            Chart.defaults.color = '#64748b';
            
            // 1. Data Top 5 Sales (Bar Chart)
            const salesDataRaw = @json($labaSales->take(5));
            if(salesDataRaw.length > 0) {
                const salesLabels = salesDataRaw.map(item => item.nama);
                const salesValues = salesDataRaw.map(item => item.total_laba);

                new Chart(document.getElementById('salesChart'), {
                    type: 'bar',
                    data: {
                        labels: salesLabels,
                        datasets: [{
                            label: 'Laba Bersih (Rp)',
                            data: salesValues,
                            backgroundColor: 'rgba(16, 185, 129, 0.85)',
                            borderColor: '#10b981',
                            borderWidth: 1,
                            borderRadius: 6,
                            barPercentage: 0.6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [4, 4], color: '#e2e8f0' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // 2. Data Top 5 Produk (Doughnut Chart)
            const produkDataRaw = @json($labaProduk->take(5));
            if(produkDataRaw.length > 0) {
                const produkLabels = produkDataRaw.map(item => {
                    let nama = item.nama;
                    return nama.length > 15 ? nama.substring(0, 15) + '...' : nama;
                });
                const produkValues = produkDataRaw.map(item => item.total_laba);
                
                // Palette warna modern
                const pieColors = ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899'];

                new Chart(document.getElementById('produkChart'), {
                    type: 'doughnut',
                    data: {
                        labels: produkLabels,
                        datasets: [{
                            data: produkValues,
                            backgroundColor: pieColors,
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: { position: window.innerWidth < 768 ? 'bottom' : 'right', labels: { boxWidth: 12, usePointStyle: true, padding: 15 } }
                        }
                    }
                });
            }
        @endif

        @if($labaMerek->count() > 0 || $labaCustomer->count() > 0)
            // Palette warna modern
            const pieColors = ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899'];

            // 3. Data Top 5 Merek (Doughnut Chart)
            const merekDataRaw = @json($labaMerek->take(5));
            if(merekDataRaw.length > 0) {
                const merekLabels = merekDataRaw.map(item => {
                    let nama = item.nama ? item.nama : 'Tanpa Merek';
                    return nama.length > 15 ? nama.substring(0, 15) + '...' : nama;
                });
                const merekValues = merekDataRaw.map(item => item.total_laba);

                new Chart(document.getElementById('merekChart'), {
                    type: 'doughnut',
                    data: {
                        labels: merekLabels,
                        datasets: [{
                            data: merekValues,
                            backgroundColor: pieColors,
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: { position: window.innerWidth < 768 ? 'bottom' : 'right', labels: { boxWidth: 12, usePointStyle: true, padding: 15 } }
                        }
                    }
                });
            }

            // 4. Data Top 5 Customer VIP (Bar Chart)
            const customerDataRaw = @json($labaCustomer->take(5));
            if(customerDataRaw.length > 0) {
                const customerLabels = customerDataRaw.map(item => {
                    let nama = item.nama;
                    return nama.length > 12 ? nama.substring(0, 12) + '...' : nama;
                });
                const customerValues = customerDataRaw.map(item => item.total_laba);

                new Chart(document.getElementById('customerChart'), {
                    type: 'bar',
                    data: {
                        labels: customerLabels,
                        datasets: [{
                            label: 'Laba Bersih (Rp)',
                            data: customerValues,
                            backgroundColor: 'rgba(59, 130, 246, 0.85)', // Warna biru agar beda dengan sales
                            borderColor: '#3b82f6',
                            borderWidth: 1,
                            borderRadius: 6,
                            barPercentage: 0.6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [4, 4], color: '#e2e8f0' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        @endif
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection











