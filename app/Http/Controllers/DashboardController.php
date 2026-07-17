<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Barang;
use App\Models\Piutang; 
use App\Models\Utang;
use App\Models\BackOrder; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total SO Keseluruhan
        $totalSO = Penjualan::count();
            
            // 2. Menunggu Approval (Pending)
            $menungguApproval = Penjualan::where('status_approval', 'pending')->count(); 
            
            $totalBarang = Barang::count();
            $stokKritis = Barang::where('stok_akhir', '<=', 15)->count();

            $salesBulan = [];
            $salesData = [];
            $omzetData = [];
            
            for ($i = 5; $i >= 0; $i--) {
                $bulan = Carbon::now()->subMonths($i);
                $salesBulan[] = $bulan->translatedFormat('M'); 
                
                $countSO = Penjualan::whereYear('created_at', $bulan->year)
                                    ->whereMonth('created_at', $bulan->month)
                                    ->count();
                
                $omzetSO = Penjualan::whereYear('created_at', $bulan->year)
                                    ->whereMonth('created_at', $bulan->month)
                                    ->where('status_approval', 'disetujui') // Omzet hanya dari yang disetujui
                                    ->sum('total_semua');

                $salesData[] = $countSO;
                $omzetData[] = (float) $omzetSO;
            }
            
            $statusMenunggu = $menungguApproval; 

            // 3. Status Selesai / Disetujui 
            $statusSelesai = Penjualan::where('status_approval', 'disetujui')->count();
            
            // 4. Status Ditolak
            $statusDitolak = Penjualan::whereIn('status_approval', ['ditolak', 'batal'])->count();
            
        $statusData = [$statusSelesai, $statusMenunggu, $statusDitolak];

        $data = compact('totalSO', 'menungguApproval', 'totalBarang', 'stokKritis', 'salesBulan', 'salesData', 'omzetData', 'statusData');

        return view('dashboard', $data);
    }

    public function salesIndex()
    {
        $userId = Auth::id();

        $data = call_user_func(function () use ($userId) {
            // 1. Total SO Pribadi
            $totalSO = Penjualan::where(function($q) use ($userId) { $q->where('user_id', $userId); })->count();
            
            // 2. SO Pending Pribadi
            $menungguApproval = Penjualan::where(function($q) use ($userId) { $q->where('user_id', $userId); })
                                         ->where('status_approval', 'pending')->count(); 

            // 3. Omzet Pribadi Bulan Ini
            $omzetBulanIni = Penjualan::where(function($q) use ($userId) { $q->where('user_id', $userId); })
                                      ->where('status_approval', 'disetujui')
                                      ->whereMonth('created_at', Carbon::now()->month)
                                      ->whereYear('created_at', Carbon::now()->year)
                                      ->sum('total_semua');

            // SO Terbaru dari Sales Ini
            $recentSO = Penjualan::with('customer')
                                 ->where(function($q) use ($userId) { $q->where('user_id', $userId); })
                                 ->orderBy('created_at', 'desc')
                                 ->take(5)
                                 ->get();

            $salesBulan = [];
            $salesData = [];
            $omzetData = [];
            
            for ($i = 5; $i >= 0; $i--) {
                $bulan = Carbon::now()->subMonths($i);
                $salesBulan[] = $bulan->translatedFormat('M'); 
                
                $countSO = Penjualan::where(function($q) use ($userId) { $q->where('user_id', $userId); })
                                    ->whereYear('created_at', $bulan->year)
                                    ->whereMonth('created_at', $bulan->month)
                                    ->count();
                
                $omzetSO = Penjualan::where(function($q) use ($userId) { $q->where('user_id', $userId); })
                                    ->whereYear('created_at', $bulan->year)
                                    ->whereMonth('created_at', $bulan->month)
                                    ->where('status_approval', 'disetujui')
                                    ->sum('total_semua');

                $salesData[] = $countSO;
                $omzetData[] = (float) $omzetSO;
            }

            $statusSelesai = Penjualan::where(function($q) use ($userId) { $q->where('user_id', $userId); })->where('status_approval', 'disetujui')->count();
            $statusDitolak = Penjualan::where(function($q) use ($userId) { $q->where('user_id', $userId); })->whereIn('status_approval', ['ditolak', 'batal'])->count();
            $statusData = [$statusSelesai, $menungguApproval, $statusDitolak];

            return compact('totalSO', 'menungguApproval', 'omzetBulanIni', 'recentSO', 'salesBulan', 'salesData', 'omzetData', 'statusData');
        });

        return view('sales.dashboard', $data);
    }

    public function getNotifications()
    {
        $user = Auth::user();
        $role = strtolower($user->role);
        $userId = $user->id;
        $isSales = in_array($role, ['sales', 'marketing']);

        // 1. Pending Approvals
        $pendingApprovals = Penjualan::when($isSales, function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        })->where('status_approval', 'pending')->count();

        // 2. Stok Kritis (Low Stock) dan Habis (Out of Stock)
        $lowStock = Barang::where('stok_akhir', '>', 0)->where('stok_akhir', '<=', 15)->count();
        $outOfStock = Barang::where('stok_akhir', '<=', 0)->count();

        // 3. Antrean BO (Back Order)
        $backOrder = BackOrder::where('status_bo', 'antrean')->count();

        // 4. Overdue Piutang (Jatuh Tempo)
        $overduePiutang = Piutang::where('status_bayar', '!=', 'Lunas')
            ->whereDate('jatuh_tempo', '<', Carbon::now()->toDateString())
            ->count();

        // 5. Overdue Utang (Jatuh Tempo Supplier)
        $overdueUtang = Utang::where('status_bayar', 'belum_bayar')
            ->whereDate('tanggal_jatuh_tempo', '<', Carbon::now()->toDateString())
            ->count();

        // 6. Retur Tertahan
        $returPembelianPending = DB::table('returs')->where('tipe', 'pembelian')->where('status_retur', 'pending')->count();
        $returPenjualanPending = DB::table('returs')->where('tipe', 'penjualan')->where('status_retur', 'pending')->count();
        $returPending = $returPembelianPending + $returPenjualanPending;

        $totalNotifications = $pendingApprovals + $lowStock + $outOfStock + $backOrder + $overduePiutang + $overdueUtang + $returPending;

        return response()->json([
            'total' => $totalNotifications,
            'pending_approvals' => $pendingApprovals,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'back_order' => $backOrder,
            'overdue_piutang' => $overduePiutang,
            'overdue_utang' => $overdueUtang,
            'retur_pending' => $returPending,
        ]);
    }

    public function warehouseIndex()
    {
        $totalProduk = Barang::count();
        $stokKritis = Barang::where('stok_akhir', '<=', 15)->count();
        $pesananMenungguPacking = Penjualan::where('status_approval', 'disetujui')
                                           ->where('status', 'diproses')
                                           ->count();
        $antreanBackOrder = BackOrder::where('status_bo', 'antrean')->count();

        // UPGRADE: Mengambil 5 antrean SO teratas yang sudah disetujui tapi belum dipacking
        $tabelPacking = Penjualan::with('customer')
                            ->where('status_approval', 'disetujui')
                            ->where('status', 'diproses')
                            ->orderBy('updated_at', 'asc') // Pakai asc agar yang paling lama menunggu muncul duluan
                            ->take(5)
                            ->get();

        return view('warehouse.dashboard', compact(
            'totalProduk', 'stokKritis', 'pesananMenungguPacking', 'antreanBackOrder', 'tabelPacking'
        )); 
    }

    public function keuanganIndex()
    {
        $totalOmzet = Piutang::sum('total_tagihan');

            $piutangBerjalan = Piutang::where('status_bayar', '!=', 'Lunas')->get()->sum(function($item) {
                return $item->total_tagihan - $item->total_dibayar - $item->potongan;
            });

            $kewajibanUtang = 0;
            if (class_exists('\App\Models\Utang')) {
                $kewajibanUtang = \App\Models\Utang::where('status_bayar', '!=', 'Lunas')->get()->sum(function($item) {
                    return $item->total_utang - $item->total_dibayar;
                });
            }

        $soDisetujui = Penjualan::where('status_approval', 'disetujui')->count();
        $riwayat = Piutang::with('penjualan.customer')->orderBy('updated_at', 'asc')->take(10)->get();

        $data = compact('totalOmzet', 'piutangBerjalan', 'kewajibanUtang', 'soDisetujui', 'riwayat');

        return view('keuangan.dashboard', $data); 
    }

    public function exportLaporan(Request $request)
    {
        // ... omitted since it's export, maybe less performance critical for load
    }
}