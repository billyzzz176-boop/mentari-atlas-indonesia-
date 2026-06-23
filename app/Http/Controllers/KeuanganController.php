<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Piutang;
use App\Models\Utang;
use App\Models\CreditNote;
use App\Models\PembayaranPiutang;
use App\Models\PembayaranUtang;
use App\Models\PenjualanDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon; 
use App\Models\ActivityLog;

class KeuanganController extends Controller
{
    // =========================================================================
    // ====================== BAGIAN PENJUALAN / SO ============================
    // =========================================================================
    public function getItemsSO($penjualan_id) 
    {
        $items = PenjualanDetail::where('penjualan_id', $penjualan_id)
                 ->with('barang')
                 ->get();

        return response()->json($items);
    }

    // =========================================================================
    // ====================== BAGIAN PIUTANG CUSTOMER ==========================
    // =========================================================================
    public function piutangIndex()
    {
        $piutangs = Piutang::with(['penjualan.customer', 'penjualan.user', 'PJ_Bayar', 'pembayarans'])
                            ->orderBy('id', 'asc')
                            ->get();
                            
        return view('keuangan.piutang', compact('piutangs'));
    }

    public function piutangShow($id)
    {
        $piutang = Piutang::with([
            'penjualan.customer', 
            'penjualan.details.barang', 
            'pembayarans' => function($q) {
                $q->orderBy('created_at', 'asc'); 
            },
            'pembayarans.penerima'
        ])->findOrFail($id);
        
        $sisa_tagihan = $piutang->total_tagihan - $piutang->total_dibayar;
        
        $creditNotes = CreditNote::where('tipe', 'penjualan')
                                 ->where('referensi_id', $piutang->penjualan_id)
                                 ->orderBy('created_at', 'asc')
                                 ->get();
        
        return view('keuangan.piutang_show', compact('piutang', 'sisa_tagihan', 'creditNotes'));
    }

    // PERBAIKAN: METODE LAINNYA & NOTIFIKASI SELEKTIF BERDASARKAN BUKTI BAYAR
    public function piutangBayar(Request $request, $id)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|string',
            'metode_pembayaran_lainnya' => 'required_if:metode_pembayaran,Lainnya|nullable|string|max:255', // <-- Kunci Input Manual
            'keterangan' => 'nullable|string',
            'bukti_bayar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Gunakan lockForUpdate() agar jika ada 2 staf akunting yang menekan bayar di mili-detik yang sama, salah satunya akan menunggu antrean eksekusi.
            $piutang = Piutang::lockForUpdate()->findOrFail($id);
            $sisa_tagihan = $piutang->total_tagihan - $piutang->total_dibayar;
            $bayar = $request->jumlah_bayar;

            if ($piutang->status_bayar === 'Lunas' || $sisa_tagihan <= 0) {
                return back()->withErrors(['error' => 'Gagal! Tagihan invoice ini sudah berstatus Lunas.']);
            }

            if ($bayar > $sisa_tagihan) {
                return back()->withErrors(['error' => 'Gagal! Jumlah bayar melebihi sisa tagihan (Sisa: Rp ' . number_format($sisa_tagihan, 0, ',', '.') . ').']);
            }

            $pathBukti = null;
            if ($request->hasFile('bukti_bayar')) {
                $pathBukti = $request->file('bukti_bayar')->store('bukti_pembayaran', 'public');
            }

            // Logika Sortir Nama Metode Pembayaran
            $metodeFinal = $request->metode_pembayaran;
            if ($metodeFinal === 'Lainnya') {
                $metodeFinal = trim($request->metode_pembayaran_lainnya);
            }

            $keteranganPiutang = trim($request->keterangan ?? '');
            if ($keteranganPiutang === '') {
                $piutang->load('penjualan');
                $nomorSo = $piutang->penjualan->no_so ?? 'SO tidak diketahui';
                $keteranganPiutang = "Pembayaran cicilan untuk invoice {$nomorSo} via {$metodeFinal}.";
            }

            PembayaranPiutang::create([
                'piutang_id' => $piutang->id,
                'jumlah_bayar' => $bayar,
                'tanggal_bayar' => Carbon::now(),
                'metode_pembayaran' => $metodeFinal, // Simpan hasil manual / preset select
                'diterima_oleh' => Auth::id(),
                'keterangan' => $keteranganPiutang,
                'bukti_bayar' => $pathBukti 
            ]);

