@extends('layouts.app')

@section('content')
<style>
    /* Global Overrides untuk Tema Premium Mentari Atlas (Disinkronkan) */
    body { background-color: #f8fafc !important; }
    
    .text-emerald-custom { color: #10b981 !important; }
    .text-slate-dark { color: #0f172a !important; }
    .text-slate-muted { color: #64748b !important; }
    
    .bg-emerald-custom { background-color: #10b981 !important; color: #ffffff !important; }
    .btn-emerald-custom { background-color: #10b981 !important; border-color: #10b981 !important; color: #ffffff !important; font-weight: 500; transition: all 0.2s; }
    .btn-emerald-custom:hover { background-color: #059669 !important; color: #ffffff !important; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); }
    
    /* Card & Table Styling */
    .card-custom { border: 1px solid #e2e8f0; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .table-custom-header th { background-color: #f1f5f9 !important; color: #334155 !important; font-weight: 600 !important; border-bottom: 2px solid #e2e8f0 !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    
    /* Diet Ketat Tabel */
    .table-mentari-compact th, .table-mentari-compact td { padding: 0.75rem 0.5rem !important; }

    /* Minimalist Action Buttons */
    .btn-action-circle {
        width: 32px; height: 32px; padding: 0;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; transition: all 0.2s ease; flex-shrink: 0;
    }
    .btn-delete-custom { background-color: #f8fafc; color: #ef4444; border: 1px solid transparent; }
    .btn-delete-custom:hover { background-color: #fee2e2; color: #dc2626; transform: scale(1.1); }

    /* Form Inputs */
    .form-control:focus, .form-select:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.15);
        background-color: #ffffff;
    }
    
    /* Font Total */
    .font-monospace-custom { font-family: 'Courier New', Courier, monospace; font-weight: 700; letter-spacing: -0.5px; }
    
    /* Search Dropdown Styles */
    .search-dropdown-menu {
        max-height: 250px;
        z-index: 1050;
        top: 100%;
        left: 0;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    }
    .search-item-row:hover {
        background-color: #ecfdf5 !important;
    }
    .search-item-row {
        transition: background-color 0.2s;
    }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 80vh;">
    
    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-0 text-slate-dark fw-bold"><i class="fas fa-shopping-cart text-emerald-custom me-2"></i>Buat Sales Order Baru</h1>
            <p class="text-slate-muted small mb-0 mt-1">Input data pelanggan dan pilih barang untuk memproses SPK / Nota Penjualan.</p>
        </div>
        <a href="{{ route('penjualan.index') }}" class="btn btn-light shadow-sm rounded-pill px-4 border fw-bold text-slate-dark text-nowrap">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <form action="{{ route('penjualan.store') }}" method="POST">
        @csrf
        
        <div class="row g-4">
            {{-- KOLOM KIRI: Informasi Customer --}}
            <div class="col-xl-4 col-lg-5">
                <div class="card card-custom bg-white h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="m-0 fw-bold text-slate-dark"><i class="fas fa-user-circle text-emerald-custom me-2"></i>Data Pelanggan</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-slate-dark mb-1">Pilih Customer / Toko *</label>
                            <select name="customer_id" id="customer_id" class="form-select bg-light fw-bold text-slate-dark select2" onchange="applyCustomerData()" required>
                                <option value="" data-nama="" data-tingkat="Bronze" data-npwp="" data-ktp="" selected disabled>-- Pilih Pelanggan --</option>
                                                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}" 
                                                data-nama="{{ $c->nama_customer }}" 
                                                data-tingkat="{{ $c->tingkat_customer ?? 'Bronze' }}"
                                                data-npwp="{{ $c->npwp }}"
                                                data-ktp="{{ $c->ktp }}"
                                                data-sisa-plafon="{{ $c->sisa_plafon ?? $c->plafon }}">
                                            {{ $c->nama_customer }} [{{ $c->tingkat_customer ?? 'Bronze' }}] - Sisa Rp{{ number_format($c->sisa_plafon ?? $c->plafon, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                            </select>
                            <input type="hidden" name="nama_customer" id="nama_customer_hidden">
                            <div id="sisa_limit_container" class="mt-2 p-2 rounded d-none" style="background-color: #d1fae5; border-left: 4px solid #10b981;">
                                <span class="small fw-bold text-emerald-custom"><i class="fas fa-wallet me-1"></i> Sisa Limit Kredit:</span>
                                <span class="fw-bold text-slate-dark ms-1" id="text_sisa_limit">Rp 0</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-slate-dark mb-1">Nomor NPWP <span class="text-slate-muted fw-normal">(Otomatis/Opsional)</span></label>
                            <input type="text" name="npwp" id="input_npwp" class="form-control bg-light" placeholder="Akan terisi otomatis jika ada">
                        </div>
                        
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-slate-dark mb-1">Nomor KTP <span class="text-slate-muted fw-normal">(Otomatis/Opsional)</span></label>
                            <input type="text" name="ktp" id="input_ktp" class="form-control bg-light" placeholder="Akan terisi otomatis jika ada">
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: Keranjang Belanja --}}
            <div class="col-xl-8 col-lg-7">
                <div class="card card-custom bg-white h-100 d-flex flex-column">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-slate-dark"><i class="fas fa-box-open text-emerald-custom me-2"></i>Rincian Barang</h6>
                        <button type="button" class="btn btn-sm btn-emerald-custom rounded-pill shadow-sm px-3" onclick="tambahBaris()">
                            <i class="fas fa-plus me-1"></i> Tambah Baris
                        </button>
                    </div>

                    {{-- Datalist removed and replaced by custom search dropdown --}}

                    <div class="card-body p-3 flex-grow-1" style="background-color: var(--bg-page);">
                        <div id="bodyBarang" class="d-flex flex-column gap-3">
                            <div class="card shadow-sm border-0 baris-barang" style="border-radius: 12px; border-left: 4px solid #10b981 !important;">
                                <div class="card-body p-3">
                                    <div class="position-relative input-search-container mb-3">
                                        <label class="small fw-bold text-slate-dark mb-1">Cari Barang (SKU/Nama)</label>
                                        <input type="text" class="form-control form-control-sm bg-light fw-medium text-slate-dark input-cari-barang py-2" 
                                               placeholder="Ketik SKU atau Nama..." 
                                               oninput="cariBarang(this)" onfocus="cariBarang(this)" onblur="hideDropdown(this)" autocomplete="off" required>
                                        <input type="hidden" name="barang_id[]" class="input-barang-id" required>
                                        <div class="search-dropdown-menu d-none position-absolute w-100 bg-white border rounded shadow-sm overflow-auto" style="z-index: 1000;"></div>
                                    </div>
                                    <div class="row g-2 align-items-end">
                                        <div class="col-4">
                                            <label class="small text-slate-muted mb-1">Stok</label>
                                            <input type="text" class="form-control form-control-sm bg-white text-center border-0 input-stok fw-bold text-slate-muted" readonly placeholder="-">
                                        </div>
                                        <div class="col-8">
                                            <label class="small text-slate-muted mb-1">Harga (Rp)</label>
                                            <input type="number" name="harga_satuan[]" class="form-control form-control-sm text-end input-harga fw-bold text-slate-dark bg-light py-2" required min="0" placeholder="0" oninput="hitungTotalAll()">
                                        </div>
                                    </div>
                                    <div class="row g-2 align-items-end mt-2">
                                        <div class="col-9">
                                            <label class="small text-slate-muted mb-1">Qty Jual</label>
                                            <input type="number" name="jumlah[]" class="form-control form-control-sm text-center input-jumlah fw-bold text-emerald-custom bg-light py-2" required min="1" value="1" oninput="hitungTotalAll()">
                                        </div>
                                        <div class="col-3 text-end">
                                            <button type="button" class="btn btn-sm btn-danger-soft w-100 py-2 btn-delete-item fw-bold" style="border-radius: 8px;" onclick="hapusBaris(this)" disabled>
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PANEL TOTAL DI DALAM CARD --}}
                    <div class="card-footer bg-light border-top py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <span class="small fw-bold text-slate-muted text-uppercase d-block mb-1">Total Tagihan</span>
                            <h3 class="mb-0 fw-bold text-emerald-custom font-monospace-custom" id="label-total">Rp 0</h3>
                        </div>
                        <button type="submit" class="btn btn-emerald-custom px-5 py-2 rounded-pill shadow-sm fw-bold">
                            <i class="fas fa-check-circle me-2"></i> Proses Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function tambahBaris() {
        const tbody = document.getElementById('bodyBarang');
        const tr = document.createElement('div');
        tr.className = 'card shadow-sm border-0 baris-barang';
        tr.style.borderRadius = '12px';
        tr.style.borderLeft = '4px solid #10b981';
        tr.innerHTML = `
            <div class="card-body p-3">
                <div class="position-relative input-search-container mb-3">
                    <label class="small fw-bold text-slate-dark mb-1">Cari Barang (SKU/Nama)</label>
                    <input type="text" class="form-control form-control-sm bg-light fw-medium text-slate-dark input-cari-barang py-2" 
                           placeholder="Ketik SKU atau Nama..." 
                           oninput="cariBarang(this)" onfocus="cariBarang(this)" onblur="hideDropdown(this)" autocomplete="off" required>
                    <input type="hidden" name="barang_id[]" class="input-barang-id" required>
                    <div class="search-dropdown-menu d-none position-absolute w-100 bg-white border rounded shadow-sm overflow-auto" style="z-index: 1000;"></div>
                </div>
                <div class="row g-2 align-items-end">
                    <div class="col-4">
                        <label class="small text-slate-muted mb-1">Stok</label>
                        <input type="text" class="form-control form-control-sm bg-white text-center border-0 input-stok fw-bold text-slate-muted" readonly placeholder="-">
                    </div>
                    <div class="col-8">
                        <label class="small text-slate-muted mb-1">Harga (Rp)</label>
                        <input type="number" name="harga_satuan[]" class="form-control form-control-sm text-end input-harga fw-bold text-slate-dark bg-light py-2" required min="0" placeholder="0" oninput="hitungTotalAll()">
                    </div>
                </div>
                <div class="row g-2 align-items-end mt-2">
                    <div class="col-9">
                        <label class="small text-slate-muted mb-1">Qty Jual</label>
                        <input type="number" name="jumlah[]" class="form-control form-control-sm text-center input-jumlah fw-bold text-emerald-custom bg-light py-2" required min="1" value="1" oninput="hitungTotalAll()">
                    </div>
                    <div class="col-3 text-end">
                        <button type="button" class="btn btn-sm btn-danger-soft w-100 py-2 btn-delete-item fw-bold" style="border-radius: 8px;" onclick="hapusBaris(this)">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        // Inject !important style directly for border-left
        tr.setAttribute('style', 'border-radius: 12px; border-left: 4px solid #10b981 !important;');
        tbody.appendChild(tr);
        updateTombolHapus();
    }

    function hapusBaris(button) {
        button.closest('.baris-barang').remove();
        updateTombolHapus();
        hitungTotalAll();
    }

    function updateTombolHapus() {
        const rows = document.querySelectorAll('.baris-barang');
        const buttons = document.querySelectorAll('.baris-barang .btn-delete-item');
        if (rows.length === 1) {
            buttons[0].disabled = true;
        } else {
            buttons.forEach(btn => btn.disabled = false);
        }
    }

    // Master data barang yang di-render dari PHP
    const listBarangMaster = [
        @foreach($barangs as $b)
        {
            id: {{ $b->id }},
            kode: {!! json_encode($b->kode_barang) !!},
            nama: {!! json_encode($b->nama_barang) !!},
            stok: {{ $b->stok_akhir }},
            harga: {{ $b->harga_jual }},
            merek: {!! json_encode($b->merek ?? '') !!}
        },
        @endforeach
    ];

    function cariBarang(input) {
        const query = input.value.toLowerCase().trim();
        const container = input.closest('.input-search-container');
        const dropdown = container.querySelector('.search-dropdown-menu');
        
        // Tampilkan semua barang (termasuk stok 0 agar bisa di-backorder)
        const filtered = listBarangMaster.filter(b => {
            if (query === '') return true;
            return b.kode.toLowerCase().includes(query) || b.nama.toLowerCase().includes(query);
        });

        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="text-muted p-3 text-center" style="font-size: 0.8rem;">Barang tidak ditemukan</div>';
        } else {
            let html = '';
            filtered.forEach(b => {
                const stokBadgeClass = b.stok > 10 ? 'bg-success-subtle text-success' : (b.stok > 0 ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger');
                html += `
                    <div class="dropdown-item py-2 px-3 border-bottom d-flex justify-content-between align-items-center search-item-row" 
                         style="cursor: pointer;" 
                         onmousedown="pilihBarangManual(this, ${b.id})">
                        <div class="pe-2" style="min-width: 0; flex: 1;">
                            <div class="fw-bold text-slate-dark text-truncate" style="font-size: 0.85rem;">${b.kode}</div>
                            <div class="text-slate-muted small text-truncate" style="font-size: 0.75rem;">${b.nama}</div>
                            ${b.merek ? `<div class="text-muted small" style="font-size: 0.7rem; font-style: italic;">Merek: ${b.merek}</div>` : ''}
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="badge ${stokBadgeClass} fw-bold px-2 py-1" style="font-size: 0.7rem;">Stok: ${b.stok}</span>
                            <div class="fw-bold text-emerald-custom mt-1" style="font-size: 0.8rem;">Rp ${b.harga.toLocaleString('id-ID')}</div>
                        </div>
                    </div>
                `;
            });
            dropdown.innerHTML = html;
        }
        dropdown.classList.remove('d-none');
    }

    function pilihBarangManual(element, id) {
        const row = element.closest('.baris-barang');
        const inputCari = row.querySelector('.input-cari-barang');
        const hiddenId = row.querySelector('.input-barang-id');
        const inputStok = row.querySelector('.input-stok');
        
        const item = listBarangMaster.find(b => b.id === id);
        if (item) {
            inputCari.value = `${item.kode} - ${item.nama}`;
            hiddenId.value = item.id;
            inputStok.value = item.stok;
            row.dataset.baseHarga = item.harga;
            
            applyTierToRow(row);
        }
    }

    function hideDropdown(input) {
        setTimeout(() => {
            const container = input.closest('.input-search-container');
            const dropdown = container.querySelector('.search-dropdown-menu');
            dropdown.classList.add('d-none');
            validasiInputCari(input);
        }, 250);
    }

    function validasiInputCari(input) {
        const valKetik = input.value.trim().toLowerCase();
        const row = input.closest('tr');
        const hiddenId = row.querySelector('.input-barang-id');
        const inputStok = row.querySelector('.input-stok');
        
        // Cek kecocokan persis (kode atau gabungan kode - nama)
        const match = listBarangMaster.find(b => 
            b.kode.toLowerCase() === valKetik ||
            `${b.kode.toLowerCase()} - ${b.nama.toLowerCase()}` === valKetik
        );
        
        if (match) {
            input.value = `${match.kode} - ${match.nama}`;
            hiddenId.value = match.id;
            inputStok.value = match.stok;
            row.dataset.baseHarga = match.harga;
        } else {
            const currentId = parseInt(hiddenId.value);
            const currentItem = listBarangMaster.find(b => b.id === currentId);
            if (!currentItem || `${currentItem.kode} - ${currentItem.nama}`.toLowerCase() !== valKetik) {
                hiddenId.value = '';
                inputStok.value = '';
                row.dataset.baseHarga = 0;
                row.querySelector('.input-harga').value = '';
                input.value = '';
            }
        }
        applyTierToRow(row);
    }

    function applyTierToRow(row) {
        const baseHarga = parseFloat(row.dataset.baseHarga) || 0;
        const selectCustomer = document.getElementById('customer_id');
        const customerOption = selectCustomer.options[selectCustomer.selectedIndex];
        const tier = customerOption ? (customerOption.getAttribute('data-tingkat') || 'Bronze') : 'Bronze';

        let finalHarga = baseHarga;
        if (tier === 'Silver') finalHarga *= 0.95;
        else if (tier === 'Gold') finalHarga *= 0.90;

        if (baseHarga > 0) {
            row.querySelector('.input-harga').value = Math.round(finalHarga);
        }
        
        hitungTotalAll();
    }

    // Fungsi Utama Saat Customer Dipilih (Menggabungkan Auto-Fill dan Harga Tier)
    function applyCustomerData() {
        const selectCustomer = document.getElementById('customer_id');
        
        if(selectCustomer.selectedIndex >= 0) {
            const opt = selectCustomer.options[selectCustomer.selectedIndex];
            
            // 1. Simpan Nama
            document.getElementById('nama_customer_hidden').value = opt.getAttribute('data-nama');
            
            // 2. Auto-Fill NPWP & KTP
            document.getElementById('input_npwp').value = opt.getAttribute('data-npwp') || '';
            document.getElementById('input_ktp').value = opt.getAttribute('data-ktp') || '';
            
            // 2b. Show Sisa Limit
            const sisaLimit = opt.getAttribute('data-sisa-plafon');
            if(sisaLimit) {
                const limitValue = parseFloat(sisaLimit);
                document.getElementById('text_sisa_limit').innerText = 'Rp ' + limitValue.toLocaleString('id-ID');
                document.getElementById('sisa_limit_container').classList.remove('d-none');
            } else {
                document.getElementById('sisa_limit_container').classList.add('d-none');
            }
            
            // 3. Update Harga Semua Barang
            const rows = document.querySelectorAll('.baris-barang');
            rows.forEach(row => {
                applyTierToRow(row);
            });
        }
    }

    function hitungTotalAll() {
        let total = 0;
        const rows = document.querySelectorAll('.baris-barang');
        rows.forEach(row => {
            const harga = parseFloat(row.querySelector('.input-harga').value) || 0;
            const jumlah = parseFloat(row.querySelector('.input-jumlah').value) || 0;
            total += (harga * jumlah);
        });
        document.getElementById('label-total').innerText = "Rp " + total.toLocaleString('id-ID');
    }
</script>
@endsection
