@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-light border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <h5 class="mb-0 fw-bold text-dark">
                Edit Sales Order: <span class="text-primary">{{ $penjualan->no_so }}</span>
            </h5>
            <a href="{{ route('penjualan.index') }}" class="btn btn-sm btn-secondary fw-bold shadow-sm text-nowrap">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="card-body p-4">
            <form id="form-edit-so" action="{{ route('penjualan.update', $penjualan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-4 border-end">
                        <h6 class="fw-bold text-primary mb-3">Informasi Customer</h6>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Nama Customer *</label>
                            <input type="text" name="nama_customer" class="form-control bg-light" value="{{ $penjualan->customer->nama_customer }}" readonly>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold text-muted">NPWP</label>
                                <input type="text" name="npwp" class="form-control" value="{{ $penjualan->customer->npwp ?? '' }}" placeholder="-">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold text-muted">KTP</label>
                                <input type="text" name="ktp" class="form-control" value="{{ $penjualan->customer->ktp ?? '' }}" placeholder="-">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8 ps-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-primary mb-0">Daftar Barang</h6>
                            <button type="button" class="btn btn-sm btn-success fw-bold py-1 px-2" style="font-size: 0.8rem;" disabled title="Penambahan item di form edit dinonaktifkan demi keamanan stok">
                                <i class="fas fa-plus me-1"></i> Tambah Item
                            </button>
                        </div>

                        <div class="table-responsive d-none d-lg-block" id="desktop-items-container">
                            <table class="table table-borderless align-middle mb-0" style="font-size: 0.85rem;">
                                <thead>
                                    <tr class="text-dark fw-bold">
                                        <th style="width: 45%;">Barang</th>
                                        <th style="width: 20%;">Harga Awal</th> 
                                        <th style="width: 20%;">Harga Akhir</th> 
                                        <th style="width: 15%;">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($penjualan->details as $detail)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                                            <select name="barang_id[]" class="form-select bg-light" readonly style="pointer-events: none;">
                                                <option value="{{ $detail->barang_id }}">
                                                    {{ $detail->barang->kode_barang }} - {{ $detail->barang->nama_barang }}
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            {{-- FITUR BEBAS HARGA (Tanpa Batas Maksimal) --}}
                                            <input type="number" name="harga_awal[]" class="form-control text-end fw-bold" 
                                                   value="{{ $detail->harga_awal ?: $detail->barang->harga_jual }}" min="0" required>
                                        </td>
                                        <td>
                                            {{-- FITUR BEBAS HARGA (Tanpa Batas Maksimal) --}}
                                            <input type="number" name="harga_satuan[]" class="form-control text-end fw-bold" 
                                                   value="{{ $detail->harga_satuan }}" min="0" required>
                                        </td>
                                        <td>
                                            <input type="number" name="jumlah[]" class="form-control text-center" 
                                                   value="{{ $detail->jumlah }}" min="1" required>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- MOBILE CARDS FOR ITEMS --}}
                        <div class="d-lg-none mt-3" id="mobile-items-container">
                            @foreach($penjualan->details as $detail)
                            <div class="card mb-3 shadow-sm border-0" style="border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                                <div class="card-body p-3 bg-light" style="border-radius: 12px;">
                                    <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                                    <input type="hidden" name="barang_id[]" value="{{ $detail->barang_id }}">
                                    <div class="fw-bold text-slate-dark mb-1">{{ $detail->barang->kode_barang }} - {{ $detail->barang->nama_barang }}</div>
                                    <hr class="my-2 opacity-10">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="small fw-bold text-muted mb-1" style="font-size: 0.7rem;">Harga Awal</label>
                                            <input type="number" name="harga_awal[]" class="form-control form-control-sm text-end fw-bold" 
                                                   value="{{ $detail->harga_awal ?: $detail->barang->harga_jual }}" min="0" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="small fw-bold text-muted mb-1" style="font-size: 0.7rem;">Harga Akhir</label>
                                            <input type="number" name="harga_satuan[]" class="form-control form-control-sm text-end fw-bold" 
                                                   value="{{ $detail->harga_satuan }}" min="0" required>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <label class="small fw-bold text-muted mb-1" style="font-size: 0.7rem;">Qty</label>
                                            <input type="number" name="jumlah[]" class="form-control form-control-sm text-center fw-bold text-primary" 
                                                   value="{{ $detail->jumlah }}" min="1" required style="font-size: 1rem;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="text-end mt-4 pt-3 border-top">
                            <span class="text-muted small d-block mb-3 fst-italic">* Mengubah harga akan mempengaruhi Total Nilai SO secara otomatis.</span>
                            <button type="submit" class="btn btn-warning btn-lg fw-bold px-4 shadow-sm text-dark" style="background-color: #ffc107;">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<script>
    document.getElementById('form-edit-so').addEventListener('submit', function(e) {
        e.preventDefault(); // Pause submission

        const desktopContainer = document.getElementById('desktop-items-container');
        const mobileContainer = document.getElementById('mobile-items-container');
        
        // Check which one is hidden and strip its name attributes so they aren't submitted
        if (desktopContainer && window.getComputedStyle(desktopContainer).display === 'none') {
            desktopContainer.querySelectorAll('input, select').forEach(i => i.removeAttribute('name'));
        }
        
        if (mobileContainer && window.getComputedStyle(mobileContainer).display === 'none') {
            mobileContainer.querySelectorAll('input, select').forEach(i => i.removeAttribute('name'));
        }
        
        this.submit(); // Resume submission
    });
</script>
@endsection