@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 font-weight-bold text-dark">Pusat Migrasi Data</h4>
            <p class="text-muted mb-0">Import data massal dari sistem lama menggunakan format Excel.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Card Import Barang -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom pt-4 pb-3">
                    <h5 class="mb-0 text-emerald-custom"><i class="fas fa-boxes me-2"></i> Import Master Barang</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Gunakan fitur ini untuk memindahkan ratusan data Sparepart sekaligus. Pastikan Anda mengunduh template resmi kami agar format kolom sesuai dengan sistem database.</p>
                    
                    <div class="d-flex gap-2 mb-4">
                        <a href="{{ route('import.template.barang') }}" class="btn btn-outline-emerald">
                            <i class="fas fa-download me-1"></i> Download Template Kosong
                        </a>
                    </div>

                    <hr class="my-4">
                    
                    <form action="{{ route('import.upload.barang') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <label for="file_excel" class="form-label fw-bold">Upload File Excel (Sudah Diisi)</label>
                            <input class="form-control" type="file" id="file_excel" name="file_excel" accept=".xlsx, .xls, .csv" required>
                            <small class="text-muted">Maksimal ukuran file: 10MB. Format: .xlsx, .xls, atau .csv</small>
                        </div>
                        <button type="submit" class="btn btn-emerald w-100">
                            <i class="fas fa-cloud-upload-alt me-1"></i> Mulai Proses Import
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card Import Customer -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom pt-4 pb-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-users me-2"></i> Import Master Customer</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Gunakan fitur ini untuk memindahkan data Toko/Bengkel pelanggan Anda. Download template kosong untuk format yang sesuai.</p>
                    
                    <div class="d-flex gap-2 mb-4">
                        <a href="{{ route('import.template.customer') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download me-1"></i> Download Template
                        </a>
                    </div>

                    <hr class="my-4">
                    
                    <form action="{{ route('import.upload.customer') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <label for="file_excel_customer" class="form-label fw-bold">Upload File Excel (Sudah Diisi)</label>
                            <input class="form-control" type="file" id="file_excel_customer" name="file_excel" accept=".xlsx, .xls, .csv" required>
                            <small class="text-muted">Maksimal ukuran file: 10MB. Format: .xlsx, .xls, atau .csv</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-cloud-upload-alt me-1"></i> Import Customer
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Card Import Supplier -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom pt-4 pb-3">
                    <h5 class="mb-0 text-danger"><i class="fas fa-truck me-2"></i> Import Master Supplier</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Gunakan fitur ini untuk memindahkan data agen/distributor tempat Anda belanja barang. Download template kosong untuk format yang sesuai.</p>
                    
                    <div class="d-flex gap-2 mb-4">
                        <a href="{{ route('import.template.supplier') }}" class="btn btn-outline-danger">
                            <i class="fas fa-download me-1"></i> Download Template
                        </a>
                    </div>

                    <hr class="my-4">
                    
                    <form action="{{ route('import.upload.supplier') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <label for="file_excel_supplier" class="form-label fw-bold">Upload File Excel (Sudah Diisi)</label>
                            <input class="form-control" type="file" id="file_excel_supplier" name="file_excel" accept=".xlsx, .xls, .csv" required>
                            <small class="text-muted">Maksimal ukuran file: 10MB. Format: .xlsx, .xls, atau .csv</small>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-cloud-upload-alt me-1"></i> Import Supplier
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
