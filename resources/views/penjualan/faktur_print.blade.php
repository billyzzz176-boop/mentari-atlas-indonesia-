<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Penjualan - {{ str_replace('SO', 'INV', $penjualan->no_so) }}</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #047857; /* Premium Emerald */
            --primary-light: #d1fae5;
            --slate-900: #0f172a;
            --slate-700: #334155;
            --slate-500: #64748b;
            --slate-200: #e2e8f0;
            --slate-50: #f8fafc;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            font-size: 13px; 
            color: var(--slate-900); 
            line-height: 1.5;
            background: #e2e8f0;
            margin: 0;
            padding: 40px 0;
        }
        
        .container { 
            width: 100%; 
            max-width: 850px; 
            margin: 0 auto; 
            background: #fff;
            padding: 50px 60px;
            box-sizing: border-box;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }

        /* Top Emerald Accent Bar */
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, var(--primary), #10b981);
        }

        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start;
            margin-bottom: 40px; 
            padding-bottom: 30px;
            border-bottom: 2px solid var(--slate-50);
        }

        .company-details h1 { 
            margin: 0 0 5px 0; 
            font-size: 26px; 
            color: var(--primary); 
            font-weight: 800; 
            letter-spacing: -0.5px; 
        }
        
        .company-details p {
            margin: 0;
            color: var(--slate-500);
            font-size: 12px;
            line-height: 1.6;
        }

        .invoice-details {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            text-align: right;
        }

        .invoice-details h2 { 
            margin: 0 12px 5px 0; 
            font-size: 32px; 
            color: var(--slate-900);
            font-weight: 800;
            letter-spacing: -1px;
            text-transform: uppercase;
        }
        
        .invoice-number {
            font-size: 14px;
            font-weight: 600;
            color: var(--slate-500);
            background: var(--slate-50);
            padding: 6px 12px;
            border-radius: 6px;
            display: inline-block;
        }

        .info-grid { 
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .info-box {
            background: var(--slate-50);
            padding: 20px;
            border-radius: 8px;
            border: 1px solid var(--slate-200);
        }

        .info-box h3 {
            margin: 0 0 15px 0;
            font-size: 11px;
            text-transform: uppercase;
            color: var(--slate-500);
            letter-spacing: 1px;
            font-weight: 700;
        }

        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .info-label { width: 40%; color: var(--slate-500); font-weight: 500; }
        .info-value { width: 60%; color: var(--slate-900); font-weight: 600; }

        .items-table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0;
            margin-bottom: 30px; 
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--slate-200);
        }
        
        .items-table th, .items-table td { 
            padding: 14px 16px; 
            text-align: left; 
            border-bottom: 1px solid var(--slate-200);
        }
        
        .items-table th { 
            background-color: var(--slate-50); 
            color: var(--slate-500);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            border-bottom: 2px solid var(--slate-200);
        }
        
        .items-table tr:last-child td { border-bottom: none; }
        .items-table tbody tr:nth-child(even) { background-color: #fdfdfd; }

        .text-center { text-align: center !important; }
        .text-end { text-align: right !important; }

        .summary-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 50px;
        }

        .totals-table { 
            width: 350px; 
            border-collapse: collapse; 
        }
        
        .totals-table td { 
            padding: 12px 16px; 
            border-bottom: 1px solid var(--slate-200);
            color: var(--slate-700);
        }
        
        .totals-table .grand-total td { 
            font-weight: 800; 
            font-size: 16px; 
            color: var(--primary);
            background-color: var(--primary-light);
            border-bottom: none;
            border-radius: 6px;
        }

        .grand-total td:first-child { border-radius: 6px 0 0 6px; }
        .grand-total td:last-child { border-radius: 0 6px 6px 0; }

        .notes-section {
            background: var(--slate-50);
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid var(--primary);
            margin-bottom: 40px;
        }

        .notes-section p {
            margin: 0;
            font-size: 12px;
            color: var(--slate-700);
            line-height: 1.6;
        }

        .signature { 
            width: 100%; 
            margin-top: 40px; 
        }
        .signature td { 
            text-align: center; 
            width: 50%; 
            color: var(--slate-700);
        }
        .signature-line { 
            margin-top: 80px; 
            border-top: 1px solid var(--slate-900); 
            display: inline-block; 
            width: 220px; 
            margin-bottom: 5px;
        }
        
        .bo-note { font-size: 11px; color: #d97706; font-style: italic; display: block; margin-top: 6px; background: #fef3c7; padding: 4px 8px; border-radius: 4px; }
        .bo-success { font-size: 11px; color: var(--primary); font-style: italic; display: block; margin-top: 6px; background: var(--primary-light); padding: 4px 8px; border-radius: 4px; font-weight: 600; }
        
        /* CSS UNTUK FITUR EDITABLE */
        .editable { transition: all 0.2s; border-bottom: 1px dashed transparent; border-radius: 3px; padding: 2px 4px; margin: -2px -4px;}
        .editable:hover { background-color: #fef08a; cursor: text; border-bottom: 1px dashed #ca8a04; }
        .editable:focus { outline: none; background-color: #fef9c3; border-bottom: 1px solid #ca8a04; box-shadow: 0 0 0 2px rgba(202, 138, 4, 0.2); }
        
        @media print {
            body { background: #fff; padding: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .container { box-shadow: none; max-width: 100%; padding: 0; }
            .no-print { display: none !important; }
            .editable:hover, .editable:focus { background-color: transparent; border-bottom: none; box-shadow: none; padding: 0; margin: 0; }
            .container::before { height: 6px; }
        }

        .controls-bar {
            text-align: right; 
            margin-bottom: 30px; 
            background: #fff; 
            padding: 15px 25px; 
            border-radius: 8px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-print {
            padding: 10px 24px; 
            background: var(--primary); 
            color: #fff; 
            cursor: pointer; 
            border: none; 
            border-radius: 6px; 
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            transition: all 0.2s;
        }
        .btn-print:hover { background: #059669; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); }
        
        .btn-back {
            padding: 10px 24px; 
            background: var(--slate-200); 
            color: var(--slate-700); 
            text-decoration: none; 
            margin-left: 12px; 
            border-radius: 6px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            transition: all 0.2s;
        }
        .btn-back:hover { background: #cbd5e1; color: var(--slate-900); }
    </style>
</head>
<body>
    <div style="max-width: 850px; margin: 0 auto;" class="no-print">
        <div class="controls-bar">
            <div style="display: flex; align-items: center; color: #b45309; font-size: 13px; font-weight: 500;">
                <span style="font-size: 18px; margin-right: 8px;">💡</span>
                Teks berlatar kuning samar dapat Anda ubah langsung sebelum dicetak.
            </div>
            <div>
                <a href="{{ route('penjualan.index') }}" class="btn-back">Kembali</a>
                <button onclick="window.print()" class="btn-print">Cetak Faktur</button>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="company-details">
                <h1>PT. MENTARI ATLAS INDONESIA</h1>
                <p>
                    <strong>Head Office:</strong> Jl. Jenderal Sudirman No. 123, Pontianak, Kalimantan Barat<br>
                    <strong>Phone:</strong> (0561) 123456 &nbsp;|&nbsp; <strong>Email:</strong> info@mentariprimasemesta.com
                </p>
            </div>
            <div class="invoice-details">
                <h2>INVOICE</h2>
                <div class="invoice-number">{{ str_replace('SO', 'INV', $penjualan->no_so) }}</div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h3>Informasi Pelanggan</h3>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Kepada Yth.</td>
                        <td class="info-value">{{ $penjualan->customer->nama_customer }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Alamat</td>
                        <td class="info-value"><span class="editable" contenteditable="true">{{ $penjualan->customer->alamat ?? 'Isi alamat lengkap di sini...' }}</span></td>
                    </tr>
                    <tr>
                        <td class="info-label">Telepon</td>
                        <td class="info-value"><span class="editable" contenteditable="true">{{ $penjualan->customer->no_telepon ?? '-' }}</span></td>
                    </tr>
                </table>
            </div>

            <div class="info-box">
                <h3>Detail Tagihan</h3>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Tanggal Invoice</td>
                        <td class="info-value"><span class="editable" contenteditable="true">{{ date('d M Y') }}</span></td>
                    </tr>
                    <tr>
                        <td class="info-label">Jatuh Tempo</td>
                        <td class="info-value"><span class="editable" contenteditable="true">{{ \Carbon\Carbon::now()->addDays(30)->format('d M Y') }}</span></td>
                    </tr>
                    <tr>
                        <td class="info-label">Referensi SO</td>
                        <td class="info-value">{{ $penjualan->no_so }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @php
            $backorders = \App\Models\BackOrder::where('penjualan_id', $penjualan->id)->get()->keyBy('barang_id');
        @endphp

        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="45%">Deskripsi Barang</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="20%" class="text-end">Harga Satuan (Rp)</th>
                    <th width="20%" class="text-end">Subtotal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualan->details as $index => $detail)
                @php
                    $bo = $backorders->get($detail->barang_id);
                    $qtyPesan = $detail->jumlah;
                    $statusTeks = 'normal';
                    
                    if($bo) {
                        $statusTeks = (strtolower($bo->status_bo) === 'terpenuhi' || strtolower($bo->status_bo) === 'selesai') ? 'lunas' : 'hutang';
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong style="color: var(--slate-900);">{{ $detail->barang->nama_barang }}</strong>
                        
                        @if($statusTeks === 'hutang')
                            <span class="bo-note editable" contenteditable="true">
                                <strong>PENTING:</strong> Pengiriman parsial (Dikirim: {{ $qtyPesan - $bo->jumlah_kurang }}, Sisa menunggu: {{ $bo->jumlah_kurang }}). Tagihan ini meliputi total keseluruhan pesanan.
                            </span>
                        @elseif($statusTeks === 'lunas')
                            <span class="bo-success editable" contenteditable="true">
                                ✔ Sisa Back Order sejumlah {{ $bo->jumlah_kurang }} telah dikirim. Komoditas lengkap.
                            </span>
                        @endif
                    </td>
                    <td class="text-center"><strong>{{ $qtyPesan }}</strong></td>
                    <td class="text-end"><span class="editable" contenteditable="true">{{ number_format($detail->harga_satuan, 0, ',', '.') }}</span></td>
                    <td class="text-end"><strong style="color: var(--slate-900);"><span class="editable" contenteditable="true">{{ number_format($detail->subtotal, 0, ',', '.') }}</span></strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-section">
            <table class="totals-table">
                <tr>
                    <td>Subtotal</td>
                    <td class="text-end"><span class="editable" contenteditable="true">{{ number_format($penjualan->total_semua, 0, ',', '.') }}</span></td>
                </tr>
                <tr>
                    <td>Pajak (0%)</td>
                    <td class="text-end"><span class="editable" contenteditable="true">0</span></td>
                </tr>
                <tr class="grand-total">
                    <td>TOTAL TAGIHAN</td>
                    <td class="text-end">Rp <span class="editable" contenteditable="true">{{ number_format($penjualan->total_semua, 0, ',', '.') }}</span></td>
                </tr>
            </table>
        </div>

        <div class="notes-section">
            <p class="editable" contenteditable="true">
                <strong>INSTRUKSI PEMBAYARAN:</strong><br>
                1. Pembayaran harap ditransfer ke rekening resmi: <strong>BCA 1234 567 890 a.n PT Mentari Atlas Indonesia</strong>.<br>
                2. Harap cantumkan Nomor Invoice pada berita acara transfer.<br>
                3. Dokumen ini sah dan dicetak secara otomatis oleh sistem, tidak memerlukan tanda tangan basah jika dikirim secara elektronik.
            </p>
        </div>

        <table class="signature">
            <tr>
                <td>
                    Hormat Kami,<br>
                    <strong>PT Mentari Atlas Indonesia</strong><br>
                    <br>
                    <br>
                    <div class="signature-line"></div><br>
                    <span class="editable" contenteditable="true"><strong>( Finance & Accounting )</strong></span>
                </td>
                <td>
                    Menyetujui,<br>
                    <strong>{{ $penjualan->customer->nama_customer }}</strong><br>
                    <br>
                    <br>
                    <div class="signature-line"></div><br>
                    <span class="editable" contenteditable="true"><strong>( Tanda Tangan & Cap )</strong></span>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>