            $totalBaru = $piutang->total_dibayar + $bayar;
            
            $status = 'Cicil';
            if ($totalBaru >= $piutang->total_tagihan) {
                $status = 'Lunas';
            }

            $piutang->update([
                'total_dibayar' => $totalBaru,
                'status_bayar'  => $status,
                'diinput_by'    => Auth::id() 
            ]);

            DB::commit();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'BAYAR PIUTANG',
                'description' => Auth::user()->name . ' mencatat pembayaran piutang customer sebesar Rp ' . number_format($bayar, 0, ',', '.'),
                'ip_address' => request()->ip(),
            ]);

            // NOTIFIKASI BERBEDA JIKA UPLOAD BUKTI VS TANPA UPLOAD BUKTI
            if ($pathBukti) {
                return back()->with('success', "Cicilan senilai Rp " . number_format($bayar, 0, ',', '.') . " berhasil dicatat beserta bukti fisiknya.");
            } else {
                return back()->with('success', "Cicilan senilai Rp " . number_format($bayar, 0, ',', '.') . " berhasil dicatat ke sistem (Tanpa Lampiran Bukti).");
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    // =========================================================================
    // ====================== BAGIAN UTANG SUPPLIER ============================
    // =========================================================================
    public function utangIndex()
    {
        $utangs = Utang::with(['pembelian.barang', 'PJ_Bayar', 'pembayarans'])
                        ->orderBy('id', 'asc')
                        ->get();
                        
        return view('keuangan.utang', compact('utangs'));
    }

    public function utangShow($id)
    {
        $utang = Utang::with([
            'pembelian.barang', 
            'pembayarans' => function($q) {
                $q->orderBy('created_at', 'asc'); 
            },
            'pembayarans.pembayar'
        ])->findOrFail($id);
        
        $sisa_utang = $utang->total_utang - $utang->potongan_dn - $utang->total_dibayar;
        
        $debitNotes = CreditNote::where('tipe', 'pembelian')
                                ->where('referensi_id', $utang->pembelian_id)
                                ->orderBy('created_at', 'asc')
                                ->get();
        
        return view('keuangan.utang_show', compact('utang', 'sisa_utang', 'debitNotes'));
    }

    public function utangBayar(Request $request, $id)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|string',
            'metode_pembayaran_lainnya' => 'required_if:metode_pembayaran,Lainnya|nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'bukti_bayar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Gunakan lockForUpdate() untuk pencegahan race condition
            $utang = Utang::lockForUpdate()->findOrFail($id);
            $sisa_utang = $utang->total_utang - $utang->potongan_dn - $utang->total_dibayar;
            $bayar = $request->jumlah_bayar;

            if ($utang->status_bayar === 'Lunas' || $sisa_utang <= 0) {
                return back()->withErrors(['error' => 'Gagal! Utang ini sudah berstatus Lunas.']);
            }

            if ($bayar > $sisa_utang) {
                return back()->withErrors(['error' => 'Gagal! Jumlah bayar melebihi sisa utang (Sisa: Rp ' . number_format($sisa_utang, 0, ',', '.') . ').']);
            }

            $pathBukti = null;
            if ($request->hasFile('bukti_bayar')) {
                $pathBukti = $request->file('bukti_bayar')->store('bukti_pembayaran_utang', 'public');
            }

            $keteranganUtang = trim($request->keterangan ?? '');
            if ($keteranganUtang === '') {
                $utang->load('pembelian');
                $nomorJurnal = $utang->no_utang_jurnal ?? 'UTG tidak diketahui';
                $keteranganUtang = "Pembayaran utang {$nomorJurnal} via {$request->metode_pembayaran}.";
            }

            // Logika Sortir Nama Metode Pembayaran
            $metodeFinal = $request->metode_pembayaran;
            if ($metodeFinal === 'Lainnya') {
                $metodeFinal = trim($request->metode_pembayaran_lainnya);
            }

            PembayaranUtang::create([
                'utang_id' => $utang->id,
                'jumlah_bayar' => $bayar,
                'tanggal_bayar' => Carbon::now(),
                'metode_pembayaran' => $metodeFinal,
                'dibayar_oleh' => Auth::id(),
                'keterangan' => $keteranganUtang,
                'bukti_bayar' => $pathBukti 
            ]);

            $totalBaru = $utang->total_dibayar + $bayar;
            
            $status = 'Cicil';
            if (($totalBaru + $utang->potongan_dn) >= $utang->total_utang) {
                $status = 'Lunas';
            }

            $utang->update([
                'total_dibayar' => $totalBaru,
                'status_bayar'  => $status,
                'diinput_by'    => Auth::id() 
            ]);

            DB::commit();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'BAYAR UTANG',
                'description' => Auth::user()->name . ' mencatat pembayaran utang supplier sebesar Rp ' . number_format($bayar, 0, ',', '.'),
                'ip_address' => request()->ip(),
            ]);

            // NOTIFIKASI BERBEDA JIKA UPLOAD BUKTI VS TANPA UPLOAD BUKTI
            if ($pathBukti) {
                return back()->with('success', "Pembayaran utang senilai Rp " . number_format($bayar, 0, ',', '.') . " berhasil dicatat beserta bukti fisiknya.");
            } else {
                return back()->with('success', "Pembayaran utang senilai Rp " . number_format($bayar, 0, ',', '.') . " berhasil dicatat ke sistem (Tanpa Lampiran Bukti).");
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    // =========================================================================
    // ====================== FITUR NOTA POTONGAN (CN/DN) ======================
    // =========================================================================
    public function storeCreditNote(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:penjualan,pembelian', 
            'referensi_id' => 'required|integer',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            if ($request->tipe == 'penjualan') {

                $piutang = Piutang::where('penjualan_id', $request->referensi_id)->lockForUpdate()->first();
                
                if (!$piutang) {
                    return back()->withErrors(['error' => 'Gagal! ID Penjualan (SO) bernilai ' . $request->referensi_id . ' tidak ditemukan dalam Buku Jurnal Piutang.']);
                }

                $piutang->total_tagihan = max(0, $piutang->total_tagihan - $request->nominal);
                $sisaPenjualan = $piutang->total_tagihan - $piutang->total_dibayar;

                if ($sisaPenjualan <= 0) {
                    $piutang->status_bayar = 'lunas';
                } elseif ($piutang->total_dibayar > 0) {
                    $piutang->status_bayar = 'cicil';
                } else {
                    $piutang->status_bayar = 'belum_bayar';
                }

                $piutang->save();

                PembayaranPiutang::create([
                    'piutang_id' => $piutang->id,
                    'jumlah_bayar' => $request->nominal,
                    'tanggal_bayar' => Carbon::now(),
                    'metode_pembayaran' => 'Credit Note',
                    'diterima_oleh' => Auth::id(),
                    'keterangan' => '[POTONGAN CREDIT NOTE]: ' . $request->keterangan . ' (Senilai Rp ' . number_format($request->nominal, 0, ',', '.') . ')',
                    'bukti_bayar' => null
                ]);

            } else {
                $utang = Utang::where('pembelian_id', $request->referensi_id)->lockForUpdate()->first();
                
                if (!$utang) {
                    return back()->withErrors(['error' => 'Gagal! ID Pembelian (PO) bernilai ' . $request->referensi_id . ' tidak ditemukan dalam Buku Jurnal Utang.']);
                }

                $utang->potongan_dn = $utang->potongan_dn + $request->nominal;
                $sisaPembelian = $utang->total_utang - $utang->potongan_dn - $utang->total_dibayar;

                if ($sisaPembelian <= 0) {
                    $utang->status_bayar = 'lunas';
                } elseif ($utang->total_dibayar > 0) {
                    $utang->status_bayar = 'cicil';
                } else {
                    $utang->status_bayar = 'belum_bayar';
                }

                $utang->save();
            }

            CreditNote::create([
                'nomor_cn' => 'CN-' . time(),
                'tipe' => $request->tipe,
                'referensi_id' => $request->referensi_id,
                'nominal' => $request->nominal,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();
            return back()->with('success', 'Nota Penyesuaian berhasil dieksekusi! Saldo terpotong tanpa mengganggu laporan arus kas.');

        } catch (\Exception $e) {
            try {
                DB::rollBack();
            } catch (\Throwable $t) {}
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }
}