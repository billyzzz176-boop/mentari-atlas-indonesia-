<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $pengiriman->no_pengiriman }}</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #334155; /* Slate 700 for Delivery Note */
            --primary-light: #f1f5f9;
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

        /* Top Accent Bar */
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, var(--slate-900), var(--slate-500));
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
            color: var(--slate-900); 
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
            text-align: right;
        }

        .invoice-details h2 { 
            margin: 0 0 5px 0; 
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
            margin-bottom: 40px; 
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

        .notes-section {
            background: var(--slate-50);
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid var(--slate-900);
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
            margin-top: 50px; 
        }
        .signature td { 
            text-align: center; 
            width: 33.33%; 
            color: var(--slate-700);
        }
        .signature-line { 
            margin-top: 80px; 
            border-top: 1px solid var(--slate-900); 
            display: inline-block; 
            width: 160px; 
            margin-bottom: 5px;
        }
        
        .bo-note { font-size: 11px; color: #d97706; font-style: italic; display: block; margin-top: 6px; background: #fef3c7; padding: 4px 8px; border-radius: 4px; }
        .bo-success { font-size: 11px; color: #10b981; font-style: italic; display: block; margin-top: 6px; background: #d1fae5; padding: 4px 8px; border-radius: 4px; font-weight: 600; }
        
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

        @media (max-width: 768px) {
            body { padding: 10px 0; background: #e2e8f0; }
            .container { padding: 25px 15px; box-shadow: none; border-radius: 8px; }
            .header { flex-direction: column; gap: 20px; align-items: stretch; margin-bottom: 25px; }
            .invoice-details { align-items: flex-start; text-align: left; }
            .invoice-details h2 { margin: 0 0 5px 0; font-size: 26px; }
            .info-grid { grid-template-columns: 1fr; gap: 20px; margin-bottom: 25px; }
            .totals-table { width: 100%; }
            .summary-section { justify-content: stretch; }
            .signature { margin-top: 20px; }
            .signature td { display: block; width: 100%; margin-bottom: 40px; text-align: left; }
            .signature-line { margin-top: 40px; width: 180px; }
            
            /* Responsive table behavior */
            .table-responsive-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin-bottom: 20px;
                border-radius: 8px;
                border: 1px solid var(--slate-200);
            }
            .items-table { margin-bottom: 0; border: none; width: 100%; min-width: 600px; }
            .items-table th, .items-table td { padding: 10px; font-size: 11px; }
            
            .controls-bar {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
                padding: 15px;
                margin-bottom: 15px;
            }
            .controls-bar div { text-align: center; }
            .btn-print, .btn-back { display: block; text-align: center; width: 100%; margin: 5px 0; }
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
            background: var(--slate-900); 
            color: #fff; 
            cursor: pointer; 
            border: none; 
            border-radius: 6px; 
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            transition: all 0.2s;
        }
        .btn-print:hover { background: #000; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); }
        
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
                Teks berlatar kuning dapat Anda sesuaikan sebelum dicetak.
            </div>
            <div>
                <a href="{{ route('penjualan.index') }}" class="btn-back">Kembali</a>
                <button onclick="window.print()" class="btn-print">Cetak Surat Jalan</button>
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
                <h2>SURAT JALAN</h2>
                <div class="invoice-number">{{ $pengiriman->no_pengiriman }}</div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h3>Informasi Penerima</h3>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Kepada Yth.</td>
                        <td class="info-value">{{ $pengiriman->penjualan->customer->nama_customer }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Alamat Kirim</td>
                        <td class="info-value"><span class="editable" contenteditable="true">{{ $pengiriman->penjualan->customer->alamat ?? 'Isi alamat pengiriman...' }}</span></td>
                    </tr>
                    <tr>
                        <td class="info-label">Telepon</td>
                        <td class="info-value"><span class="editable" contenteditable="true">{{ $pengiriman->penjualan->customer->no_telepon ?? '-' }}</span></td>
                    </tr>
                </table>
            </div>

            <div class="info-box">
                <h3>Detail Pengiriman</h3>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Tanggal Kirim</td>
                        <td class="info-value"><span class="editable" contenteditable="true">{{ \Carbon\Carbon::parse($pengiriman->tanggal_kirim)->format('d M Y') }}</span></td>
                    </tr>
                    <tr>
                        <td class="info-label">Plat Kendaraan</td>
                        <td class="info-value"><span class="editable" contenteditable="true">{{ $pengiriman->plat_kendaraan ?? '[Ketik Plat / Ekspedisi]' }}</span></td>
                    </tr>
                    <tr>
                        <td class="info-label">Referensi SO</td>
                        <td class="info-value">{{ $pengiriman->penjualan->no_so }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="table-responsive-wrapper">
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="55%">Deskripsi Barang</th>
                        <th width="20%" class="text-center">Kuantitas Pesanan</th>
                        <th width="20%" class="text-center">Kuantitas Kirim</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengiriman->details as $index => $detail)
                    @php
                        $soDetail = $pengiriman->penjualan->details->where('barang_id', $detail->barang_id)->first();
                        $qtyPesan = $soDetail ? $soDetail->jumlah : $detail->jumlah_kirim;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong style="color: var(--slate-900);">{{ $detail->barang->nama_barang }}</strong>
                        </td>
                        <td class="text-center">{{ $qtyPesan }}</td>
                        <td class="text-center"><strong><span class="editable" contenteditable="true">{{ $detail->jumlah_kirim }}</span></strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="notes-section">
            <p class="editable" contenteditable="true">
                <strong>CATATAN PENGIRIMAN:</strong><br>
                1. Mohon periksa kondisi dan kelengkapan barang sebelum menandatangani Surat Jalan ini.<br>
                2. Klaim kerusakan atau kekurangan barang tidak dilayani setelah surat jalan ditandatangani oleh penerima.<br>
                3. Surat Jalan ini merupakan dokumen sah sebagai bukti serah terima barang.
            </p>
        </div>

        <table class="signature">
            <tr>
                <td>
                    Dibuat Oleh,<br>
                    <strong>Admin Gudang</strong><br>
                    <br>
                    <br>
                    <div class="signature-line"></div><br>
                    <span class="editable" contenteditable="true"><strong>( Nama Jelas )</strong></span>
                </td>
                <td>
                    Pengirim / Supir,<br>
                    <strong>Ekspedisi</strong><br>
                    <br>
                    <br>
                    <div class="signature-line"></div><br>
                    <span class="editable" contenteditable="true"><strong>( Nama Jelas )</strong></span>
                </td>
                <td>
                    Penerima,<br>
                    <strong>{{ $pengiriman->penjualan->customer->nama_customer }}</strong><br>
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