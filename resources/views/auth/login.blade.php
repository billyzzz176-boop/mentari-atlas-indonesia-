<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mentari Atlas Indonesia</title>
    
    {{-- Pastikan ini mengarah ke file CSS/Bootstrap Anda --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { 
            background-color: #f1f5f9; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* Warna Premium Mentari Atlas */
        .text-emerald { color: #10b981 !important; }
        .text-slate-dark { color: #0f172a !important; }
        .text-slate-muted { color: #64748b !important; }
        .bg-emerald { background-color: #10b981 !important; }
        
        /* Tombol & Input */
        .btn-emerald { 
            background-color: #10b981; 
            border: none; 
            color: white; 
            font-weight: 600; 
            transition: all 0.3s ease;
        }
        .btn-emerald:hover { 
            background-color: #059669; 
            transform: translateY(-2px); 
            box-shadow: 0 8px 15px rgba(16, 185, 129, 0.3); 
        }
        .form-control-custom {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 2.5rem 0.75rem 2.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        .form-control-custom:focus {
            background-color: #ffffff;
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
            outline: none;
        }

        .password-toggle-icon {
            position: absolute;
            top: 50%; /* Center vertically perfectly */
            transform: translateY(-50%);
            right: 1rem; /* Position on the right */
            color: #94a3b8;
            z-index: 100;
            cursor: pointer;
            padding: 0.5rem; /* Make it easier to click */
        }
        .input-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 1rem;
            color: #94a3b8;
            z-index: 10;
        }
        .password-toggle-icon:hover {
            color: #64748b;
        }

        /* Card Layout */
        .login-card {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: 2rem;
        }
        .login-brand-panel {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        /* Dekorasi Lingkaran Abstrak di Panel Kiri */
        .circle-decoration-1 {
            position: absolute;
            width: 300px; height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -100px; right: -100px;
        }
        .circle-decoration-2 {
            position: absolute;
            width: 200px; height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            bottom: -50px; left: -50px;
        }
    </style>
</head>
<body>

    <div class="login-card row g-0">
        
        {{-- PANEL KIRI: BRANDING & IDENTITAS --}}
        <div class="col-lg-5 login-brand-panel d-none d-lg-flex">
            <div class="circle-decoration-1"></div>
            <div class="circle-decoration-2"></div>
            
            <div class="position-relative" style="z-index: 1;">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-white text-emerald rounded-circle d-flex align-items-center justify-content-center me-3 shadow" style="width: 48px; height: 48px;">
                        <i class="fas fa-layer-group fs-4"></i>
                    </div>
                    <div class="d-flex flex-column">
                        <h2 class="fw-bold mb-0 lh-1">Mentari<span class="fw-light">Atlas</span></h2>
                        <span class="text-white-50 text-uppercase mt-1" style="font-size: 0.75rem; letter-spacing: 4px; font-weight: 500;">Indonesia</span>
                    </div>
                </div>
                <h4 class="fw-light mb-3">Sistem Manajemen Bisnis Terintegrasi</h4>
                <p class="text-white-50 small mb-0" style="line-height: 1.6;">
                    Pantau persediaan, kelola alur kerja Sales Order, dan analisis performa bisnis Anda secara langsung dalam satu platform aman.
                </p>
            </div>
            
            <div class="position-relative mt-auto" style="z-index: 1;">
                <span class="badge bg-white text-emerald px-3 py-2 rounded-pill shadow-sm">
                    <i class="fas fa-shield-alt me-1"></i> Enterprise Secured
                </span>
            </div>
        </div>

        {{-- PANEL KANAN: FORM LOGIN --}}
        <div class="col-lg-7 p-4 p-md-5">
            <div class="px-md-4 py-md-3">
                
                {{-- Logo untuk tampilan mobile --}}
                <div class="d-flex align-items-center mb-4 d-lg-none">
                    <i class="fas fa-layer-group fs-2 text-emerald me-2"></i>
                    <div class="d-flex flex-column">
                        <h3 class="fw-bold text-slate-dark mb-0 lh-1">Mentari<span class="text-emerald">Atlas</span></h3>
                        <span class="text-slate-muted text-uppercase" style="font-size: 0.65rem; letter-spacing: 3px; font-weight: 600; margin-top: 2px;">Indonesia</span>
                    </div>
                </div>

                <div class="mb-4 mb-md-5">
                    <h3 class="fw-bold text-slate-dark">Selamat Datang!</h3>
                    <p class="text-slate-muted">Silakan masuk menggunakan akun korporat Anda.</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    {{-- Input Email --}}
                    <div class="mb-4">
                        <div class="position-relative">
                            <i class="fas fa-envelope input-icon"></i>
                            <input id="email" type="email" class="form-control form-control-custom w-100 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Alamat Email">
                        </div>
                        @error('email')
                            <span class="invalid-feedback d-block mt-1 fw-medium" role="alert" style="font-size: 0.8rem;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    {{-- Input Password --}}
                    <div class="mb-4">
                        <div class="position-relative">
                            <i class="fas fa-lock input-icon"></i>
                            <input id="password" type="password" class="form-control form-control-custom w-100 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Kata Sandi">
                            <i class="fas fa-eye password-toggle-icon" id="togglePassword"></i> {{-- Eye icon for password toggle --}}
                        </div>
                        @error('password')
                            <span class="invalid-feedback d-block mt-1 fw-medium" role="alert" style="font-size: 0.8rem;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    {{-- Remember Me & Lupa Password --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-slate-muted small" for="remember">
                                Ingat Saya
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                            <a class="text-emerald text-decoration-none small fw-bold" href="{{ route('password.request') }}">
                                Lupa sandi?
                            </a>
                        @endif
                    </div>

                    {{-- Tombol Login --}}
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-emerald rounded-pill py-2 fs-6">
                            Masuk ke Sistem <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye / eye-slash icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
