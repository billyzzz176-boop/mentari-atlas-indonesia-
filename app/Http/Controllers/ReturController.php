<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\Piutang;
use App\Models\StockHistory;
use App\Models\ActivityLog;
use App\Models\PembayaranPiutang;
use App\Models\Utang;
use App\Models\PembayaranUtang;
use App\Models\CreditNote;
use App\Models\BackOrder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReturController extends Controller
{
    // ==========================================
    // AREA RETUR PENJUALAN (DARI CUSTOMER)
    // ==========================================
    
    public function penjualanIndex()
    {
        $penjualans = Penjualan::with('customer')->orderBy('id', 'asc')->get();
        $barangs = Barang::all();
        
        $returs = DB::table('returs')
            ->leftJoin('penjualans', 'returs.referensi_id', '=', 'penjualans.id')
            ->leftJoin('customers', 'penjualans.customer_id', '=', 'customers.id')
            ->leftJoin('barangs', 'returs.barang_id', '=', 'barangs.id')
            ->where('returs.tipe', 'penjualan')
            ->select('returs.*', 'penjualans.no_so', 'customers.nama_customer', 'barangs.nama_barang')
            ->orderBy('returs.id', 'asc')
            ->get()
            ->map(function($item) {
                $item->no_retur_jual = $item->no_retur ?? 'RE-'.$item->id;
                $item->qty_retur = $item->qty;
                $item->jenis_retur = $item->jenis_retur ?? 'fisik'; 
                $item->status_kondisi = $item->kondisi;
                $item->nominal_potongan = $item->nominal_potongan ?? 0;
                
                $item->penjualan = (object) ['no_so' => $item->no_so ?? 'N/A'];
                $item->customer = (object) ['nama_customer' => $item->nama_customer ?? 'Umum'];
                $item->barang = (object) ['nama_barang' => $item->nama_barang ?? 'N/A'];
                $item->created_at = Carbon::parse($item->created_at);
                return $item;
            })
            ->groupBy('no_retur_jual');

        return view('warehouse.retur_penjualan', compact('penjualans', 'barangs', 'returs'));
    }

    public function getItemsSO($id)
    {
        $items = DB::table('penjualan_details')
            ->join('barangs', 'penjualan_details.barang_id', '=', 'barangs.id')
            ->where('penjualan_details.penjualan_id', $id)
            ->select('penjualan_details.*', 'barangs.nama_barang')
            ->get()
            ->map(function($item) {
                return [
                    'barang_id' => $item->barang_id,
                    'jumlah_diajukan' => $item->jumlah,
                    'harga_satuan' => $item->harga_satuan,
                    'barang' => [
                        'nama_barang' => $item->nama_barang
                    ]
                ];
            });

        return response()->json($items);
    }

    public function penjualanCreate()
    {
        $penjualans = Penjualan::with('customer')
            ->whereIn('status', ['ready_to_invoice', 'selesai'])
            ->orderBy('id', 'asc')
            ->get();
        return view('warehouse.retur_penjualan_create', compact('penjualans'));
    }

    public function penjualanStore(Request $request)
    {
        // Mendukung format single-item lama untuk kompatibilitas ke belakang (misal: Unit Test)
        if ($request->has('barang_id') && !$request->has('items')) {
            $items = [
                0 => [
                    'selected' => '1',
                    'barang_id' => $request->barang_id,
                    'qty_retur' => $request->qty_retur,
                    'jenis_retur' => $request->jenis_retur,
                    'status_kondisi' => $request->status_kondisi,
                    'aging_retur' => $request->aging_retur,
                    'nominal_potongan' => $request->nominal_potongan,
                    'alasan' => $request->alasan,
                ]
            ];
            $request->merge(['items' => $items]);
        }

        $request->validate([
            'penjualan_id' => 'required',
            'items' => 'required|array|min:1',
        ]);

        // Filter item yang dicentang
        $selectedItems = array_filter($request->items, function($item) {
            return isset($item['selected']) && $item['selected'] == '1';
        });

        if (empty($selectedItems)) {
            return redirect()->back()->withErrors(['error' => 'Pilih setidaknya satu barang untuk diretur!']);
        }

        DB::beginTransaction();
        try {
            $penjualan = Penjualan::find($request->penjualan_id);
            if (!$penjualan) {
                return redirect()->back()->withErrors(['error' => 'Data Penjualan tidak ditemukan.']);
            }
            $noSO = $penjualan->no_so;

            // Satu nomor retur untuk satu batch transaksi retur ini
            $noReturAuto = 'RE-JUAL-' . date('Ymd') . rand(100, 999);
            
            $totalPotonganBatch = 0;
            $alasanCombined = [];

            foreach ($selectedItems as $index => $itemData) {
                $barangId = $itemData['barang_id'] ?? null;
                $qtyRetur = (int) ($itemData['qty_retur'] ?? 0);
                $jenisRetur = $itemData['jenis_retur'] ?? 'fisik';
                $statusKondisi = $itemData['status_kondisi'] ?? 'bagus';
                $agingRetur = $itemData['aging_retur'] ?? '0_45';
                $nominalPotonganCustom = (float) ($itemData['nominal_potongan'] ?? 0);
                $alasanItem = $itemData['alasan'] ?? 'Klaim return';

                if (!$barangId || $qtyRetur <= 0) {
                    throw new \Exception("Data barang atau kuantitas retur tidak valid.");
                }

                $detail = DB::table('penjualan_details')
                    ->where('penjualan_id', $request->penjualan_id)
                    ->where('barang_id', $barangId)
                    ->first();

                if (!$detail) {
                    throw new \Exception("Barang dengan ID {$barangId} tidak ditemukan dalam nota penjualan SO {$noSO}.");
                }

                $totalReturSebelumnya = DB::table('returs')
                    ->where('tipe', 'penjualan')
                    ->where('referensi_id', $request->penjualan_id)
                    ->where('barang_id', $barangId)
                    ->sum('qty');

                $sisaKapasitasRetur = $detail->jumlah - $totalReturSebelumnya;

                if ($qtyRetur > $sisaKapasitasRetur) {
                    $barangObj = Barang::find($barangId);
                    $namaBarang = $barangObj ? $barangObj->nama_barang : "ID {$barangId}";
                    throw new \Exception("Batas retur terlampaui untuk item '{$namaBarang}'! Maksimal kuantitas yang masih bisa diretur adalah {$sisaKapasitasRetur} Pcs.");
                }

                // -------------------------------------------------------------
                // LOGIKA HITUNG CREDIT NOTE & DENDA AGING
                // -------------------------------------------------------------
                $nominalPotonganAwal = $jenisRetur === 'harga_credit_note' 
                    ? $nominalPotonganCustom 
                    : ((float) $qtyRetur * (float) $detail->harga_satuan);

                $nominalTerpakaiReturs = $nominalPotonganAwal;
                $teksAlasan = $alasanItem;

                if ($jenisRetur === 'fisik') {
                    if ($agingRetur === '46_90') {
                        $denda = $nominalPotonganAwal * 0.10;
                        $nominalTerpakaiReturs = $nominalPotonganAwal - $denda;
                        $teksAlasan = $alasanItem . ' [Retur 46-90 Hari: Kena charge denda 10%]';
                    } elseif ($agingRetur === '91_135') {
                        $denda = $nominalPotonganAwal * 0.30;
                        $nominalTerpakaiReturs = $nominalPotonganAwal - $denda;
                        $teksAlasan = $alasanItem . ' [Retur >90 Hari: Kena charge denda 30%]';
                    }
                }

                // Catat ke tabel `returs`
                DB::table('returs')->insert([
                    'tipe' => 'penjualan',
                    'referensi_id' => $request->penjualan_id,
                    'barang_id' => $barangId,
                    'qty' => $qtyRetur,
                    'nominal_potongan' => $nominalTerpakaiReturs,
                    'jenis_retur' => $jenisRetur,
                    'kondisi' => $jenisRetur === 'fisik' ? $statusKondisi : 'tidak_mempengaruhi',
                    'alasan' => $teksAlasan,
                    'no_retur' => $noReturAuto,
                    'status_retur' => 'completed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Terbit Credit Note per item
                $nomorCN = 'CN-JUAL-' . date('Ymd') . rand(100, 999);
                CreditNote::create([
                    'nomor_cn' => $nomorCN,
                    'tipe' => 'penjualan',
                    'referensi_id' => $request->penjualan_id,
                    'nominal' => $nominalTerpakaiReturs,
                    'keterangan' => 'Credit Note terbit dari klaim (' . $jenisRetur . '): ' . $teksAlasan,
                ]);

                // Update fisik gudang
                if ($jenisRetur === 'fisik') {
                    $barang = Barang::lockForUpdate()->find($barangId);
                    if ($barang) {
                        if (strtolower($statusKondisi) === 'bagus') {
                            $stokSebelumnya = $barang->stok_akhir;
                            $barang->update([
                                'stok_akhir' => $barang->stok_akhir + $qtyRetur,
                                'barang_masuk' => $barang->barang_masuk + $qtyRetur
                            ]);

                            StockHistory::record(
                                $barang,
                                $qtyRetur, 
                                'return_customer',
                                $noReturAuto . ' / ' . $noSO, 
                                'Retur fisik customer (BAGUS). Stok ditarik kembali ke sistem gudang utama. [CN: ' . $nomorCN . ']',
                                $stokSebelumnya
                            );
                        } else {
                            $stokRusakSebelumnya = $barang->stok_rusak ?? 0;
                            $barang->update([
                                'stok_rusak' => $stokRusakSebelumnya + $qtyRetur
                            ]);

                            StockHistory::record(
                                $barang,
                                $qtyRetur, 
                                'return_customer',
                                $noReturAuto . ' / ' . $noSO, 
                                'Retur fisik customer (RUSAK). Barang otomatis dialokasikan ke Stok Rusak. [CN: ' . $nomorCN . ']',
                                $stokRusakSebelumnya
                            );
                        }
                    }
                }

                $totalPotonganBatch += $nominalTerpakaiReturs;
                $alasanCombined[] = $teksAlasan;
            }

            // Update Piutang secara akumulatif
            $piutang = Piutang::where('penjualan_id', $request->penjualan_id)->first();
            if ($piutang && $totalPotonganBatch > 0) {
                $piutang->total_tagihan = max(0, (float) $piutang->total_tagihan - $totalPotonganBatch);
                $sisa = (float) $piutang->total_tagihan - (float) $piutang->total_dibayar;

                if ($sisa <= 0) {
                    $piutang->status_bayar = 'lunas';
                    $piutang->total_tagihan = 0;
                } elseif ((float) $piutang->total_dibayar > 0) {
                    $piutang->status_bayar = 'cicil';
                } else {
                    $piutang->status_bayar = 'belum_bayar';
                }

                $piutang->save();

                // Catat pembayaran piutang dengan total akumulasi
                PembayaranPiutang::create([
                    'piutang_id' => $piutang->id,
                    'jumlah_bayar' => $totalPotonganBatch,
                    'tanggal_bayar' => Carbon::now(),
                    'metode_pembayaran' => 'Retur Customer / Credit Note',
                    'diterima_oleh' => Auth::id(),
                    'keterangan' => 'Retur massal otomatis mengurangi tagihan [' . $noReturAuto . ']: ' . implode('; ', array_unique($alasanCombined)),
                    'bukti_bayar' => null,
                ]);
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'RETUR PENJUALAN',
                'description' => Auth::user()->name . ' memproses klaim retur penjualan massal (Credit Note): ' . $noReturAuto,
                'ip_address' => request()->ip(),
            ]);

            DB::commit();
            return redirect()->route('retur.penjualan.index')->with('success', 'Klaim Retur Penjualan & Credit Note berhasil diproses.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'Gagal Memproses Retur Penjualan: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // AREA RETUR PEMBELIAN (KE SUPPLIER)
    // ==========================================
    
    public function pembelianIndex()
    {
        $pembelians = Pembelian::orderBy('id', 'asc')->get();
        $barangs = Barang::all();
        
        $returs = DB::table('returs')
            ->leftJoin('pembelians', 'returs.referensi_id', '=', 'pembelians.id')
            ->leftJoin('barangs', 'returs.barang_id', '=', 'barangs.id')
            ->where('returs.tipe', 'pembelian')
            ->select('returs.*', 'pembelians.no_pembelian', 'pembelians.nama_supplier', 'barangs.nama_barang')
            ->orderBy('returs.id', 'asc')
            ->get()
            ->map(function($item) {
                $item->no_retur_beli = $item->no_retur ?? 'RB-'.$item->id;
                $item->qty_retur = $item->qty;
                $item->jenis_retur = $item->jenis_retur ?? 'fisik'; 
                $item->status_kondisi = $item->kondisi;
                $item->nominal_potongan = $item->nominal_potongan ?? 0;
                $item->status_retur = $item->status_retur ?? 'completed'; // Draf / Selesai
                
                $item->pembelian = (object) ['no_pembelian' => $item->no_pembelian ?? 'N/A'];
                $item->nama_supplier = $item->nama_supplier ?? 'N/A';
                $item->barang = (object) ['nama_barang' => $item->nama_barang ?? 'N/A'];
                $item->created_at = Carbon::parse($item->created_at);
                return $item;
            })
            ->groupBy('no_retur_beli');

        return view('warehouse.retur_pembelian', compact('pembelians', 'barangs', 'returs'));
    }

    public function pembelianCreate()
    {
        $pembelians = Pembelian::select('no_pembelian', 'nama_supplier')
            ->distinct()
            ->orderBy('no_pembelian', 'desc')
            ->get();
        return view('warehouse.retur_pembelian_create', compact('pembelians'));
    }

    public function getItemsPO($id)
    {
        if (is_numeric($id)) {
            $p = Pembelian::find($id);
            $noPO = $p ? $p->no_pembelian : $id;
        } else {
            $noPO = $id;
        }

        $items = DB::table('pembelians')
            ->join('barangs', 'pembelians.barang_id', '=', 'barangs.id')
            ->where('pembelians.no_pembelian', $noPO)
            ->select('pembelians.*', 'barangs.nama_barang')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'barang_id' => $item->barang_id,
                    'jumlah_diajukan' => $item->jumlah_beli,
                    'harga_beli' => $item->harga_beli_hpp ?? 0,
                    'barang' => [
                        'nama_barang' => $item->nama_barang
                    ]
                ];
            });

        return response()->json($items);
    }

    public function pembelianStore(Request $request)
    {
        // Mendukung format single-item lama untuk kompatibilitas ke belakang (misal: Unit Test)
        if ($request->has('barang_id') && !$request->has('items')) {
            $items = [
                0 => [
                    'selected' => '1',
                    'barang_id' => $request->barang_id,
                    'qty_retur' => $request->qty_retur,
                    'jenis_retur' => $request->jenis_retur,
                    'status_kondisi' => $request->status_kondisi,
                    'nominal_potongan' => $request->nominal_potongan,
                    'alasan' => $request->alasan,
                ]
            ];
            $request->merge(['items' => $items]);
        }

        $request->validate([
            'pembelian_id' => 'required',
            'items' => 'required|array|min:1',
        ]);

        // Filter item yang dicentang
        $selectedItems = array_filter($request->items, function($item) {
            return isset($item['selected']) && $item['selected'] == '1';
        });

        if (empty($selectedItems)) {
            return redirect()->back()->withErrors(['error' => 'Pilih setidaknya satu barang untuk diretur!']);
        }

        DB::beginTransaction();
        try {
            $pembelianVal = $request->pembelian_id;
            $pembelian = Pembelian::where('no_pembelian', $pembelianVal)->first() 
                ?? Pembelian::find($pembelianVal);

            if (!$pembelian) {
                return redirect()->back()->withErrors(['error' => 'Data Pembelian tidak ditemukan.']);
            }
            $noPO = $pembelian->no_pembelian;

            // Satu nomor retur untuk satu batch transaksi retur ini
            $noReturAuto = 'RE-BELI-' . date('Ymd') . rand(100, 999);
            $totalPotonganBatch = 0;
            $alasanCombined = [];

            foreach ($selectedItems as $index => $itemData) {
                $barangId = $itemData['barang_id'] ?? null;
                $qtyRetur = (int) ($itemData['qty_retur'] ?? 0);
                $jenisRetur = $itemData['jenis_retur'] ?? 'fisik';
                $statusKondisi = $itemData['status_kondisi'] ?? 'bagus';
                $nominalPotonganCustom = (float) ($itemData['nominal_potongan'] ?? 0);
                $alasanItem = $itemData['alasan'] ?? 'Klaim return';

                if (!$barangId || $qtyRetur <= 0) {
                    throw new \Exception("Data barang atau kuantitas retur tidak valid.");
                }

                $barang = Barang::lockForUpdate()->find($barangId);
                if (!$barang) {
                    throw new \Exception("Barang dengan ID {$barangId} tidak ditemukan.");
                }

                // Ambil record pembelian spesifik untuk barang ini dalam PO tersebut
                $detail = Pembelian::where('no_pembelian', $noPO)
                    ->where('barang_id', $barangId)
                    ->first();

                if (!$detail) {
                    throw new \Exception("Barang '{$barang->nama_barang}' tidak ditemukan dalam nota pembelian PO {$noPO}.");
                }

                // Cek stok gudang jika retur fisik
                if ($jenisRetur === 'fisik') {
                    $stokTersedia = ($statusKondisi === 'rusak') ? ($barang->stok_rusak ?? 0) : ($barang->stok_akhir ?? 0);
                    if ($stokTersedia < $qtyRetur) {
                        throw new \Exception("Gagal Retur Fisik untuk '{$barang->nama_barang}'! Sisa stok ".ucfirst($statusKondisi)." di gudang hanya ({$stokTersedia} pcs), tidak cukup untuk diretur.");
                    }
                }

                // Kalkulasi nominal potongan
                $nominalPotongan = ($jenisRetur === 'harga_debit_note' && $nominalPotonganCustom > 0)
                    ? $nominalPotonganCustom 
                    : ((float) $qtyRetur * (float) $detail->harga_beli_hpp);

                // Masukkan ke tabel returs
                DB::table('returs')->insert([
                    'no_retur' => $noReturAuto,
                    'tipe' => 'pembelian',
                    'jenis_retur' => $jenisRetur,
                    'referensi_id' => $detail->id, // hubungkan ke record detail pembelian spesifik
                    'barang_id' => $barangId,
                    'qty' => $qtyRetur,
                    'kondisi' => $jenisRetur === 'fisik' ? $statusKondisi : 'tidak_mempengaruhi',
                    'nominal_potongan' => $nominalPotongan,
                    'status_retur' => 'completed',
                    'alasan' => $alasanItem,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update fisik gudang
                if ($jenisRetur === 'fisik') {
                    if ($statusKondisi === 'rusak') {
                        $stokRusakSebelumnya = $barang->stok_rusak ?? 0;
                        $barang->update(['stok_rusak' => $stokRusakSebelumnya - $qtyRetur]);
                        
                        StockHistory::record(
                            $barang, -$qtyRetur, 'return_supplier', $noReturAuto . ' / ' . $noPO, 
                            'Retur fisik BARANG RUSAK ke supplier. [DN per item]', $stokRusakSebelumnya
                        );
                    } else {
                        $stokSebelumnya = $barang->stok_akhir;
                        $barang->update([
                            'stok_akhir' => $barang->stok_akhir - $qtyRetur,
                            'barang_keluar' => $barang->barang_keluar + $qtyRetur,
                        ]);
                        
                        StockHistory::record(
                            $barang, -$qtyRetur, 'return_supplier', $noReturAuto . ' / ' . $noPO, 
                            'Retur fisik BARANG BAGUS ke supplier. [DN per item]', $stokSebelumnya
                        );
                    }
                }

                $totalPotonganBatch += $nominalPotongan;
                $alasanCombined[] = $alasanItem;
            }

            // Terbit Credit Note (Debit Note) secara akumulatif untuk batch ini
            $nomorDN = 'DN-BELI-' . date('Ymd') . rand(100, 999);
            
            // Tentukan referensi_id untuk CreditNote
            $utang = null;
            $refIdForDN = $pembelian->id;
            
            $allPembelianIds = Pembelian::where('no_pembelian', $noPO)->pluck('id');
            $utang = Utang::whereIn('pembelian_id', $allPembelianIds)->first();
            if ($utang) {
                $refIdForDN = $utang->pembelian_id;
            } else {
                $firstId = Pembelian::where('no_pembelian', $noPO)->orderBy('id', 'asc')->value('id');
                if ($firstId) {
                    $refIdForDN = $firstId;
                }
            }

            CreditNote::create([
                'nomor_cn' => $nomorDN, 
                'tipe' => 'pembelian',
                'referensi_id' => $refIdForDN,
                'nominal' => $totalPotonganBatch,
                'keterangan' => 'Debit Note terbit akibat klaim retur pembelian massal ke supplier: ' . implode('; ', $alasanCombined),
            ]);

            // Potong utang
            if ($utang) {
                $utang->potongan_dn = $utang->potongan_dn + $totalPotonganBatch;
                
                $sisaTagihan = $utang->total_utang - $utang->potongan_dn - $utang->total_dibayar;
                if ($sisaTagihan <= 0) {
                    $utang->status_bayar = 'lunas';
                } elseif ($utang->total_dibayar > 0) {
                    $utang->status_bayar = 'cicil';
                } else {
                    $utang->status_bayar = 'belum_bayar';
                }
                $utang->save();
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'RETUR PEMBELIAN',
                'description' => Auth::user()->name . ' memproses klaim retur pembelian massal (Debit Note): ' . $noReturAuto,
                'ip_address' => request()->ip(),
            ]);

            DB::commit();
            return redirect()->route('retur.pembelian.index')->with('success', 'Retur Pembelian Massal & Debit Note berhasil diproses. Beban utang ke supplier berhasil dipotong!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'Gagal Memproses Retur Pembelian: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // FUNGSI EKSEKUSI TOMBOL "RETURN SEKARANG" (DARI QC)
    // ==========================================
    public function eksekusiReturPending(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Ambil data retur utama yang diklik
            $returData = DB::table('returs')->where('id', $id)->first();
            
            if (!$returData || $returData->status_retur !== 'pending') {
                return redirect()->back()->withErrors(['error' => 'Klaim Return tidak valid atau sudah dieksekusi sebelumnya.']);
            }

            // Ambil semua item return yang berbagi no_retur yang sama dan masih pending
            $returItems = DB::table('returs')
                ->where('no_retur', $returData->no_retur)
                ->where('status_retur', 'pending')
                ->get();

            if ($returItems->isEmpty()) {
                return redirect()->back()->withErrors(['error' => 'Tidak ada item return pending ditemukan.']);
            }

            $totalNominalPotongan = 0;
            $alasanCombined = [];

            foreach ($returItems as $item) {
                $pembelian = Pembelian::find($item->referensi_id);
                $barang = Barang::find($item->barang_id);
                
                if (!$pembelian || !$barang) continue;

                // 1. Eksekusi Potong Stok Rusak (Jika jenis retur fisik / RMA)
                if ($item->jenis_retur === 'fisik' && strtolower($item->kondisi) === 'rusak') {
                    $stokRusakSekarang = $barang->stok_rusak ?? 0;
                    
                    if ($stokRusakSekarang < $item->qty) {
                        $qtyYangBisaDipotong = $stokRusakSekarang;
                        $barang->update(['stok_rusak' => 0]);
                    } else {
                        $qtyYangBisaDipotong = $item->qty;
                        $barang->update(['stok_rusak' => $stokRusakSekarang - $item->qty]);
                    }

                    if ($qtyYangBisaDipotong > 0) {
                        StockHistory::record(
                            $barang, -$qtyYangBisaDipotong, 'return_supplier', $item->no_retur . ' / ' . ($pembelian->no_pembelian ?? '-'), 
                            'Eksekusi RMA. Fisik BARANG RUSAK dari hasil QC telah dikirim ke supplier.', $stokRusakSekarang
                        );
                    }
                }

                // Akumulasikan nominal potongan
                $totalNominalPotongan += (float) $item->nominal_potongan;
                $alasanCombined[] = $item->alasan;

                // Ubah status item retur menjadi Completed
                DB::table('returs')->where('id', $item->id)->update([
                    'status_retur' => 'completed',
                    'updated_at' => now()
                ]);
            }

            // 2. Terbitkan 1 Debit Note Resmi untuk seluruh nomor return tersebut
            $nomorDN = 'DN-QC-' . date('Ymd') . rand(100, 999);
            
            // Cari pembelian terkait untuk mendapatkan nomor PO dan utang
            $pembelian = Pembelian::find($returData->referensi_id);
            $utang = null;
            $refIdForDN = $returData->referensi_id;

            if ($pembelian) {
                // Cari semua ID pembelian dengan nomor PO yang sama
                $allPembelianIds = Pembelian::where('no_pembelian', $pembelian->no_pembelian)->pluck('id');
                // Cari Utang yang merujuk ke salah satu dari ID pembelian tersebut
                $utang = Utang::whereIn('pembelian_id', $allPembelianIds)->first();
                if ($utang) {
                    $refIdForDN = $utang->pembelian_id;
                } else {
                    $firstId = Pembelian::where('no_pembelian', $pembelian->no_pembelian)->orderBy('id', 'asc')->value('id');
                    if ($firstId) {
                        $refIdForDN = $firstId;
                    }
                }
            }

            CreditNote::create([
                'nomor_cn' => $nomorDN, 
                'tipe' => 'pembelian',
                'referensi_id' => $refIdForDN, // Gunakan referensi_id yang sesuai dengan Utang
                'nominal' => $totalNominalPotongan,
                'keterangan' => 'Eksekusi Debit Note dari QC (' . $returData->no_retur . '): ' . implode('; ', array_unique($alasanCombined)),
            ]);

            // 3. Eksekusi Pemotongan Utang (Adjustment pada Utang utama)
            if ($utang) {
                // Tambahkan total potongan ke potongan_dn
                $utang->potongan_dn = $utang->potongan_dn + $totalNominalPotongan;
                
                $sisaTagihan = $utang->total_utang - $utang->potongan_dn - $utang->total_dibayar;
                if ($sisaTagihan <= 0) {
                    $utang->status_bayar = 'lunas';
                } elseif ($utang->total_dibayar > 0) {
                    $utang->status_bayar = 'cicil';
                } else {
                    $utang->status_bayar = 'belum_bayar';
                }
                $utang->save();
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'RETUR PEMBELIAN',
                'description' => Auth::user()->name . ' memproses eksekusi massal retur pembelian (Debit Note): ' . $returData->no_retur,
                'ip_address' => request()->ip(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'RMA Berhasil Dieksekusi untuk seluruh produk di No. Return ' . $returData->no_retur . '! Fisik barang telah dipotong, dan tagihan utang supplier dikurangi senilai Rp ' . number_format($totalNominalPotongan,0,',','.'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'Gagal Mengeksekusi RMA Retur: ' . $e->getMessage()]);
        }
    }
}