<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\Piutang;
use App\Models\StockHistory;
use App\Models\BackOrder; 
use App\Models\ActivityLog; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    // =========================================================================
    // 1. FUNGSI "OTAK" SPK (SAW Method)
    // =========================================================================
    private function hitungSkorSpk($penjualan) 
    {
        $penjualan = $penjualan->fresh(['details.barang', 'customer']);
        
        $akumulasiSkor = 0;
        $count = $penjualan->details->count();

        if ($count == 0) return 0; 

        foreach ($penjualan->details as $detail) {
            $barang = $detail->barang;
            if (!$barang) continue;
            
            $hargaNetto = $detail->harga_satuan - ($detail->diskon ?? 0);
            $profitMargin = (($hargaNetto - $barang->harga_beli) / ($barang->harga_beli ?: 1)) * 100;
            
            $skorMargin = ($profitMargin >= 20) ? 100 : (($profitMargin >= 5) ? 60 : 20);
            
            $persenSisa = (($barang->stok_akhir - $detail->jumlah) / ($barang->stok_awal ?: 1)) * 100;
            $skorStok = ($persenSisa >= 20) ? 100 : ($persenSisa >= 5 ? 50 : 10);
            
            $skorCust = ($penjualan->customer->npwp && $penjualan->customer->ktp) ? 100 : 50;
            
            $trackRecord = Penjualan::where('user_id', $penjualan->user_id)
                                    ->where('status_approval', 'disetujui')->count();
            $skorSales = ($trackRecord >= 5) ? 100 : 50;

            $akumulasiSkor += ($skorMargin * 0.4) + ($skorStok * 0.3) + ($skorCust * 0.2) + ($skorSales * 0.1);
        }

        $rataRataSkor = ($akumulasiSkor / $count);
        
        $penjualan->skor_spk = $rataRataSkor;
        $penjualan->save();

        return $rataRataSkor; 
    }

    // =========================================================================
    // 2. TAMPILAN INDEX & SHOW
    // =========================================================================
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Penjualan::with(['customer', 'user', 'details.barang']);
        $role = strtolower(Auth::user()->role);
        
        if (in_array($role, ['sales', 'marketing'])) {
            $query->where('user_id', Auth::id());
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_so', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('nama_customer', 'like', "%{$search}%");
                  });
            });
        }
        
        $pengajuan = $query->orderBy('created_at', 'asc')->paginate(50)->withQueryString();
        return view('penjualan.index', compact('pengajuan', 'search'));
    }

    public function show($id)
    {
        $penjualan = Penjualan::with(['customer', 'details.barang', 'user'])->findOrFail($id);
        $role = strtolower(Auth::user()->role);

        if (in_array($role, ['sales', 'marketing']) && $penjualan->user_id != Auth::id()) {
            abort(403, 'Akses ditolak. Anda hanya dapat melihat dokumen milik Anda sendiri.');
        }
        
        return view('penjualan.show', compact('penjualan'));
    }

    public function create()
    {
        $barangs = Barang::orderBy('kode_barang', 'asc')->get(); 
        $customers = Customer::orderBy('nama_customer', 'asc')->get();
        return view('penjualan.buat', compact('barangs', 'customers'));
    }

    // =========================================================================
    // 3. BUAT PESANAN BARU (STATUS AWAL: DRAFT)
    // =========================================================================
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'barang_id' => 'required|array',
            'jumlah' => 'required|array',
            'harga_satuan' => 'required|array',
        ]);

        try {
            DB::beginTransaction();
            
            $customer = Customer::findOrFail($request->customer_id);
            if ($request->npwp || $request->ktp) {
                $customer->update([
                    'npwp' => $request->npwp ?? $customer->npwp,
                    'ktp' => $request->ktp ?? $customer->ktp,
                ]);
            }

            $lastId = Penjualan::max('id') ?? 0;
            $no_so = 'SO-' . date('Ymd') . '-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
            
            $penjualan = Penjualan::create([
                'no_so' => $no_so,
                'tanggal_order' => date('Y-m-d'),
                'customer_id' => $customer->id,
                'user_id' => Auth::id(),
                'total_semua' => 0,
                'status_approval' => 'pending',
                'status' => 'draft',
                'sales_created_at' => now(),
            ]);

            $total_semua = 0;
            foreach ($request->barang_id as $index => $id_barang) {
                $barang = Barang::findOrFail($id_barang);
                $diskonItem = $request->diskon[$index] ?? 0;
                $subtotal = $request->jumlah[$index] * ($request->harga_satuan[$index] - $diskonItem);
                $total_semua += $subtotal;

                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'barang_id' => $barang->id,
                    'hpp' => $barang->harga_beli ?? 0, // <--- KUNCI HPP DIAMBIL DARI MASTER BARANG SAAT INI
                    'harga_satuan' => $request->harga_satuan[$index],
                    'diskon' => $diskonItem,
                    'jumlah' => $request->jumlah[$index],
                    'subtotal' => $subtotal,
                ]);
            }

            $penjualan->total_semua = $total_semua;
            $penjualan->save();
            
            $skorFinal = $this->hitungSkorSpk($penjualan);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'BUAT SO',
                'description' => Auth::user()->name . ' membuat dokumen Sales Order baru: ' . $penjualan->no_so,
                'ip_address' => $request->ip(),
            ]);

            DB::commit();
            
            return redirect()->route('penjualan.index')
                             ->with('success', "Order berhasil diajukan dengan skor kelayakan " . number_format($skorFinal, 0) . "%");
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // =========================================================================
    // 4. APPROVAL DIREKTUR 
    // =========================================================================
    public function approvalList()
    {
        $pengajuan = Penjualan::with(['customer', 'details.barang', 'user'])->where('status_approval', 'pending')->orderBy('created_at', 'asc')->get();
        return view('penjualan.approval', compact('pengajuan'));
    }

    public function approve(Request $request, $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        
        try {
            DB::beginTransaction();

            if ($request->status == 'disetujui' && $request->has('harga_edit')) {
                $total_keseluruhan_baru = 0;
                foreach ($request->harga_edit as $detail_id => $harga_baru) {
                    $detail = PenjualanDetail::findOrFail($detail_id);
                    $detail->harga_satuan = $harga_baru; 
                    
                    if ($request->has('jumlah_edit') && isset($request->jumlah_edit[$detail_id])) {
                        $detail->jumlah = $request->jumlah_edit[$detail_id];
                    }

                    $subtotal_baru = $detail->jumlah * $harga_baru;
                    $detail->subtotal = $subtotal_baru;
                    $detail->save();
                    
                    $total_keseluruhan_baru += $subtotal_baru;
                }
                $penjualan->total_semua = $total_keseluruhan_baru;
            }

            if ($request->status == 'disetujui') {
                // Check if user is manager and it is over limit
                if (strtolower(Auth::user()->role) === 'manager') {
                    $sisa_limit = $penjualan->customer->sisa_plafon ?? 0;
                    if ($penjualan->total_semua > $sisa_limit) {
                        throw new \Exception('Akun staf dengan role Manager tidak diizinkan menyetujui pesanan yang melebihi limit kredit!');
                    }
                }

                $penjualan->status_approval = 'disetujui';
                $penjualan->status = 'diproses';
                $penjualan->approved_at = now();
                $penjualan->catatan = $request->catatan;
            } else {
                $penjualan->status_approval = 'ditolak';
                $penjualan->catatan = $request->catatan;
            }
            
            $penjualan->save();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => ($request->status == 'disetujui') ? 'SETUJUI SO' : 'TOLAK SO',
                'description' => Auth::user()->name . ' telah ' . strtoupper($request->status) . ' dokumen: ' . $penjualan->no_so,
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return back()->with('success', 'Keputusan berhasil disimpan! Silakan lanjut ke proses Packing.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses persetujuan: ' . $e->getMessage()]);
        }
    }

    // =========================================================================
    // 5. FUNGSI SEND TO BACKORDER (KIRIM SO KE ANTREAN BO KARENA STOK KOSONG TOTAL)
    // =========================================================================
    public function sendToBackorder($id)
    {
        $penjualan = Penjualan::with('details')->findOrFail($id);

        if ($penjualan->status_approval !== 'disetujui') {
            return back()->with('error', 'Gagal: Order harus disetujui terlebih dahulu.');
        }

        // Cegah duplikasi jika sudah di-backorder
        if ($penjualan->status === 'menunggu_restock') {
            return redirect()->route('backorder.index')->with('success', 'Order sudah berada di antrean Back Order.');
        }

        try {
            DB::beginTransaction();

            $penjualan->status = 'menunggu_restock';
            $penjualan->save();

            foreach ($penjualan->details as $detail) {
                $barang = Barang::findOrFail($detail->barang_id);
                
                // Jika butuh dikirim, langsung jadikan backorder semuanya karena 0 shippable total
                if ($detail->jumlah > 0) {
                    BackOrder::create([
                        'penjualan_id' => $penjualan->id,
                        'barang_id' => $barang->id,
                        'jumlah_diminta' => $detail->jumlah, 
                        'jumlah_kurang' => $detail->jumlah,
                        'status_bo' => 'antrean'
                    ]);
                }
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'KIRIM KE BO',
                'description' => Auth::user()->name . ' memindahkan SO ' . $penjualan->no_so . ' ke antrean Back Order karena stok kosong.',
                'ip_address' => request()->ip(),
            ]);

            DB::commit();

            return redirect()->route('backorder.index')->with('success', 'Order telah dipindahkan ke antrean Back Order. Silakan lakukan restock sebelum mem-packing.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses back order: ' . $e->getMessage()]);
        }
    }

    // =========================================================================
    // 6. FUNGSI WAREHOUSE PACKING (MEMOTONG STOK & MEMBUAT BACK ORDER PARSIAL)
    // =========================================================================
    public function updateToPackingSelesai($id)
    {
        // Gunakan lockForUpdate untuk mencegah Race Condition (Double Packing)
        $penjualan = Penjualan::with('details')->lockForUpdate()->findOrFail($id);
        
        if ($penjualan->status_approval !== 'disetujui') {
            return back()->with('error', 'Gagal: Order harus disetujui oleh Direktur terlebih dahulu.');
        }

        if ($penjualan->status === 'ready_to_invoice') {
             return back()->with('error', 'Gagal: Barang ini sudah dipacking sebelumnya.');
        }

        try {
            DB::beginTransaction();

            // Ambil data BackOrder yang masih mengantre untuk SO ini
            $activeBackorders = BackOrder::where('penjualan_id', $penjualan->id)->where('status_bo', 'antrean')->get();

            $penjualan->status = 'ready_to_invoice';
            $penjualan->save();
            
            $adaBackOrder = false;
            $totalTagihanAwal = 0; // Tambahan: Hitung total tagihan hanya untuk barang yang terkirim

            // Create Pengiriman record
            $noSOBase = str_replace('SO-', '', $penjualan->no_so);
            $noINVBase = str_replace('SO', 'INV', $penjualan->no_so);
            $pengiriman = \App\Models\Pengiriman::create([
                'penjualan_id' => $penjualan->id,
                'no_pengiriman' => 'SJ-' . $noSOBase . '-1',
                'no_invoice' => $noINVBase . '-1',
                'tanggal_kirim' => now()->toDateString(),
                'plat_kendaraan' => null,
                'catatan' => 'Pengiriman Tahap 1 (Parsial/Full)',
            ]);

            foreach ($penjualan->details as $detail) {
                // Kunci baris barang agar stok tidak direbut oleh transaksi lain yang berjalan bersamaan (Race Condition)
                $barang = Barang::where('id', $detail->barang_id)->lockForUpdate()->firstOrFail();
                $stokSebelumnya = $barang->stok_akhir;

                // Logika Parsial: Kirim seadanya stok saat ini
                $qtyDikirimSekarang = ($barang->stok_akhir >= $detail->jumlah) ? $detail->jumlah : max(0, $barang->stok_akhir);
                $jumlahKurang = $detail->jumlah - $qtyDikirimSekarang;
                
                // Tambahan: Tambahkan nilai barang yang benar-benar terkirim saat ini ke Piutang
                $totalTagihanAwal += ($qtyDikirimSekarang * $detail->harga_satuan);

                $existingBo = $activeBackorders->where('barang_id', $barang->id)->first();

                if ($jumlahKurang > 0) {
                    if ($existingBo) {
                        $existingBo->update(['jumlah_kurang' => $jumlahKurang]);
                    } else {
                        BackOrder::create([
                            'penjualan_id' => $penjualan->id,
                            'barang_id' => $barang->id,
                            'jumlah_diminta' => $detail->jumlah, 
                            'jumlah_kurang' => $jumlahKurang,
                            'status_bo' => 'antrean'
                        ]);
                    }
                    $adaBackOrder = true;
                } else {
                    if ($existingBo) {
                        $existingBo->update(['status_bo' => 'terpenuhi']);
                    }
                }

                if ($qtyDikirimSekarang > 0) {
                    $barang->update([
                        'stok_akhir'   => $barang->stok_akhir - $qtyDikirimSekarang,
                        'barang_keluar' => $barang->barang_keluar + $qtyDikirimSekarang,
                    ]);

                    StockHistory::record(
                        $barang,
                        -$qtyDikirimSekarang,
                        'sale',
                        $penjualan->no_so,
                        'Pengiriman Packing (Parsial/Full).',
                        $stokSebelumnya
                    );

                    \App\Models\PengirimanDetail::create([
                        'pengiriman_id' => $pengiriman->id,
                        'barang_id' => $barang->id,
                        'jumlah_kirim' => $qtyDikirimSekarang,
                        'harga_satuan' => $detail->harga_satuan,
                        'subtotal' => $qtyDikirimSekarang * $detail->harga_satuan,
                    ]);
                }
            }

            $no_invoice = str_replace('SO', 'INV', $penjualan->no_so);
            
            Piutang::firstOrCreate(
                ['no_invoice' => $no_invoice], 
                [
                    'penjualan_id' => $penjualan->id,
                    'total_tagihan' => $totalTagihanAwal, // Diubah agar sesuai jumlah terkirim
                    'potongan' => 0, 
                    'total_dibayar' => 0,
                    'status_bayar' => 'belum_bayar',
                    'jatuh_tempo' => \Carbon\Carbon::now()->addDays((int)($penjualan->customer->tempo_hari ?? 30)),
                    'diinput_by' => Auth::id(),
                ]
            );

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'UPDATE STATUS',
                'description' => Auth::user()->name . ' menyelesaikan proses packing untuk SO: ' . $penjualan->no_so,
                'ip_address' => request()->ip(),
            ]);
            
            DB::commit();

            if ($adaBackOrder) {
                return back()->with('success', 'Packing Parsial berhasil! Stok yang ada sudah dipotong. Barang yang kurang otomatis masuk ke antrean Back Order.');
            } else {
                return back()->with('success', 'Packing Selesai! Semua pesanan lengkap dan stok telah berhasil dipotong 100%.');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses packing: ' . $e->getMessage()]);
        }
    }

    // =========================================================================
    // 6. PRINT SURAT & FAKTUR
    // =========================================================================
    public function printFaktur($id)
    {
        if (strtolower(Auth::user()->role) === 'sales') {
            abort(403, 'Akses Ditolak: Sales tidak diizinkan mencetak dokumen faktur/surat jalan.');
        }
        $penjualan = Penjualan::findOrFail($id);
        if ($penjualan->status === 'draft') abort(403, 'Akses Ditolak: Pesanan masih draft dan tidak dapat dicetak.');
        $pengiriman = \App\Models\Pengiriman::where('penjualan_id', $id)->orderBy('id', 'asc')->first();
        if (!$pengiriman) abort(404, 'Pengiriman tidak ditemukan.');

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'CETAK FAKTUR',
            'description' => Auth::user()->name . ' mencetak Faktur untuk SO: ' . $penjualan->no_so,
            'ip_address' => request()->ip(),
        ]);

        return view('penjualan.faktur_print', compact('pengiriman'));
    }

    public function printSuratJalan($id)
    {
        if (strtolower(Auth::user()->role) === 'sales') {
            abort(403, 'Akses Ditolak: Sales tidak diizinkan mencetak dokumen faktur/surat jalan.');
        }
        $penjualan = Penjualan::findOrFail($id);
        if ($penjualan->status === 'draft') abort(403, 'Akses Ditolak: Pesanan masih draft dan tidak dapat dicetak.');
        $pengiriman = \App\Models\Pengiriman::where('penjualan_id', $id)->orderBy('id', 'asc')->first();
        if (!$pengiriman) abort(404, 'Pengiriman tidak ditemukan.');

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'CETAK SURAT JALAN',
            'description' => Auth::user()->name . ' mencetak Surat Jalan untuk SO: ' . $penjualan->no_so,
            'ip_address' => request()->ip(),
        ]);

        return view('penjualan.surat_jalan_print', compact('pengiriman'));
    }

    public function printFakturPengiriman($id)
    {
        if (strtolower(Auth::user()->role) === 'sales') {
            abort(403, 'Akses Ditolak: Sales tidak diizinkan mencetak dokumen faktur/surat jalan.');
        }
        $pengiriman = \App\Models\Pengiriman::with(['penjualan.customer', 'details.barang'])->findOrFail($id);
        
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'CETAK FAKTUR PENGIRIMAN',
            'description' => Auth::user()->name . ' mencetak Faktur untuk Pengiriman: ' . $pengiriman->no_pengiriman,
            'ip_address' => request()->ip(),
        ]);

        return view('penjualan.faktur_print', compact('pengiriman'));
    }

    public function printSuratJalanPengiriman($id)
    {
        if (strtolower(Auth::user()->role) === 'sales') {
            abort(403, 'Akses Ditolak: Sales tidak diizinkan mencetak dokumen faktur/surat jalan.');
        }
        $pengiriman = \App\Models\Pengiriman::with(['penjualan.customer', 'details.barang'])->findOrFail($id);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'CETAK SURAT JALAN PENGIRIMAN',
            'description' => Auth::user()->name . ' mencetak Surat Jalan untuk Pengiriman: ' . $pengiriman->no_pengiriman,
            'ip_address' => request()->ip(),
        ]);

        return view('penjualan.surat_jalan_print', compact('pengiriman'));
    }

    // =========================================================================
    // 7. EDIT, UPDATE, DESTROY
    // =========================================================================
    public function edit($id)
    {
        $penjualan = Penjualan::with(['customer', 'details.barang'])->findOrFail($id);
        if (strtolower(Auth::user()->role) === 'sales' && $penjualan->user_id != Auth::id()) {
            return redirect()->route('penjualan.index')->with('error', 'Anda tidak berhak mengedit pesanan milik orang lain.');
        }
        if ($penjualan->status !== 'draft') return redirect()->route('penjualan.index')->with('error', 'Tidak dapat diedit.');
        return view('penjualan.edit', compact('penjualan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['harga_awal' => 'required|array', 'harga_satuan' => 'required|array', 'jumlah' => 'required|array']);
        try {
            DB::beginTransaction();
            $penjualan = Penjualan::findOrFail($id);
            if (strtolower(Auth::user()->role) === 'sales' && $penjualan->user_id != Auth::id()) {
                throw new \Exception('Anda tidak berhak mengedit pesanan milik orang lain.');
            }
            if ($penjualan->status !== 'draft') throw new \Exception('Pesanan sudah diproses.');
            $total_semua = 0;
            foreach ($request->detail_id as $index => $detailId) {
                $detail = PenjualanDetail::findOrFail($detailId);
                $diskonItem = $request->diskon[$index] ?? 0;
                $subtotal = $request->jumlah[$index] * ($request->harga_satuan[$index] - $diskonItem);
                $detail->update(['diskon' => $diskonItem, 'harga_satuan' => $request->harga_satuan[$index], 'jumlah' => $request->jumlah[$index], 'subtotal' => $subtotal]);
                $total_semua += $subtotal;
            }
            $penjualan->total_semua = $total_semua;
            $penjualan->save();
            
            $this->hitungSkorSpk($penjualan);
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'EDIT SO',
                'description' => Auth::user()->name . ' mengubah rincian draf Sales Order: ' . $penjualan->no_so,
                'ip_address' => $request->ip(),
            ]);

            DB::commit();
            return redirect()->route('penjualan.index')->with('success', 'Perubahan berhasil.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        
        if (strtolower(Auth::user()->role) === 'sales' && $penjualan->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak berhak membatalkan pesanan milik orang lain.');
        }

        if ($penjualan->status_approval !== 'pending') {
            return back()->with('error', 'Tidak dapat membatalkan Sales Order yang sudah disetujui atau ditolak.');
        }
        
        $penjualan->status_approval = 'dibatalkan';
        $penjualan->status = 'batal';
        $penjualan->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'BATAL SO',
            'description' => Auth::user()->name . ' membatalkan draf Sales Order: ' . $penjualan->no_so,
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'Sales Order berhasil dibatalkan.');
    }
}