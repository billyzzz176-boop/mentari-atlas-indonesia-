<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BackOrder;
use App\Models\Barang;
use App\Models\StockHistory;
use Illuminate\Support\Facades\DB;

class BackOrderController extends Controller
{
    /**
     * Menampilkan halaman daftar antrean Back Order
     */
    public function index()
    {
        // 1. Ambil semua data BO beserta relasinya dan urutkan murni berdasarkan waktu masuk (terlama ke terbaru)
        $backOrders = BackOrder::with(['penjualan.customer', 'penjualan.user', 'barang'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('backorder.index', compact('backOrders'));
    }

    /**
     * Memproses penebusan / pemenuhan stok antrean BO (Kemas Sisa)
     */
    public function penebusan($id)
    {
        $bo = BackOrder::lockForUpdate()->findOrFail($id);

        // Validasi status ganda (jika sudah terpenuhi atau selesai)
        if (strtolower($bo->status_bo) === 'terpenuhi' || strtolower($bo->status_bo) === 'selesai') {
            return back()->withErrors(['error' => 'Antrean Back Order ini sudah terpenuhi sebelumnya.']);
        }

        // KITA HAPUS BLOKIRAN INI AGAR BISA LANGSUNG KEMAS DARI BO MENU
        // if (in_array($bo->penjualan->status, ['draft', 'menunggu_restock'])) {
        //     return back()->withErrors(['error' => 'Gagal: Order ini belum dipacking sama sekali. Silakan lakukan restock lalu klik tombol Packing di riwayat SO untuk mengirim pesanan secara utuh.']);
        // }

        $barang = Barang::where('id', $bo->barang_id)->lockForUpdate()->firstOrFail();

        // Validasi ganda: Pastikan stok di gudang saat klik benar-benar mencukupi kuantitas kurangnya
        if ($barang->stok_akhir < $bo->jumlah_kurang) {
            return back()->withErrors(['error' => "Stok untuk barang '{$barang->nama_barang}' saat ini tidak mencukupi untuk memenuhi BO (Stok: {$barang->stok_akhir} Unit, Butuh: {$bo->jumlah_kurang} Unit)."]);
        }

        try {
            DB::beginTransaction();

            // Jika status masih menunggu_restock (belum pernah di-packing), kita update jadi ready_to_invoice
            if (in_array($bo->penjualan->status, ['draft', 'menunggu_restock'])) {
                $bo->penjualan->status = 'ready_to_invoice';
                $bo->penjualan->save();
            }

            // 1. Kurangi stok akhir gudang & naikkan jumlah barang keluar
            $stokSebelumnya = $barang->stok_akhir;
            $barang->update([
                'stok_akhir' => $barang->stok_akhir - $bo->jumlah_kurang,
                'barang_keluar' => $barang->barang_keluar + $bo->jumlah_kurang
            ]);

            // 2. Catat riwayat mutasi kartu stok secara formal
            StockHistory::record(
                $barang,
                -$bo->jumlah_kurang,
                'backorder_fulfillment', // Kategori mutasi pelunasan BO
                $bo->penjualan->no_so,
                'Pengiriman Tahap ' . (\App\Models\Pengiriman::where('penjualan_id', $bo->penjualan_id)->count() + 1) . ' (Pelunasan sisa kuantitas Back Order).',
                $stokSebelumnya
            );

            // 3. Ubah status antrean BO menjadi terpenuhi
            $bo->update([
                'status_bo' => 'terpenuhi'
            ]);

            // 4. Update total tagihan di Piutang & Buat Pengiriman Baru
            $detail = \App\Models\PenjualanDetail::where('penjualan_id', $bo->penjualan_id)
                        ->where('barang_id', $bo->barang_id)->first();
            
            // Create Pengiriman
            $noSOBase = str_replace('SO-', '', $bo->penjualan->no_so);
            $noINVBase = str_replace('SO', 'INV', $bo->penjualan->no_so);
            
            $existingCount = \App\Models\Pengiriman::where('penjualan_id', $bo->penjualan_id)->count();
            $nextSeq = $existingCount + 1;

            $pengiriman = \App\Models\Pengiriman::create([
                'penjualan_id' => $bo->penjualan_id,
                'no_pengiriman' => 'SJ-' . $noSOBase . '-' . $nextSeq,
                'no_invoice' => $noINVBase . '-' . $nextSeq,
                'tanggal_kirim' => now()->toDateString(),
                'plat_kendaraan' => null,
                'catatan' => 'Pengiriman Tahap ' . $nextSeq . ' (Pelunasan Back Order)',
            ]);

            if ($detail) {
                $tambahanTagihan = $bo->jumlah_kurang * $detail->harga_satuan;
                
                $piutang = \App\Models\Piutang::where('penjualan_id', $bo->penjualan_id)->lockForUpdate()->first();
                if ($piutang) {
                    $piutang->total_tagihan += $tambahanTagihan;
                    
                    // Update status bayar (MENGGUNAKAN ENUM YANG BENAR: belum_bayar, dibayar_sebagian, lunas)
                    if ($piutang->total_dibayar < $piutang->total_tagihan) {
                        $piutang->status_bayar = ($piutang->total_dibayar > 0) ? 'dibayar_sebagian' : 'belum_bayar';
                    } else {
                        $piutang->status_bayar = 'lunas';
                    }
                    
                    $piutang->save();
                } else {
                    // BIKIN PIUTANG BARU KARENA BELUM PERNAH ADA PENGIRIMAN
                    \App\Models\Piutang::create([
                        'no_invoice' => $noINVBase . '-1',
                        'penjualan_id' => $bo->penjualan_id,
                        'total_tagihan' => $tambahanTagihan,
                        'potongan' => 0, 
                        'total_dibayar' => 0,
                        'status_bayar' => 'belum_bayar',
                        'jatuh_tempo' => \Carbon\Carbon::now()->addDays((int)($bo->penjualan->customer->tempo_hari ?? 30)),
                        'diinput_by' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                    ]);
                }

                \App\Models\PengirimanDetail::create([
                    'pengiriman_id' => $pengiriman->id,
                    'barang_id' => $barang->id,
                    'jumlah_kirim' => $bo->jumlah_kurang,
                    'harga_satuan' => $detail->harga_satuan,
                    'subtotal' => $bo->jumlah_kurang * $detail->harga_satuan,
                ]);
            }

            DB::commit();
            return back()->with('success', "Stok cadangan antrean BO untuk barang '{$barang->nama_barang}' berhasil dilepaskan dan dikemas untuk pengiriman tahap {$nextSeq}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses pemenuhan BO: ' . $e->getMessage()]);
        }
    }
}