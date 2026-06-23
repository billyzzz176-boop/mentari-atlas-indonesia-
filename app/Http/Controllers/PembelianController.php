<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\Barang;
use App\Models\Utang;
use App\Models\Supplier;
use App\Models\StockHistory;
use App\Models\PembayaranUtang;
use App\Models\CreditNote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ActivityLog;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $barangs = Barang::orderBy('kode_barang', 'asc')->get();
        
        $query = DB::table('pembelians')
            ->select('no_pembelian')
            ->groupBy('no_pembelian');
            
        if ($search) {
            $query->where('no_pembelian', 'like', "%{$search}%")
                  ->orWhere('nama_supplier', 'like', "%{$search}%");
        }
        
        $poList = $query->orderBy(DB::raw('MIN(created_at)'), 'asc')->paginate(10)->withQueryString();

        $riwayat = Pembelian::with('barang')
            ->whereIn('no_pembelian', $poList->pluck('no_pembelian'))
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('no_pembelian');
            
        $suppliers = Supplier::orderBy('id', 'asc')->get();
        
        return view('pembelian.index', compact('barangs', 'riwayat', 'suppliers', 'poList', 'search'));
    }

    public function store(Request $request)
    {
        // Normalize scalar inputs to arrays for backward compatibility
        if ($request->has('barang_id') && !is_array($request->barang_id)) {
            $request->merge([
                'barang_id' => [$request->barang_id],
                'jumlah_beli' => [$request->jumlah_beli],
                'harga_beli_hpp' => [$request->harga_beli_hpp],
            ]);
        }

        $request->validate([
            'nama_supplier'  => 'required|string|max:255',
            'barang_id.*'      => 'required|exists:barangs,id',
            'jumlah_beli.*'    => 'required|integer|min:1',
            'harga_beli_hpp.*' => 'required|numeric|min:0',
            'foto_invoice'     => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $datePrefix = 'PO-' . date('Ymd') . '-';
            
            $barangIds = $request->barang_id;
            $jumlahBelis = $request->jumlah_beli;
            $hargaBelis = $request->harga_beli_hpp;
            
            $fotoPath = null;
            if ($request->hasFile('foto_invoice')) {
                $fotoPath = $request->file('foto_invoice')->store('pembelian_invoices', 'public');
            }
            
            $lastPo = Pembelian::where('no_pembelian', 'like', $datePrefix . '%')->orderBy('no_pembelian', 'desc')->first();
            $newPoNum = $lastPo ? intval(substr($lastPo->no_pembelian, -4)) + 1 : 1;
            $no_pembelian = $datePrefix . str_pad($newPoNum, 4, '0', STR_PAD_LEFT);
            
            for ($i = 0; $i < count($barangIds); $i++) {
                $qty = $jumlahBelis[$i];
                $hpp = $hargaBelis[$i];
                $total_bayar = $qty * $hpp;

                $pembelian = Pembelian::create([
                    'no_pembelian'   => $no_pembelian,
                    'nama_supplier'  => strtoupper(trim($request->nama_supplier)),
                    'barang_id'      => $barangIds[$i],
                    'jumlah_beli'    => $qty,
                    'harga_beli_hpp' => $hpp,
                    'total_bayar'    => $total_bayar,
                    'tanggal_beli'   => date('Y-m-d'),
                    'status_barang'  => 'pending',
                    'foto_invoice'   => $fotoPath,
                ]);

                $barang = Barang::where('id', $barangIds[$i])->lockForUpdate()->first();
                if ($barang && $barang->harga_beli != $hpp) {
                    $oldHpp = $barang->harga_beli;
                    
                    $barang->harga_beli = $hpp;
                    $barang->save();

                    \App\Models\StockHistory::record(
                        $barang,
                        0,
                        'edit_data',
                        $no_pembelian,
                        "Update HPP via PO: Rp " . number_format($oldHpp, 0, ',', '.') . " -> Rp " . number_format($hpp, 0, ',', '.')
                    );
                }
            }
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'TAMBAH PEMBELIAN',
                'description' => Auth::user()->name . ' membuat dokumen Purchase Order (PO) baru: ' . $no_pembelian,
                'ip_address' => request()->ip(),
            ]);

            DB::commit();
            return back()->with('success', "PO {$no_pembelian} berhasil dibuat. Silakan lakukan proses SORTIR saat fisik barang tiba di gudang!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function prosesSortir(Request $request, $no_pembelian)
    {
        $request->validate([
            'pembelian_ids'   => 'required|array',
            'qty_bagus'       => 'required|array',
            'qty_rusak'       => 'required|array',
            'qty_kurang'      => 'required|array',
            'qty_bagus.*'     => 'integer|min:0',
            'qty_rusak.*'     => 'integer|min:0',
            'qty_kurang.*'    => 'integer|min:0',
        ]);

        $pembelians = Pembelian::where('no_pembelian', $no_pembelian)->get();
        if ($pembelians->isEmpty()) {
            return back()->withErrors(['error' => 'Data PO tidak ditemukan.']);
        }
        
        if ($pembelians->first()->status_barang === 'selesai') {
            return back()->withErrors(['error' => 'Gagal! PO ini sudah disortir sebelumnya.']);
        }

        foreach($request->pembelian_ids as $id) {
            $pembelian = $pembelians->firstWhere('id', $id);
            if (!$pembelian) continue;
            
            $bagus = $request->qty_bagus[$id] ?? 0;
            $rusak = $request->qty_rusak[$id] ?? 0;
            $kurang = $request->qty_kurang[$id] ?? 0;
            
            $totalCek = (int)$bagus + (int)$rusak + (int)$kurang;
            if ($totalCek != (int)$pembelian->jumlah_beli) {
                return back()->withErrors(['error' => "Total sortir untuk barang {$pembelian->barang->nama_barang} ({$totalCek}) tidak sama dengan jumlah order awal ({$pembelian->jumlah_beli}). Pastikan perhitungannya pas!"]);
            }
        }

        try {
            DB::beginTransaction();

            $totalSemuaPO = 0;
            $adaRetur = false;
            $supplierName = $pembelians->first()->nama_supplier;
            
            $noReturAuto = 'RE-QC-' . date('Ymd') . rand(1000, 9999);

            foreach($request->pembelian_ids as $id) {
                $pembelian = $pembelians->firstWhere('id', $id);
                if (!$pembelian) continue;
                
                $bagus = $request->qty_bagus[$id] ?? 0;
                $rusak = $request->qty_rusak[$id] ?? 0;
                $kurang = $request->qty_kurang[$id] ?? 0;

                $pembelian->update([
                    'status_barang' => 'selesai',
                    'qty_bagus'     => $bagus,
                    'qty_rusak'     => $rusak,
                    'qty_kurang'    => $kurang,
                ]);

                $barang = Barang::where('id', $pembelian->barang_id)->lockForUpdate()->firstOrFail();
                $hargaBeliBaru = $pembelian->harga_beli_hpp;
                
                $totalSemuaPO += $pembelian->total_bayar;

                if ($bagus > 0) {
                    $stokAkhirLama = $barang->stok_akhir ?? 0;
                    $barang->update([
                        'barang_masuk' => $barang->barang_masuk + $bagus,
                        'stok_akhir'   => $stokAkhirLama + $bagus,
                        'harga_beli'   => $hargaBeliBaru
                    ]);
                    StockHistory::record($barang, $bagus, 'purchase', $no_pembelian, 'Penerimaan QC: Barang Bagus.', $stokAkhirLama);
                }

                if ($rusak > 0) {
                    $stokRusakLama = $barang->stok_rusak ?? 0;
                    $barang->update([
                        'stok_rusak' => $stokRusakLama + $rusak
                    ]);
                    StockHistory::record($barang, $rusak, 'purchase', $no_pembelian, 'Penerimaan QC: Barang Cacat/Rusak.', $stokRusakLama);
                }

                if ($request->has('potong_tagihan') && ($rusak > 0 || $kurang > 0)) {
                    $adaRetur = true;
                    $totalQtyBermasalah = $rusak + $kurang;
                    $nominalPotongan = $totalQtyBermasalah * $hargaBeliBaru;
                    
                    $alasan = "Klaim Otomatis Hasil QC: Terdapat {$rusak} rusak, {$kurang} kurang/hilang dari total order {$pembelian->jumlah_beli} Pcs.";

                    DB::table('returs')->insert([
                        'no_retur' => $noReturAuto, 
                        'tipe' => 'pembelian', 
                        'jenis_retur' => ($rusak > 0) ? 'fisik' : 'harga_debit_note',
                        'referensi_id' => $pembelian->id, 
                        'barang_id' => $barang->id, 
                        'qty' => $totalQtyBermasalah,
                        'kondisi' => ($rusak > 0) ? 'rusak' : 'tidak_mempengaruhi', 
                        'nominal_potongan' => $nominalPotongan,
                        'status_retur' => 'pending',
                        'alasan' => $alasan, 
                        'created_at' => now(), 
                        'updated_at' => now(),
                    ]);
                }
            }

            $firstPembelianId = $pembelians->first()->id;
            if (!Utang::where('pembelian_id', $firstPembelianId)->exists()) {
                $noUtangJurnal = 'UTG-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                
                $supplierMaster = \App\Models\Supplier::whereRaw('LOWER(nama_supplier) = ?', [strtolower($supplierName)])->first();
                $jatuhTempoHari = $supplierMaster && $supplierMaster->jatuh_tempo_hari !== null 
                                    ? $supplierMaster->jatuh_tempo_hari 
                                    : 30;

                Utang::create([
                    'no_utang_jurnal'     => $noUtangJurnal,
                    'pembelian_id'        => $firstPembelianId,
                    'total_utang'         => $totalSemuaPO,
                    'total_dibayar'       => 0,
                    'status_bayar'        => 'belum_bayar',
                    'tanggal_jatuh_tempo' => date('Y-m-d', strtotime('+' . $jatuhTempoHari . ' days')),
                ]);
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'UPDATE STATUS PEMBELIAN',
                'description' => Auth::user()->name . ' menyelesaikan proses QC/Sortir masal untuk PO: ' . $no_pembelian,
                'ip_address' => request()->ip(),
            ]);

            DB::commit();

            if ($request->has('potong_tagihan') && $adaRetur) {
                return back()->with('success', "Proses QC Selesai! Stok Bagus & Rusak telah di-update. (Draf Klaim Retur/Utang telah disiapkan dan menunggu persetujuan).");
            } else {
                return back()->with('success', "Proses QC Selesai! Semua stok bagus telah dimasukkan ke gudang.");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal Memproses Sortir: ' . $e->getMessage()]);
        }
    }
}