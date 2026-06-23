<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\StockHistory;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Barang::query();

        if ($search) {
            $query->where('kode_barang', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%");
        }

        $barangs = $query->orderBy('kode_barang', 'asc')->paginate(50)->withQueryString();
        
        return view('barang.index', compact('barangs', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'spesifikasi' => 'nullable|string|max:255',
            'merek'       => 'nullable|string|max:255',
            'harga_beli'  => 'required|numeric|min:0',
            'harga_jual'  => 'required|numeric|min:0',
            'stok_akhir'  => 'required|numeric|min:0',
            'stok_rusak'  => 'nullable|numeric|min:0',
        ]);

        $barang = Barang::create([
            'kode_barang'    => strtoupper(trim($request->kode_barang)),
            'nama_barang'    => trim($request->nama_barang),
            'spesifikasi'    => trim($request->spesifikasi),
            'merek'          => trim($request->merek),
            'harga_beli'     => $request->harga_beli,
            'harga_jual'     => $request->harga_jual,
            'stok_awal'      => $request->stok_akhir,
            'barang_masuk'   => $request->stok_akhir, 
            'barang_keluar'  => 0,
            'stok_akhir'     => $request->stok_akhir,
            'stok_rusak'     => $request->stok_rusak ?? 0,
            'tanggal_update' => date('Y-m-d H:i:s')
        ]);

        if ($request->stok_akhir > 0) {
            StockHistory::record(
                $barang,
                $request->stok_akhir,
                'initial_stock',
                'BARANG-'.$barang->id,
                'Inisialisasi stok awal barang saat ditambahkan.',
                0
            );
        }

        if ($request->stok_rusak > 0) {
            StockHistory::record(
                $barang,
                $request->stok_rusak,
                'initial_stock',
                'BARANG-'.$barang->id,
                'Inisialisasi stok rusak awal saat ditambahkan.',
                0
            );
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'TAMBAH BARANG',
            'description' => Auth::user()->name . ' menambahkan master barang baru: ' . $barang->kode_barang . ' - ' . $barang->nama_barang,
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'Barang baru dengan format SKU terstandarisasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    public function history($id)
    {
        $barang = Barang::with('stockHistories')->findOrFail($id);
        
        $histories = $barang->stockHistories()->orderBy('created_at', 'asc')->get();
        
        return view('barang.history', compact('barang', 'histories'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang,'.$id,
            'nama_barang' => 'required|string|max:255',
            'spesifikasi' => 'nullable|string|max:255',
            'merek'       => 'nullable|string|max:255',
            'harga_beli'  => 'required|numeric|min:0',
            'harga_jual'  => 'required|numeric|min:0',
            'stok_akhir'  => 'required|numeric|min:0', // <--- Stok Bagus
            'stok_rusak'  => 'required|numeric|min:0', // <--- Stok Rusak
        ]);

        $barang = Barang::findOrFail($id);
        
        $stokBagusSebelumnya = $barang->stok_akhir;
        $stokRusakSebelumnya = $barang->stok_rusak;

        // 2. Cek perubahan data (selain stok)
        $barang->fill([
            'kode_barang'    => strtoupper(trim($request->kode_barang)),
            'nama_barang'    => trim($request->nama_barang),
            'spesifikasi'    => trim($request->spesifikasi),
            'merek'          => trim($request->merek),
            'harga_beli'     => $request->harga_beli,
            'harga_jual'     => $request->harga_jual,
            'stok_akhir'     => $request->stok_akhir, // Simpan stok bagus manual
            'stok_rusak'     => $request->stok_rusak, // Simpan stok rusak manual
            'tanggal_update' => date('Y-m-d H:i:s')
        ]);

        $changes = [];
        if ($barang->isDirty('nama_barang')) $changes[] = "Nama ({$barang->getOriginal('nama_barang')} -> {$barang->nama_barang})";
        if ($barang->isDirty('harga_beli')) $changes[] = "HPP ({$barang->getOriginal('harga_beli')} -> {$barang->harga_beli})";
        if ($barang->isDirty('harga_jual')) $changes[] = "Harga Jual ({$barang->getOriginal('harga_jual')} -> {$barang->harga_jual})";
        if ($barang->isDirty('kode_barang')) $changes[] = "SKU ({$barang->getOriginal('kode_barang')} -> {$barang->kode_barang})";
        if ($barang->isDirty('spesifikasi')) $changes[] = "Spek diperbarui";
        if ($barang->isDirty('merek')) $changes[] = "Merek ({$barang->getOriginal('merek')} -> {$barang->merek})";

        $barang->save();

        if (count($changes) > 0) {
            StockHistory::record(
                $barang,
                0,
                'edit_data',
                'BARANG-'.$barang->id,
                "Edit Data: " . implode(', ', $changes),
                $stokBagusSebelumnya
            );
        }

        // 3. Catat di history jika ada perubahan manual pada stok bagus
        if ($stokBagusSebelumnya != $request->stok_akhir) {
            $selisihBagus = $request->stok_akhir - $stokBagusSebelumnya;
            StockHistory::record(
                $barang,
                $selisihBagus,
                'manual_adjustment',
                'BARANG-'.$barang->id,
                "Penyesuaian manual Stok Bagus (Dari {$stokBagusSebelumnya} menjadi {$request->stok_akhir})",
                $stokBagusSebelumnya
            );
        }

        // Catat di history jika ada perubahan manual pada stok rusak
        if ($stokRusakSebelumnya != $request->stok_rusak) {
            $selisihRusak = $request->stok_rusak - $stokRusakSebelumnya;
            StockHistory::record(
                $barang,
                $selisihRusak,
                'manual_adjustment',
                'BARANG-'.$barang->id,
                "Penyesuaian manual Stok Rusak (Dari {$stokRusakSebelumnya} menjadi {$request->stok_rusak})",
                $stokRusakSebelumnya
            );
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'EDIT BARANG',
            'description' => Auth::user()->name . ' memperbarui data barang: ' . $barang->kode_barang,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('barang.index')->with('success', 'Data barang dan stok fisik berhasil diperbarui secara manual.');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);

        $kodeBarang = $barang->kode_barang;
        $namaBarang = $barang->nama_barang;
        $barang->delete(); // Soft delete

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'HAPUS BARANG',
            'description' => Auth::user()->name . ' menghapus (soft delete) data barang: ' . $kodeBarang . ' - ' . $namaBarang,
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'Barang berhasil dihapus dari daftar aktif.');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file_csv');
        $fileContent = file_get_contents($file->getRealPath());
        $lines = preg_split('/\r\n|\r|\n/', $fileContent);
        
        $lines = array_filter($lines, function($line) {
            return trim($line) !== '';
        });
        
        $lines = array_values($lines);

        if (count($lines) <= 1) {
            return back()->withErrors(['error' => 'File CSV kosong atau hanya berisi baris judul (header).']);
        }

        $headerLine = $lines[0];
        $delimiter = strpos($headerLine, ';') !== false ? ';' : ',';
        $barisBerhasil = 0;

        try {
            DB::beginTransaction();

            for ($i = 1; $i < count($lines); $i++) {
                $data = str_getcsv($lines[$i], $delimiter);

                if (count($data) < 2 || empty(trim($data[0]))) {
                    continue;
                }

                // Format Baru: kode_barang(0), nama_barang(1), spesifikasi(2), merek(3), harga_beli(4), harga_jual(5), stok_bagus(6), stok_rusak(7)
                $kodeBarang  = strtoupper(trim($data[0]));
                $namaBarang  = trim($data[1]);
                $spesifikasi = isset($data[2]) ? trim($data[2]) : null;
                $merek       = isset($data[3]) ? trim($data[3]) : null;
                
                $hargaBeliMentah = isset($data[4]) ? preg_replace('/[^0-9.]/', '', $data[4]) : 0;
                $hargaBeli       = floatval($hargaBeliMentah);

                $hargaJualMentah = isset($data[5]) ? preg_replace('/[^0-9.]/', '', $data[5]) : 0;
                $hargaJual       = floatval($hargaJualMentah);
                
                $stokBagus   = isset($data[6]) ? intval(trim($data[6])) : 0;
                $stokRusak   = isset($data[7]) ? intval(trim($data[7])) : 0;

                $barangExisting = Barang::where('kode_barang', $kodeBarang)->first();

                if ($barangExisting) {
                    $stokSebelumnya = $barangExisting->stok_akhir;
                    $stokRusakSebelumnya = $barangExisting->stok_rusak;

                    $barangExisting->update([
                        'nama_barang'    => $namaBarang,
                        'spesifikasi'    => $spesifikasi,
                        'merek'          => $merek,
                        'harga_beli'     => $hargaBeli,
                        'harga_jual'     => $hargaJual,
                        'barang_masuk'   => $barangExisting->barang_masuk + $stokBagus,
                        'stok_akhir'     => $barangExisting->stok_akhir + $stokBagus,
                        'stok_rusak'     => $barangExisting->stok_rusak + $stokRusak,
                        'tanggal_update' => date('Y-m-d H:i:s')
                    ]);

                    if ($stokBagus !== 0) {
                        StockHistory::record(
                            $barangExisting,
                            $stokBagus,
                            'import_csv',
                            $kodeBarang,
                            'Penambahan stok bagus dari impor CSV.',
                            $stokSebelumnya
                        );
                    }
                    if ($stokRusak !== 0) {
                        StockHistory::record(
                            $barangExisting,
                            $stokRusak,
                            'import_csv',
                            $kodeBarang,
                            'Penambahan stok rusak dari impor CSV.',
                            $stokRusakSebelumnya
                        );
                    }
                } else {
                    $barang = Barang::create([
                        'kode_barang'    => $kodeBarang,
                        'nama_barang'    => $namaBarang,
                        'spesifikasi'    => $spesifikasi,
                        'merek'          => $merek,
                        'harga_beli'     => $hargaBeli,
                        'harga_jual'     => $hargaJual,
                        'stok_awal'      => $stokBagus,
                        'barang_masuk'   => $stokBagus,
                        'barang_keluar'  => 0,
                        'stok_akhir'     => $stokBagus,
                        'stok_rusak'     => $stokRusak,
                        'tanggal_update' => date('Y-m-d H:i:s')
                    ]);

                    if ($stokBagus > 0) {
                        StockHistory::record(
                            $barang,
                            $stokBagus,
                            'initial_stock',
                            $kodeBarang,
                            'Inisialisasi stok bagus dari impor CSV.',
                            0
                        );
                    }
                    if ($stokRusak > 0) {
                        StockHistory::record(
                            $barang,
                            $stokRusak,
                            'initial_stock',
                            $kodeBarang,
                            'Inisialisasi stok rusak dari impor CSV.',
                            0
                        );
                    }
                }
                
                $barisBerhasil++;
            }

            DB::commit();
            
            if ($barisBerhasil > 0) {
                return back()->with('success', "Berhasil! $barisBerhasil data barang dengan SKU, Harga, & Stok lengkap telah disinkronkan.");
            } else {
                return back()->withErrors(['error' => 'Gagal membaca data! Pastikan susunan kolom CSV Anda sudah sesuai petunjuk format.']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses data CSV: ' . $e->getMessage()]);
        }
    }
}