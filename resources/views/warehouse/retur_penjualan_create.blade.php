@extends('layouts.app')

@section('content')


<style>
    body { background-color: #f8fafc !important; }
    
    .text-blue-custom { color: #0284c7 !important; }
    .bg-blue-custom { background-color: #0284c7 !important; color: #ffffff !important; }
    .btn-blue-custom { background-color: #0284c7 !important; border-color: #0284c7 !important; color: #ffffff !important; font-weight: 500; transition: all 0.2s; }
    .btn-blue-custom:hover { background-color: #0369a1 !important; color: #ffffff !important; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2); }

    .text-slate-dark { color: #0f172a !important; }
    .text-slate-muted { color: #64748b !important; }
    
    .card-custom { border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    
    .table-premium thead th { 
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important; 
        color: #ffffff !important; 
        border-bottom: none !important; 
        font-weight: 600 !important; 
        text-transform: uppercase; 
        font-size: 0.75rem; 
        letter-spacing: 0.5px; 
        padding: 12px 16px !important;
    }
    
    .item-row {
        transition: all 0.15s ease-in-out;
    }
    .item-row:hover {
        background-color: rgba(2, 132, 199, 0.01) !important;
    }
    .item-row.row-selected {
        background-color: rgba(2, 132, 199, 0.04) !important;
    }

    .select2-container .select2-selection--single { height: 38px !important; border: 1px solid #cbd5e1 !important; border-radius: 0.375rem !important; display: flex; align-items: center; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { color: #0f172a !important; padding-left: 0.75rem !important; }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 80vh;">
    
    {{-- BREADCRUMB & BACK BUTTON --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('retur.penjualan.index') }}" class="text-blue-custom text-decoration-none">Return Penjualan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Catat Return Baru</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-slate-dark fw-bold"><i class="fas fa-undo text-blue-custom me-2"></i>Catat Return / Credit Note (Massal)</h1>
        </div>
        <a href="{{ route('retur.penjualan.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 px-4 py-3 mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><strong>Gagal!</strong> {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('retur.penjualan.store') }}" method="POST" id="formReturJual">
        @csrf
        
        {{-- CARD PILIH SO --}}
        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-slate-dark mb-2"><i class="fas fa-file-invoice me-1 text-blue-custom"></i>Pilih Nota Penjualan (SO) *</label>
                        <select name="penjualan_id" id="penjualan_id" class="form-select border-secondary-subtle select2" required style="width: 100%;">
                            <option value="" disabled selected>-- Ketik untuk Mencari Nota SO --</option>
                            @foreach($penjualans as $p)
                                <option value="{{ $p->id }}">{{ $p->no_so }} - {{ $p->customer->nama_customer ?? 'Umum' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0 border-start ps-md-4">
                        <span class="small text-slate-muted d-block"><i class="fas fa-info-circle me-1"></i> Informasi Pembatasan Retur:</span>
                        <small class="text-slate-muted">Barang yang bisa diretur hanya barang yang statusnya sudah terkirim fisik dan tidak melebihi sisa kapasitas retur nota penjualan.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE / CARD LIST DAFTAR BARANG --}}
        <div class="card card-custom border-0 shadow-sm mb-4 d-none" id="items-card">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-slate-dark"><i class="fas fa-box-open me-2 text-blue-custom"></i>Rincian Item yang Akan Diretur</span>
                <span class="badge bg-blue-custom rounded-pill px-3 py-1.5 fw-bold" id="badge-selected-count">0 Item Terpilih</span>
            </div>
            
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-premium align-middle mb-0" style="font-size: 0.85rem; width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">Pilih</th>
                            <th>Nama Barang</th>
                            <th style="width: 120px;">Qty Retur</th>
                            <th style="width: 180px;">Jenis Klaim</th>
                            <th style="width: 180px;">Kondisi / Aging</th>
                            <th style="width: 160px;">Nominal CN (Rp)</th>
                            <th>Alasan / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="items-table-body">
                        <!-- Digambar dinamis oleh JS -->
                    </tbody>
                </table>
            </div>

            {{-- MOBILE CARDS --}}
            <div class="d-lg-none p-2" id="items-mobile-container">
                <!-- Digambar dinamis oleh JS -->
            </div>

            {{-- SUMMARY & SUBMIT FOOTER --}}
            <div class="card-footer bg-white border-top p-4">
                <div class="row align-items-center">
                    <div class="col-md-7 text-center text-md-start mb-3 mb-md-0">
                        <span class="text-slate-muted small"><i class="fas fa-exclamation-circle me-1"></i> Pastikan item dan kuantitas retur sudah benar sebelum memproses.</span>
                    </div>
                    <div class="col-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold text-slate-muted small">TOTAL QTY:</span>
                            <span class="fw-bold text-slate-dark h5 mb-0" id="summary-total-qty">0 Pcs</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold text-slate-muted small">TOTAL ESTIMASI CN:</span>
                            <span class="fw-bold text-blue-custom h4 mb-0" id="summary-total-cn">Rp 0</span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('retur.penjualan.index') }}" class="btn btn-light border fw-bold w-100 py-2.5">Batal</a>
                            <button type="submit" class="btn btn-blue-custom fw-bold w-100 py-2.5" onclick="return confirm('Konfirmasi: Proses return penjualan massal ini? Stok dan piutang pelanggan akan disesuaikan otomatis oleh sistem.')">Proses Return</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const tableBody = document.getElementById('items-table-body');
        const mobileContainer = document.getElementById('items-mobile-container');
        const itemsCard = document.getElementById('items-card');
        const penjualanSelect = document.getElementById('penjualan_id');

        function updateGrandTotals() {
            let totalQty = 0;
            let totalCN = 0;
            let selectedCount = 0;

            // Check di Active Container
            const isMobile = window.innerWidth < 992;
            const activeContainer = isMobile ? document.getElementById('items-mobile-container') : document.getElementById('items-table-body');
            if (!activeContainer) return;

            const rows = activeContainer.querySelectorAll('.item-row');
            rows.forEach(row => {
                const checkbox = row.querySelector('.item-checkbox');
                if (checkbox && checkbox.checked) {
                    selectedCount++;
                    const qtyInput = row.querySelector('.item-qty');
                    const qty = parseInt(qtyInput ? qtyInput.value : 0) || 0;
                    totalQty += qty;

                    const nominalInput = row.querySelector('.item-nominal');
                    const nominal = parseFloat(nominalInput ? nominalInput.value : 0) || 0;
                    totalCN += nominal;
                }
            });

            const badgeCount = document.getElementById('badge-selected-count');
            const summaryQty = document.getElementById('summary-total-qty');
            const summaryCN = document.getElementById('summary-total-cn');

            if (badgeCount) badgeCount.innerText = selectedCount + ' Item Terpilih';
            if (summaryQty) summaryQty.innerText = totalQty + ' Pcs';
            if (summaryCN) {
                summaryCN.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalCN);
            }
        }

        function syncInputValues(sourceEl, targetName, index) {
            const val = sourceEl.value;
            const targetEl = document.querySelector(`[name="items[${index}][${targetName}]"]`);
            if (targetEl && targetEl !== sourceEl) {
                targetEl.value = val;
            }
        }

        function updateRowFields(index) {
            // Dapatkan baris desktop & mobile
            const dRow = document.querySelector(`.d-row-${index}`);
            const mRow = document.querySelector(`.m-row-${index}`);
            if (!dRow || !mRow) return;

            // Dapatkan checkbox
            const dCheckbox = dRow.querySelector('.item-checkbox');
            const mCheckbox = mRow.querySelector('.item-checkbox');
            const isChecked = dCheckbox.checked || mCheckbox.checked;

            // Set state checkbox sync
            dCheckbox.checked = isChecked;
            mCheckbox.checked = isChecked;

            const fields = ['barang_id', 'qty_retur', 'jenis_retur', 'status_kondisi', 'aging_retur', 'nominal_potongan', 'alasan'];
            const fieldClasses = {
                'barang_id': 'barang-id',
                'qty_retur': 'qty',
                'jenis_retur': 'jenis',
                'status_kondisi': 'kondisi',
                'aging_retur': 'aging',
                'nominal_potongan': 'nominal',
                'alasan': 'alasan'
            };

            fields.forEach(f => {
                const cls = fieldClasses[f] || f;
                const dInput = dRow.querySelector(`.item-${cls}`);
                const mInput = mRow.querySelector(`.item-${cls}`);

                if (isChecked) {
                    dInput.removeAttribute('disabled');
                    mInput.removeAttribute('disabled');
                } else {
                    dInput.setAttribute('disabled', 'disabled');
                    mInput.setAttribute('disabled', 'disabled');
                }
            });

            if (!isChecked) {
                dRow.classList.remove('row-selected');
                mRow.classList.remove('row-selected');
                updateGrandTotals();
                return;
            }

            dRow.classList.add('row-selected');
            mRow.classList.add('row-selected');

            // Logika Jenis Klaim
            const dJenis = dRow.querySelector('.item-jenis');
            const mJenis = mRow.querySelector('.item-jenis');
            const jenis = dJenis.value;

            const dDivKondisi = dRow.querySelector('.div-kondisi');
            const mDivKondisi = mRow.querySelector('.div-kondisi');
            const dDivAging = dRow.querySelector('.div-aging');
            const mDivAging = mRow.querySelector('.div-aging');

            const dNominal = dRow.querySelector('.item-nominal');
            const mNominal = mRow.querySelector('.item-nominal');

            if (jenis === 'harga_credit_note') {
                if (dDivKondisi) dDivKondisi.classList.add('d-none');
                if (mDivKondisi) mDivKondisi.classList.add('d-none');
                if (dDivAging) dDivAging.classList.add('d-none');
                if (mDivAging) mDivAging.classList.add('d-none');

                dRow.querySelector('.item-kondisi').setAttribute('disabled', 'disabled');
                mRow.querySelector('.item-kondisi').setAttribute('disabled', 'disabled');
                dRow.querySelector('.item-aging').setAttribute('disabled', 'disabled');
                mRow.querySelector('.item-aging').setAttribute('disabled', 'disabled');

                dNominal.removeAttribute('readonly');
                mNominal.removeAttribute('readonly');
                dNominal.setAttribute('required', 'required');
                mNominal.setAttribute('required', 'required');
                
                if (dNominal.value === '0') dNominal.value = '';
                if (mNominal.value === '0') mNominal.value = '';
            } else {
                if (dDivKondisi) dDivKondisi.classList.remove('d-none');
                if (mDivKondisi) mDivKondisi.classList.remove('d-none');

                dRow.querySelector('.item-kondisi').removeAttribute('disabled');
                mRow.querySelector('.item-kondisi').removeAttribute('disabled');

                const kondisi = dRow.querySelector('.item-kondisi').value;

                if (dDivAging) dDivAging.classList.remove('d-none');
                if (mDivAging) mDivAging.classList.remove('d-none');
                dRow.querySelector('.item-aging').removeAttribute('disabled');
                mRow.querySelector('.item-aging').removeAttribute('disabled');

                // Kalkulasi
                const price = parseFloat(dRow.getAttribute('data-price') || 0);
                const qty = parseInt(dRow.querySelector('.item-qty').value) || 0;
                let calculatedCN = qty * price;

                const aging = dRow.querySelector('.item-aging').value;
                if (aging === '46_90') {
                    calculatedCN = calculatedCN * 0.90;
                } else if (aging === '91_135') {
                    calculatedCN = calculatedCN * 0.70;
                }

                dNominal.setAttribute('readonly', 'readonly');
                mNominal.setAttribute('readonly', 'readonly');
                dNominal.removeAttribute('required');
                mNominal.removeAttribute('required');

                dNominal.value = Math.round(calculatedCN);
                mNominal.value = Math.round(calculatedCN);
            }

            updateGrandTotals();
        }

        const handleSOChange = function() {
            const so_id = penjualanSelect.value;
            if (!tableBody || !mobileContainer || !itemsCard) return;

            tableBody.innerHTML = '';
            mobileContainer.innerHTML = '';
            itemsCard.classList.add('d-none');
            updateGrandTotals();

            if (so_id) {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Memuat daftar barang dari SO...</td></tr>';
                mobileContainer.innerHTML = '<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Memuat daftar barang dari SO...</div>';
                itemsCard.classList.remove('d-none');

                fetch('/get-items-so/' + so_id)
                    .then(response => response.json())
                    .then(data => {
                        tableBody.innerHTML = '';
                        mobileContainer.innerHTML = '';

                        if (data.length > 0) {
                            data.forEach((item, i) => {
                                let qty = item.jumlah_diajukan || item.qty || item.jumlah || 0;
                                let price = item.harga_satuan || 0;

                                // HTML Desktop
                                let dRowHtml = `
                                    <tr class="item-row d-row-${i}" data-index="${i}" data-price="${price}">
                                        <td class="text-center">
                                            <input type="checkbox" name="items[${i}][selected]" value="1" class="form-check-input item-checkbox" style="width: 1.2rem; height: 1.2rem; cursor: pointer;">
                                            <input type="hidden" name="items[${i}][barang_id]" value="${item.barang_id}" class="item-barang-id" disabled>
                                        </td>
                                        <td>
                                            <strong class="text-slate-dark d-block">${item.barang.nama_barang}</strong>
                                            <span class="text-slate-muted small">Terkirim: <strong>${qty} Pcs</strong> | Rp ${new Intl.NumberFormat('id-ID').format(price)}/pcs</span>
                                        </td>
                                        <td>
                                            <input type="number" name="items[${i}][qty_retur]" class="form-control form-control-sm text-center item-qty" value="1" min="1" max="${qty}" required disabled>
                                        </td>
                                        <td>
                                            <select name="items[${i}][jenis_retur]" class="form-select form-select-sm item-jenis" required disabled>
                                                <option value="fisik" selected>📦 Return Fisik</option>
                                                <option value="harga_credit_note">🏷️ Credit Note</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="div-kondisi">
                                                <select name="items[${i}][status_kondisi]" class="form-select form-select-sm item-kondisi" disabled>
                                                    <option value="bagus" selected>Bagus</option>
                                                    <option value="rusak">Rusak</option>
                                                </select>
                                            </div>
                                            <div class="div-aging mt-1">
                                                <select name="items[${i}][aging_retur]" class="form-select form-select-sm item-aging text-danger border-danger">
                                                    <option value="0_45" selected>0-45 Hari (0%)</option>
                                                    <option value="46_90">46-90 Hari (10%)</option>
                                                    <option value="91_135">91-135 Hari (30%)</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" name="items[${i}][nominal_potongan]" class="form-control form-control-sm item-nominal fw-bold text-blue-custom" value="0" readonly disabled>
                                        </td>
                                        <td>
                                            <input type="text" name="items[${i}][alasan]" class="form-control form-control-sm item-alasan" placeholder="Keterangan..." required disabled>
                                        </td>
                                    </tr>
                                `;

                                // HTML Mobile
                                let mCardHtml = `
                                    <div class="card card-custom mb-3 item-row m-row-${i} p-3" data-index="${i}" data-price="${price}">
                                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="checkbox" name="items[${i}][selected]" value="1" class="form-check-input item-checkbox" style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                                                <input type="hidden" name="items[${i}][barang_id]" value="${item.barang_id}" class="item-barang-id" disabled>
                                                <strong class="text-slate-dark text-sm">${item.barang.nama_barang}</strong>
                                            </div>
                                            <span class="badge bg-secondary-soft text-slate-dark text-xs">${qty} Pcs</span>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="small text-slate-muted mb-1 d-block">Qty Retur</label>
                                                <input type="number" name="items[${i}][qty_retur]" class="form-control form-control-sm item-qty" value="1" min="1" max="${qty}" required disabled>
                                            </div>
                                            <div class="col-6">
                                                <label class="small text-slate-muted mb-1 d-block">Jenis Klaim</label>
                                                <select name="items[${i}][jenis_retur]" class="form-select form-select-sm item-jenis" required disabled>
                                                    <option value="fisik" selected>📦 Return Fisik</option>
                                                    <option value="harga_credit_note">🏷️ Credit Note</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-12">
                                                <div class="div-kondisi">
                                                    <label class="small text-slate-muted mb-1 d-block">Kondisi Fisik</label>
                                                    <select name="items[${i}][status_kondisi]" class="form-select form-select-sm item-kondisi" disabled>
                                                        <option value="bagus" selected>Bagus</option>
                                                        <option value="rusak">Rusak</option>
                                                    </select>
                                                </div>
                                                <div class="div-aging">
                                                    <label class="small text-slate-muted mb-1 d-block">Masa Aging</label>
                                                    <select name="items[${i}][aging_retur]" class="form-select form-select-sm item-aging text-danger border-danger">
                                                        <option value="0_45" selected>0-45 Hari (0%)</option>
                                                        <option value="46_90">46-90 Hari (10%)</option>
                                                        <option value="91_135">91-135 Hari (30%)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="small text-slate-muted mb-1 d-block">Nominal CN (Rp)</label>
                                                <input type="number" name="items[${i}][nominal_potongan]" class="form-control form-control-sm item-nominal fw-bold text-blue-custom" value="0" readonly disabled>
                                            </div>
                                            <div class="col-6">
                                                <label class="small text-slate-muted mb-1 d-block">Keterangan</label>
                                                <input type="text" name="items[${i}][alasan]" class="form-control form-control-sm item-alasan" placeholder="Keterangan..." required disabled>
                                            </div>
                                        </div>
                                    </div>
                                `;

                                tableBody.insertAdjacentHTML('beforeend', dRowHtml);
                                mobileContainer.insertAdjacentHTML('beforeend', mCardHtml);
                            });

                            // Setup change listeners for sync & updates
                            const bindRowEvents = function(index) {
                                const dRow = document.querySelector(`.d-row-${index}`);
                                const mRow = document.querySelector(`.m-row-${index}`);

                                const elements = ['qty_retur', 'jenis_retur', 'status_kondisi', 'aging_retur', 'nominal_potongan', 'alasan'];
                                const elementClasses = {
                                    'qty_retur': 'qty',
                                    'jenis_retur': 'jenis',
                                    'status_kondisi': 'kondisi',
                                    'aging_retur': 'aging',
                                    'nominal_potongan': 'nominal',
                                    'alasan': 'alasan'
                                };

                                // Checkbox trigger
                                dRow.querySelector('.item-checkbox').addEventListener('change', () => updateRowFields(index));
                                mRow.querySelector('.item-checkbox').addEventListener('change', () => updateRowFields(index));

                                // Sync desktop & mobile inputs
                                elements.forEach(f => {
                                    const cls = elementClasses[f] || f;
                                    const dIn = dRow.querySelector(`.item-${cls}`);
                                    const mIn = mRow.querySelector(`.item-${cls}`);

                                    dIn.addEventListener('change', () => {
                                        mIn.value = dIn.value;
                                        updateRowFields(index);
                                    });
                                    mIn.addEventListener('change', () => {
                                        dIn.value = mIn.value;
                                        updateRowFields(index);
                                    });

                                    dIn.addEventListener('input', () => {
                                        mIn.value = dIn.value;
                                        updateRowFields(index);
                                    });
                                    mIn.addEventListener('input', () => {
                                        dIn.value = mIn.value;
                                        updateRowFields(index);
                                    });
                                });
                            };

                            data.forEach((_, i) => bindRowEvents(i));

                        } else {
                            tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger"><i class="fas fa-exclamation-circle me-2"></i>Tidak ada barang yang terkirim pada SO ini.</td></tr>';
                            mobileContainer.innerHTML = '<div class="text-center py-4 text-danger"><i class="fas fa-exclamation-circle me-2"></i>Tidak ada barang yang terkirim pada SO ini.</div>';
                        }
                        updateGrandTotals();
                    })
                    .catch(error => {
                        tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger"><i class="fas fa-times-circle me-2"></i>Gagal memuat data barang.</td></tr>';
                        mobileContainer.innerHTML = '<div class="text-center py-4 text-danger"><i class="fas fa-times-circle me-2"></i>Gagal memuat data barang.</div>';
                        console.error('Error:', error);
                    });
            }
        };

        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            jQuery('#penjualan_id').on('change', handleSOChange);
        } else {
            penjualanSelect.addEventListener('change', handleSOChange);
        }

        const mainForm = document.getElementById('formReturJual');
        mainForm.addEventListener('submit', function(e) {
            const checkedCount = mainForm.querySelectorAll('.item-checkbox:checked').length;
            // Since there are duplicate checkboxes, checkedCount might be double.
            // We only need at least 1 true selected item, which means > 0.
            if (checkedCount === 0) {
                alert('Pilih setidaknya satu barang untuk diretur!');
                e.preventDefault();
                return false;
            }

            const desktopContainer = document.getElementById('items-table-body');
            const mobileContainer = document.getElementById('items-mobile-container');
            if (window.innerWidth < 992) {
                if (desktopContainer) desktopContainer.querySelectorAll('input, select').forEach(i => i.removeAttribute('name'));
            } else {
                if (mobileContainer) mobileContainer.querySelectorAll('input, select').forEach(i => i.removeAttribute('name'));
            }
        });
    });
</script>
@endsection
