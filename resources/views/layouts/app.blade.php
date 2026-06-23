<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem ERP - Mentari Atlas Indonesia</title>

    <!-- PWA & Favicon Meta Tags -->
    <meta name="theme-color" content="#10b981">
    <link rel="icon" type="image/png" href="{{ asset('mentari-icon-192.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('mentari-icon-192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('mentari-icon-192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- SweetAlert2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    
    {{-- Select2 CSS & Bootstrap 5 Theme --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    {{-- jQuery & Select2 JS (Moved to head to avoid reference errors in views) --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        :root {
            --bg-page: #f8fafc;
            --bg-panel: rgba(255, 255, 255, 0.98);
            --border-panel: #e2e8f0;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --accent: #10b981;
            --accent-hover: #059669;
            --accent-soft: rgba(16, 185, 129, 0.15);
            --accent-muted: rgba(16, 185, 129, 0.05);
            --shadow-soft: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -2px rgba(0, 0, 0, 0.03);
            --shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            --radius: 16px;
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        html, body {
            width: 100%;
            margin: 0;
            padding: 0;
            min-height: 100%;
            background-color: var(--bg-page);
            color: var(--text-primary);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
            -webkit-font-smoothing: antialiased;
        }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 2px solid transparent; background-clip: padding-box; }

        /* Card Custom Pro Max */
        .card-custom {
            border: 1px solid rgba(255,255,255,0.4);
            border-radius: var(--radius);
            box-shadow: var(--shadow-soft);
            background: var(--bg-panel);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: var(--transition-smooth);
        }
        .card-custom:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }

        /* Buttons Pro Max */
        .btn {
            border-radius: 50rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            transition: var(--transition-smooth);
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        .btn-action-square {
            width: 35px;
            height: 35px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: var(--text-secondary);
            transition: var(--transition-smooth);
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }
        .btn-action-square:hover {
            background: var(--accent-soft);
            border-color: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.15);
        }

        /* Forms Pro Max */
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 0.6rem 1rem;
            transition: var(--transition-smooth);
            background-color: #f8fafc;
        }
        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-soft);
        }

        /* Tables Pro Max (Mentari & Gacor merged) */
        .table-mentari, .table-gacor {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        .table-mentari th, .table-gacor th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem;
            border-top: none;
        }
        .table-mentari td, .table-gacor td {
            vertical-align: middle;
            color: #334155;
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.2s ease;
        }
        .table-mentari tbody tr, .table-gacor tbody tr {
            transition: var(--transition-smooth);
        }
        .table-mentari tbody tr:hover, .table-gacor tbody tr:hover {
            background-color: rgba(16, 185, 129, 0.02); /* Soft emerald hint */
        }

        /* Badge Soft & Gacor */
        .badge-soft-success { background-color: var(--accent-soft); color: var(--accent); border: 1px solid rgba(16, 185, 129, 0.2); }
        .badge-soft-warning { background-color: rgba(245, 158, 11, 0.15); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.2); }
        .badge-soft-danger { background-color: rgba(239, 68, 68, 0.15); color: #b91c1c; border: 1px solid rgba(239, 68, 68, 0.2); }
        .badge-gacor { padding: 0.5em 0.8em; font-weight: 600; letter-spacing: 0.5px; border-radius: 50rem; }

        /* Skeleton Loader Overlay with layout persistence */
        #skeleton-overlay {
            position: fixed;
            top: 60px; /* Leave top-header visible */
            left: 16px; /* Leave collapsed sidebar visible */
            right: 0;
            bottom: 0;
            background: var(--bg-page, #f8fafc);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease-in-out;
        }

        #skeleton-overlay.active {
            opacity: 1;
            visibility: visible;
            pointer-events: all;
        }
        
        /* Push skeleton overlay when sidebar is hovered (expanded) */
        @media (min-width: 992px) {
            .sidebar:hover ~ .main-wrapper #skeleton-overlay {
                left: 260px;
            }
        }
        
        @media (max-width: 991.98px) {
            #skeleton-overlay {
                left: 0 !important;
                top: 60px;
                bottom: 60px; /* Leave bottom navigation visible */
            }
        }
        
        /* GPU-Accelerated Shimmer Effect */
        .skeleton-box {
            position: relative;
            overflow: hidden;
            background-color: var(--border-panel, #e2e8f0);
            border-radius: 8px;
            margin-bottom: 15px;
            min-height: 20px;
        }
        
        .skeleton-box::after {
            content: "";
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            transform: translateX(-100%);
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.4) 20%,
                rgba(255, 255, 255, 0.7) 60%,
                rgba(255, 255, 255, 0) 100%
            );
            animation: shimmer-effect 1.4s infinite ease-out;
            will-change: transform;
        }
        
        [data-theme="dark"] .skeleton-box {
            background-color: #1e293b;
        }
        
        [data-theme="dark"] .skeleton-box::after {
            background: linear-gradient(
                90deg,
                rgba(30, 41, 59, 0) 0%,
                rgba(255, 255, 255, 0.05) 20%,
                rgba(255, 255, 255, 0.1) 60%,
                rgba(30, 41, 59, 0) 100%
            );
        }

        @keyframes shimmer-effect {
            100% {
                transform: translateX(100%);
            }
        }
        
        #top-loader {
            position: fixed; top: 0; left: 0; height: 3px;
            background: var(--accent); z-index: 100000; width: 0;
            transition: width 0.4s ease; box-shadow: 0 0 10px var(--accent);
        }

        /* 🌟 GACOR UI: Micro-Interactions & Animations */
        
        /* 1. Page Fade-In Transition */
        body {
            animation: fadeIn 0.4s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* 2. Table Row Hover Elevate */
        .table tbody tr {
            transition: all 0.2s ease-in-out;
        }
        .table tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* 3. Button Click Ripple & Hover Scale */
        .btn {
            transition: all 0.2s ease-in-out;
        }
        .btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .btn:active:not(:disabled) {
            transform: translateY(1px) scale(0.98);
        }

        .text-success-custom { color: var(--accent) !important; }

        /* Sidebar Premium Layout Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 16px; /* Hijau polos tipis saat tidak disentuh */
            height: 100vh !important;
            min-height: 100vh !important;
            background-color: #064e3b !important; /* Emerald-900 solid color for better performance */
            border-right: 1px solid rgba(4, 120, 87, 0.5) !important; /* Emerald-700 border */
            z-index: 1060;
            transition: width 0.25s ease-in-out; /* Transisi halus untuk sidebar saja */
            border-radius: 0;
            overflow-x: hidden;
            overflow-y: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.02);
            flex-shrink: 0;
        }

        .sidebar:hover {
            width: 260px;
            box-shadow: 10px 0 25px rgba(0, 0, 0, 0.05);
            border-radius: 0; /* Kembalikan ke ujung lancip */
        }

        /* Sembunyikan semua isi sidebar (ikon & teks) saat tidak di-hover agar benar-benar hijau polos */
        .sidebar:not(:hover) .sidebar-brand,
        .sidebar:not(:hover) .sidebar-menu {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.15s ease-in-out;
        }

        .sidebar:hover .sidebar-brand,
        .sidebar:hover .sidebar-menu {
            opacity: 1;
            visibility: visible;
            transition: opacity 0.2s ease-in-out 0.1s;
        }

        /* Sidebar Brand Area */
        .sidebar-brand {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center; /* Posisi di tengah */
            padding: 0; /* Hapus padding agar center sempurna */
            border-bottom: 1px solid #047857; /* Emerald-700 */
            flex-shrink: 0;
            overflow: hidden;
            white-space: nowrap;
        }
        .sidebar-brand .brand-logo {
            width: 38px;
            height: 38px;
            background-color: #ecfdf5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-brand .brand-text {
            margin-left: 12px;
            font-weight: 800;
            font-size: 1.15rem;
            color: #ffffff; /* White text for dark sidebar */
            transition: opacity 0.2s, visibility 0.2s;
            opacity: 0;
            visibility: hidden;
            white-space: nowrap;
        }
        .sidebar:hover .sidebar-brand .brand-text {
            opacity: 1;
            visibility: visible;
        }

        /* Sidebar Menu Items */
        .sidebar-menu {
            list-style: none;
            padding: 1.5rem 0 0 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
            flex-grow: 1;
        }
        .sidebar-item {
            margin: 4px 10px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            height: 44px;
            padding: 0;
            color: #a7f3d0; /* Emerald-200 */
            font-weight: 600;
            font-size: 0.825rem;
            text-decoration: none;
            border-radius: 8px;
            margin: 4px 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            white-space: nowrap;
        }
        .sidebar-link i {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            width: 50px; /* Same width as the collapsed link to perfectly center the icon via Flexbox */
            height: 100%;
            flex-shrink: 0;
            color: #6ee7b7; /* Emerald-300 */
            transition: color 0.2s;
        }
        .sidebar-link span {
            padding-right: 15px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s, visibility 0.2s;
        }
        .sidebar:hover .sidebar-link span {
            opacity: 1;
            visibility: visible;
        }

        /* Active Menu Link */
        .sidebar-link.active-link-custom {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            box-shadow: none;
        }
        .sidebar-link.active-link-custom i {
            color: #10b981 !important;
        }

        /* Hover link */
        .sidebar-link:hover:not(.active-link-custom) {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar-link:hover i {
            color: #10b981;
        }

        /* Special Approval Highlight */
        .sidebar-link.nav-approval-highlight {
            background-color: #e0f2fe !important;
            color: #0284c7 !important;
        }
        .sidebar-link.nav-approval-highlight i {
            color: #0284c7 !important;
        }
        .sidebar-link.nav-approval-highlight:hover {
            background-color: #bae6fd !important;
        }

        /* Active Menu Link Blue Variant (Dark Blue Tone) */
        .sidebar-link.active-link-blue-main {
            color: #60a5fa !important; /* Blue-400 */
            background-color: rgba(37, 99, 235, 0.3) !important; /* Blue-600 */
            border: 1px solid rgba(37, 99, 235, 0.5) !important;
            box-shadow: none;
        }
        .sidebar-link.active-link-blue-main i {
            color: #3b82f6 !important; /* Blue-500 */
        }
        
        .sidebar-link.hover-blue-main:hover:not(.active-link-blue-main) {
            color: #93c5fd;
            background-color: rgba(37, 99, 235, 0.2);
        }
        .sidebar-link.hover-blue-main:hover i {
            color: #60a5fa;
        }

        /* Dropdown Submenu inside Sidebar */
        .sidebar-dropdown-menu {
            list-style: none;
            padding-left: 0;
            margin: 4px 15px 4px 44px; /* Neatly aligned with parent text */
            display: none;
        }
        .sidebar-dropdown-link {
            display: flex;
            align-items: center;
            height: 34px;
            padding: 0 12px;
            color: #a7f3d0; /* Emerald-200 */
            font-weight: 500;
            font-size: 0.775rem;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .sidebar-dropdown-link i {
            font-size: 0.9rem;
            margin-right: 10px;
        }
        .sidebar-dropdown-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar-dropdown-link.active-link-custom {
            color: #10b981 !important;
            font-weight: 700;
        }
        
        /* Special Submenu Active Links */
        .sidebar-dropdown-link.active-link-blue {
            color: #3b82f6 !important;
            font-weight: 700;
        }
        .sidebar-dropdown-link.active-link-red {
            color: #ef4444 !important;
            font-weight: 700;
        }
        .sidebar-dropdown-link.hover-blue:hover {
            color: #bfdbfe !important;
            background-color: rgba(59, 130, 246, 0.15) !important;
        }
        .sidebar-dropdown-link.hover-red:hover {
            color: #fca5a5 !important;
            background-color: rgba(239, 68, 68, 0.15) !important;
        }

        /* Show submenu when active/expanded */
        .sidebar-item.expanded .sidebar-dropdown-menu {
            display: block;
        }

        /* When sidebar is not hovered, force hide dropdown menus */
        .sidebar:not(:hover) .sidebar-dropdown-menu {
            display: none !important;
        }
        .sidebar:not(:hover) .sidebar-link .dropdown-arrow {
            opacity: 0 !important;
            visibility: hidden !important;
        }
        .sidebar-link .dropdown-arrow {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s, visibility 0.2s, transform 0.2s;
        }
        .sidebar:hover .sidebar-link .dropdown-arrow {
            opacity: 1;
            visibility: visible;
        }

        /* Header / Topbar Premium Styling */
        .top-header { position: sticky; top: 0; height: 60px; background: #ffffff; border-bottom: 1px solid rgba(226, 232, 240, 0.6); z-index: 1040; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; }

        /* Expand top-header left padding when sidebar is hovered */
        .sidebar:hover ~ .top-header { position: sticky; top: 0; height: 60px; background: #ffffff; border-bottom: 1px solid rgba(226, 232, 240, 0.6); z-index: 1040; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; }

        /* Adjust Main Content Layout */
        .main-wrapper { 
            display: flex; flex-direction: column; flex-grow: 1; min-width: 0; min-height: 100vh; 
            margin-left: 16px; 
            background-color: var(--bg-page);
            transition: none;
            transform-origin: right center;
        }

        .sidebar:hover ~ .main-wrapper {
            margin-left: 16px; /* Ditetapkan 16px agar konten web tidak geser/menyusut (overlay sidebar) */
        }

        /* Sidebar Mobile backdrop overlay */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(15, 23, 42, 0.5); /* Solid dark overlay */
            z-index: 1055;
            transition: opacity 0.3s ease;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                width: 260px;
                transform: translateX(-100%);
                box-shadow: none;
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .sidebar.show-mobile-sidebar {
                transform: translateX(0);
                box-shadow: 10px 0 25px rgba(0, 0, 0, 0.15);
                overflow-y: auto;
            }
            .sidebar.show-mobile-sidebar .brand-text,
            .sidebar.show-mobile-sidebar .sidebar-link span,
            .sidebar.show-mobile-sidebar .dropdown-arrow {
                opacity: 1 !important;
                visibility: visible !important;
            }
            .sidebar:not(.show-mobile-sidebar) .brand-text,
            .sidebar:not(.show-mobile-sidebar) .sidebar-link span,
            .sidebar:not(.show-mobile-sidebar) .dropdown-arrow {
                opacity: 1 !important; /* Di mobile, teks sidebar selalu siap karena width tetap 260px (off-canvas) */
                visibility: visible !important;
            }
            .top-header { position: sticky; top: 0; height: 60px; background: #ffffff; border-bottom: 1px solid rgba(226, 232, 240, 0.6); z-index: 1040; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; }
            .sidebar:hover ~ .top-header { position: sticky; top: 0; height: 60px; background: #ffffff; border-bottom: 1px solid rgba(226, 232, 240, 0.6); z-index: 1040; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; }
            .main-wrapper { display: flex; flex-direction: column; flex-grow: 1; min-width: 0; min-height: 100vh; margin-left: 0; transform: none !important; border-radius: 0 !important; }
        }

        /* Profile Dropdown elements */
        .dropdown-menu {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border-panel); padding: 0.5rem;
            border-radius: 14px; background-color: #ffffff;
            animation: fadeInDropdown 0.2s ease forwards; transform-origin: top;
        }
        @keyframes fadeInDropdown { from { opacity: 0; transform: scaleY(0.95); } to { opacity: 1; transform: scaleY(1); } }
        .dropdown-item { border-radius: 8px; transition: background-color 0.2s, color 0.2s; font-weight: 500; }
        .dropdown-item:hover { background-color: var(--bg-page); }

        .btn-profile-dropdown {
            background-color: #ffffff; border: 1px solid var(--border-panel);
            padding: 0.35rem 0.85rem 0.35rem 0.35rem; border-radius: 999px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: var(--shadow-soft); cursor: pointer;
        }
        .btn-profile-dropdown:hover, .btn-profile-dropdown[aria-expanded="true"] {
            background-color: var(--bg-page); border-color: #cbd5e1; box-shadow: var(--shadow-hover);
        }
        
        main.container { animation: fadeInContent 0.5s ease-out forwards; }
        @keyframes fadeInContent { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* MODAL PREMIUM STYLING */
        .modal-content.premium-modal { border: none; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; }
        .modal-header.bg-emerald-soft { background-color: #ecfdf5 !important; border-bottom: 1px solid #d1fae5 !important; padding: 1.25rem 1.5rem; }
        .modal-body.bg-slate-50 { background-color: #f8fafc !important; padding: 1.5rem; }

        /* TEMA TABEL PREMIUM MENTARI ATLAS */
        .table-wrapper-mentari { border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: visible; border: 1px solid #e2e8f0; background-color: white; margin-bottom: 1.5rem; }
        .table-mentari { width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0; }
        .table-mentari thead th { 
            background: #ecfdf5 !important; 
            color: #047857 !important; 
            font-weight: 700 !important; 
            text-transform: uppercase; 
            font-size: 0.7rem; 
            letter-spacing: 0.8px; 
            padding: 1.25rem 1.25rem !important; 
            border-bottom: 2px solid #10b981 !important; 
            white-space: nowrap; 
        }
        .table-mentari tbody tr:nth-child(even) { background-color: #f8fafc !important; }
        .table-mentari tbody tr:hover { background-color: #ecfdf5 !important; transition: background-color 0.2s ease-in-out; }
        .table-mentari tbody td { padding: 1rem 1.25rem; color: #334155; vertical-align: middle; font-size: 0.85rem; border-bottom: 1px solid #e2e8f0; }
        .table-mentari tbody tr:last-child td { border-bottom: none; }
        
        /* Universal Sticky Last Column (Action Column) */
        .table-wrapper-mentari th:last-child,
        .table-wrapper-mentari td:last-child,
        .table-mentari th:last-child,
        .table-mentari td:last-child,
        th.sticky-action,
        td.sticky-action {
            position: sticky;
            right: 0;
            z-index: 2;
        }
        .table-wrapper-mentari thead th:last-child,
        .table-mentari thead th:last-child,
        thead th.sticky-action {
            z-index: 3;
            background: #ecfdf5 !important;
        }
        .table-wrapper-mentari tbody tr td:last-child,
        .table-mentari tbody tr td:last-child,
        tbody tr td.sticky-action {
            background-color: #ffffff; /* match body bg */
            transition: background-color 0.2s ease-in-out;
        }
        .table-wrapper-mentari tbody tr:nth-child(even) td:last-child,
        .table-mentari tbody tr:nth-child(even) td:last-child,
        tbody tr:nth-child(even) td.sticky-action {
            background-color: #f8fafc !important;
        }
        .table-wrapper-mentari tbody tr:hover td:last-child,
        .table-mentari tbody tr:hover td:last-child,
        tbody tr:hover td.sticky-action {
            background-color: #ecfdf5 !important;
        }
        .table-wrapper-mentari tfoot tr td:last-child,
        .table-mentari tfoot tr td:last-child,
        tfoot tr td.sticky-action {
            background-color: #f8f9fa !important;
        }
        .table-wrapper-mentari th:last-child::before,
        .table-wrapper-mentari td:last-child::before,
        .table-mentari th:last-child::before,
        .table-mentari td:last-child::before,
        th.sticky-action::before,
        td.sticky-action::before {
            content: '';
            position: absolute;
            top: 0;
            left: -5px;
            bottom: 0;
            width: 5px;
            box-shadow: inset -5px 0 5px -5px rgba(0,0,0,0.15);
            pointer-events: none;
        }
        /* Fix Select2 Text Overlap with Clear (x) and Dropdown (v) icons */
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding-right: 45px !important;
        }

        /* Modern Profile Button Hover */
        .profile-btn-modern:hover {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }
          /* FIX MOBILE SIDEBAR INVISIBLE MENU */
      @media (max-width: 991.98px) {
          .sidebar.show-mobile-sidebar .sidebar-brand,
          .sidebar.show-mobile-sidebar .sidebar-menu {
              opacity: 1 !important;
              visibility: visible !important;
          }
      }

        /* BOTTOM NAVIGATION - GLASSMORPHISM */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 65px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-top: 1px solid rgba(226, 232, 240, 0.6);
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1000;
            padding-bottom: env(safe-area-inset-bottom);
        }
        [data-theme="dark"] .bottom-nav {
            background: rgba(30, 41, 59, 0.85);
            border-top: 1px solid #334155;
        }
        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
            width: 100%;
            height: 100%;
        }
        .bottom-nav-item i {
            font-size: 1.25rem;
            margin-bottom: 4px;
            transition: transform 0.2s, color 0.2s;
        }
        .bottom-nav-item.active {
            color: var(--accent);
        }
        .bottom-nav-item.active i {
            transform: translateY(-2px);
            color: var(--accent);
            text-shadow: 0 4px 10px rgba(16, 185, 129, 0.4);
        }
        .bottom-nav-item:hover {
            color: var(--accent);
        }
        
        /* Give padding to body on mobile so content isn't hidden behind bottom nav */
        @media (max-width: 991.98px) {
            body {
                padding-bottom: 70px;
            }
        }
</style>

</head>
<body>

    <div id="top-loader"></div>

    <div class="d-flex flex-row w-100 min-vh-100">
    @auth
    @php
        $userRole = strtolower(Auth::user()->role);
        $hakAkses = Auth::user()->hak_akses ?? [];
        $isDirektur = ($userRole == 'direktur' || $userRole == 'superadmin');
        $isSales = ($userRole == 'sales');
    @endphp

    <div class="sidebar-backdrop d-none" id="sidebar-backdrop" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.5); z-index: 1055;"></div>
      <div class="sidebar {{ $isSales ? 'd-none d-lg-flex' : '' }}">
        <div class="sidebar-brand">
            <div class="brand-logo shadow-sm">
                <i class="fas fa-layer-group text-success-custom fs-5"></i>
            </div>
            <div class="brand-text d-flex flex-column justify-content-center">
                <span class="lh-1">Mentari<span class="text-success-custom">Atlas</span></span>
                <span class="text-slate-muted text-uppercase" style="font-size: 0.55rem; letter-spacing: 2.5px; margin-top: 3px; font-weight: 700;">Indonesia</span>
            </div>
        </div>

        

        
        
        <ul class="sidebar-menu">
            {{-- Dashboard Utama --}}
            <li class="sidebar-item">
                @if(in_array($userRole, ['admin_warehouse', 'warehouse', 'admin warehouse']))
                    <a class="sidebar-link {{ request()->is('warehouse/dashboard') ? 'active-link-custom' : '' }}" href="{{ route('warehouse.dashboard') }}">
                        <i class="fas fa-chart-pie"></i><span>Ruang Gudang</span>
                    </a>
                @elseif(in_array($userRole, ['admin_keuangan', 'keuangan', 'admin keuangan']))
                    <a class="sidebar-link {{ request()->is('keuangan/dashboard') ? 'active-link-custom' : '' }}" href="{{ route('keuangan.dashboard') }}">
                        <i class="fas fa-chart-line"></i><span>Ruang Keuangan</span>
                    </a>
                @elseif(in_array($userRole, ['sales', 'marketing']))
                    <a class="sidebar-link {{ request()->is('sales/dashboard') ? 'active-link-custom' : '' }}" href="{{ route('sales.dashboard') }}">
                        <i class="fas fa-briefcase"></i><span>Ruang Sales</span>
                    </a>
                @else
                    <a class="sidebar-link {{ request()->is('dashboard') ? 'active-link-custom' : '' }}" href="{{ url('/dashboard') }}">
                        <i class="fas fa-th-large"></i><span>Dashboard Utama</span>
                    </a>
                @endif
            </li>

            {{-- 1. Approval SO --}}
            @if($isDirektur || in_array('approval_so', $hakAkses))
            <li class="sidebar-item">
                <a class="sidebar-link hover-blue-main {{ request()->is('penjualan/approval*') ? 'active-link-blue-main' : '' }}" href="{{ route('penjualan.approval') }}">
                    <i class="fas fa-stamp" style="color: #60a5fa;"></i><span>Approval SO</span>
                </a>
            </li>
            @endif

            {{-- 2. Buat Order (Only for Sales) --}}
            @if($isSales)
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->is('penjualan/buat*') ? 'active-link-custom' : '' }}" href="{{ url('/penjualan/buat') }}">
                    <i class="fas fa-plus-circle"></i><span>Buat Order</span>
                </a>
            </li>
            @endif

            {{-- 3. Riwayat SO --}}
            @if($isDirektur || $isSales || in_array('riwayat_so', $hakAkses))
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->is('penjualan') || (request()->is('penjualan/*') && !request()->is('penjualan/approval*') && !request()->is('penjualan/buat*') && !request()->is('penjualan/edit*')) ? 'active-link-custom' : '' }}" href="{{ route('penjualan.index') }}">
                    <i class="fas fa-file-invoice"></i><span>Riwayat SO</span>
                </a>
            </li>
            @endif

            {{-- 3. Data Barang --}}
            @if(!$isSales && ($isDirektur || in_array('data_barang', $hakAkses)))
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->is('barang*') ? 'active-link-custom' : '' }}" href="{{ url('/barang') }}">
                    <i class="fas fa-boxes"></i><span>Data Barang</span>
                </a>
            </li>
            @endif

            {{-- 4. Back Order --}}
            @if(!$isSales && ($isDirektur || in_array('backorder', $hakAkses)))
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('backorder.*') ? 'active-link-custom' : '' }}" href="{{ route('backorder.index') }}">
                    <i class="fas fa-hourglass-half"></i><span>Back Order</span>
                </a>
            </li>
            @endif

            {{-- 5. Pembelian Stok --}}
            @if(!$isSales && ($isDirektur || in_array('pembelian_stok', $hakAkses)))
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->is('pembelian*') ? 'active-link-custom' : '' }}" href="{{ url('/pembelian') }}">
                    <i class="fas fa-shopping-bag"></i><span>Pembelian Stok</span>
                </a>
            </li>
            @endif

            {{-- 6. Return Barang (Dropdown) --}}
            @if(!$isSales && ($isDirektur || in_array('return_barang', $hakAkses)))
            @php $isReturActive = request()->is('*retur*'); @endphp
            <li class="sidebar-item {{ $isReturActive ? 'expanded' : '' }}">
                <a href="#" class="sidebar-link has-dropdown {{ $isReturActive ? 'active-link-custom' : '' }}">
                    <i class="fas fa-arrow-rotate-left"></i>
                    <span>Return Barang</span>
                    <i class="fas fa-chevron-down ms-auto dropdown-arrow transition-all" style="font-size: 0.7rem; {{ $isReturActive ? 'transform: rotate(180deg);' : '' }}"></i>
                </a>
                <ul class="sidebar-dropdown-menu">
                    <li>
                        <a class="sidebar-dropdown-link hover-blue {{ request()->routeIs('retur.penjualan.index') ? 'active-link-blue' : '' }}" href="{{ route('retur.penjualan.index') }}">
                            <i class="fas fa-undo text-info"></i><span>Return Penjualan</span>
                        </a>
                    </li>
                    <li>
                        <a class="sidebar-dropdown-link hover-red {{ request()->routeIs('retur.pembelian.index') ? 'active-link-red' : '' }}" href="{{ route('retur.pembelian.index') }}">
                            <i class="fas fa-truck-loading text-danger"></i><span>Return Pembelian</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- 7. Keuangan (Dropdown) --}}
            @if(!$isSales && ($isDirektur || in_array('akses_keuangan', $hakAkses)))
            @php $isKeuanganActive = request()->is('*keuangan*'); @endphp
            <li class="sidebar-item {{ $isKeuanganActive ? 'expanded' : '' }}">
                <a href="#" class="sidebar-link has-dropdown {{ $isKeuanganActive ? 'active-link-custom' : '' }}">
                    <i class="fas fa-vault"></i>
                    <span>Keuangan</span>
                    <i class="fas fa-chevron-down ms-auto dropdown-arrow transition-all" style="font-size: 0.7rem; {{ $isKeuanganActive ? 'transform: rotate(180deg);' : '' }}"></i>
                </a>
                <ul class="sidebar-dropdown-menu">
                    <li>
                        <a class="sidebar-dropdown-link hover-blue {{ request()->routeIs('keuangan.piutang.index') ? 'active-link-blue' : '' }}" href="{{ route('keuangan.piutang.index') }}">
                            <i class="fas fa-hand-holding-usd text-info"></i><span>Piutang Customer</span>
                        </a>
                    </li>
                    <li>
                        <a class="sidebar-dropdown-link hover-red {{ request()->routeIs('keuangan.utang.index') ? 'active-link-red' : '' }}" href="{{ route('keuangan.utang.index') }}">
                            <i class="fas fa-file-invoice-dollar text-danger"></i><span>Utang Supplier</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- 8. Data Customer --}}
            @if(!$isSales && ($isDirektur || in_array('tingkat_cust', $hakAkses)))
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->is('customer*') ? 'active-link-custom' : '' }}" href="{{ route('customer.index') }}">
                    <i class="fas fa-users-cog text-success-custom"></i><span>Data Customer</span>
                </a>
            </li>
            @endif

            {{-- 9. Data Supplier --}}
            @if(!$isSales && ($isDirektur || in_array('data_supplier', $hakAkses)))
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->is('supplier*') ? 'active-link-custom' : '' }}" href="{{ route('supplier.index') }}">
                    <i class="fas fa-truck text-success-custom"></i><span>Data Supplier</span>
                </a>
            </li>
            @endif

            {{-- 10. Profit/Laba --}}
            @if(!$isSales && ($isDirektur || in_array('profit_laba', $hakAkses)))
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->is('laporan/laba*') ? 'active-link-custom' : '' }}" href="{{ route('laba.index') }}">
                    <i class="fas fa-chart-line"></i><span>Profit / Laba</span>
                </a>
            </li>
            @endif

            {{-- 11. Unduh Laporan --}}
            @if(!$isSales && ($isDirektur || in_array('unduh_laporan', $hakAkses)))
            <li class="sidebar-item">
                <a class="sidebar-link" href="#" onclick="openUnduhLaporanModal(event)">
                    <i class="fas fa-file-export"></i><span>Unduh Laporan</span>
                </a>
            </li>
            @endif

            {{-- 12. Audit Trail --}}
            @if(!$isSales && ($isDirektur || in_array('audit_trail', $hakAkses)))
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('activity_logs.index') ? 'active-link-custom' : '' }}" href="{{ route('activity_logs.index') }}">
                    <i class="fas fa-history"></i><span>Audit Trail</span>
                </a>
            </li>
            @endif

            {{-- 13. Akun Staf --}}
            @if(!$isSales && ($isDirektur || in_array('akun_staf', $hakAkses)))
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->is('users*') ? 'active-link-custom' : '' }}" href="{{ url('/users') }}">
                    <i class="fas fa-user-gear"></i><span>Akun Staf</span>
                </a>
            </li>
            @endif
        </ul>
    </div>

    <div class="main-wrapper">
        <header class="top-header">
            <div class="d-flex align-items-center gap-2">
                @if(strtolower(Auth::user()->role) != 'sales')
                <!-- Hamburger button for mobile devices -->
                <button class="btn btn-link text-slate-dark d-lg-none p-0 me-2 border-0" id="sidebar-toggle-btn" style="font-size: 1.25rem;">
                    <i class="fas fa-bars text-success-custom"></i>
                </button>
                @endif
                
                <!-- Breadcrumbs / Page Title (Desktop) -->
                <nav aria-label="breadcrumb" class="d-none d-lg-block">
                    <ol class="breadcrumb mb-0 align-items-center" id="header-breadcrumb">
                        <li class="breadcrumb-item text-muted fw-medium" style="font-size: 0.85rem;"><i class="fas fa-home me-1 text-success-custom"></i> Mentari<span class="text-success-custom fw-semibold">Atlas</span> Indonesia</li>
                        <li class="breadcrumb-item active text-slate-dark fw-semibold" id="breadcrumb-active-item" style="font-size: 0.85rem;">Dashboard</li>
                    </ol>
                </nav>
                
                <!-- Brand title for mobile screens -->
                <div class="d-flex flex-column justify-content-center d-lg-none">
                    <span class="fw-bold text-slate-dark lh-1" style="font-size: 1.15rem; letter-spacing: -0.5px;">Mentari<span class="text-success-custom">Atlas</span></span>
                    <span class="text-slate-muted text-uppercase" style="font-size: 0.55rem; letter-spacing: 2px; margin-top: 2px; font-weight: 700;">Indonesia</span>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">

                @if(request()->is('dashboard'))
                <!-- Notification feature removed -->
                @endif

                <!-- User Profile -->
                <div class="dropdown">
                    <a class="d-flex align-items-center text-decoration-none bg-light rounded-pill px-2 py-1 shadow-sm border profile-btn-modern" href="#" role="button" data-bs-toggle="dropdown" style="transition: all 0.2s;">
                        <div class="text-white rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 32px; height: 32px; background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
                            <i class="fas fa-user fa-sm"></i>
                        </div> 
                        <div class="d-flex flex-column align-items-start me-2">
                            <span class="fw-bolder text-slate-dark lh-1" style="font-size: 0.85rem; letter-spacing: -0.3px;">{{ Auth::user()->name }}</span>
                            <span class="text-emerald-custom fw-bold lh-1 mt-1 text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.5px;">{{ str_replace('_', ' ', Auth::user()->role) }}</span>
                        </div>
                        <i class="fas fa-chevron-down text-slate-muted ms-1 me-1" style="font-size: 0.7rem;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end mt-2 p-2 shadow-lg" style="min-width: 200px; border-radius: 12px;">
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded-2 text-slate-dark d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#profileModal" style="font-size: 0.85rem;">
                                <div class="bg-success-subtle rounded d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 32px; height: 32px; min-width: 32px;"><i class="fas fa-id-card fa-fw text-emerald-custom"></i></div>
                                <span class="fw-medium">Profil Saya</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider border-light my-2"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 px-3 rounded-2 text-danger fw-bold d-flex align-items-center w-100 border-0 bg-transparent text-start" style="font-size: 0.85rem;">
                                    <div class="bg-danger-subtle rounded d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 32px; height: 32px; min-width: 32px;"><i class="fas fa-power-off fa-fw text-danger"></i></div>
                                    <span>Keluar Aplikasi</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
    @endauth

    <main class="container-fluid p-4" style="flex: 1;">
                
        <!-- Skeleton Loader Overlay HTML -->
        <div id="skeleton-overlay">
            <div class="d-flex mb-4 align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="skeleton-box" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 15px; margin-bottom: 0;"></div>
                    <div class="skeleton-box" style="width: 180px; height: 28px; margin-bottom: 0;"></div>
                </div>
                <div class="skeleton-box d-none d-md-block" style="width: 120px; height: 36px; border-radius: 20px; margin-bottom: 0;"></div>
            </div>
            <div class="row mb-3">
                <div class="col-12 col-md-4">
                    <div class="skeleton-box" style="height: 110px;"></div>
                </div>
                <div class="col-12 col-md-4 d-none d-md-block">
                    <div class="skeleton-box" style="height: 110px;"></div>
                </div>
                <div class="col-12 col-md-4 d-none d-md-block">
                    <div class="skeleton-box" style="height: 110px;"></div>
                </div>
            </div>
            <div class="skeleton-box" style="flex-grow: 1; min-height: 200px;"></div>
        </div>

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    {{-- Script Validasi & UI Lainnya --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi Global Select2 dengan Tema Bootstrap 5
            if ($.fn.select2) {
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Pilih salah satu...'
                });
            }

            // Konfigurasi Toast Premium
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                background: 'rgba(255, 255, 255, 0.95)',
                color: '#0f172a',
                customClass: {
                    popup: 'shadow-sm border border-slate-200'
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if(session('success'))
                Toast.fire({ icon: 'success', title: {!! json_encode(session('success')) !!} });
            @endif

            @if(session('error'))
                Toast.fire({ icon: 'error', title: {!! json_encode(session('error')) !!} });
            @endif
        });

        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.btn-delete');
            if (deleteBtn) {
                e.preventDefault(); const form = deleteBtn.closest('form'); 
                Swal.fire({ title: 'Apakah Anda yakin?', text: "Data yang dihapus tidak dapat dikembalikan!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#64748b', confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus Data', cancelButtonText: 'Batal', reverseButtons: true })
                .then((result) => { if (result.isConfirmed) { form.submit(); } });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => { document.body.appendChild(modal); });

            // Toggle Sidebar Dropdowns
            document.querySelectorAll('.sidebar-link.has-dropdown').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const parent = this.closest('.sidebar-item');
                    
                    // Toggle expanded class
                    parent.classList.toggle('expanded');
                    
                    // Rotate arrow icon
                    const arrow = this.querySelector('.dropdown-arrow');
                    if (arrow) {
                        if (parent.classList.contains('expanded')) {
                            arrow.style.transform = 'rotate(180deg)';
                        } else {
                            arrow.style.transform = 'rotate(0deg)';
                        }
                    }
                });
            });

            // Mobile Sidebar Toggling
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle-btn');
            const backdrop = document.getElementById('sidebar-backdrop');

            if (toggleBtn && sidebar && backdrop) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('show-mobile-sidebar');
                    backdrop.classList.toggle('d-none');
                });

                backdrop.addEventListener('click', function() {
                    sidebar.classList.remove('show-mobile-sidebar');
                    backdrop.classList.add('d-none');
                });

                // Auto close sidebar when ANY normal link is clicked (excluding dropdown toggles)
                document.querySelectorAll('.sidebar-link:not(.has-dropdown), .sidebar-dropdown-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        // Don't close if it's just a hash link that doesn't trigger modal
                        if(this.getAttribute('href') === '#' && !this.hasAttribute('data-bs-toggle')) return;
                        
                        sidebar.classList.remove('show-mobile-sidebar');
                        backdrop.classList.add('d-none');
                    });
                });
            }

            // Dynamic Breadcrumb / Page Title in Header
            const activeLink = document.querySelector('.sidebar-link.active-link-custom');
            const activeDropdownLink = document.querySelector('.sidebar-dropdown-link.active-link-custom');
            const breadcrumbActive = document.getElementById('breadcrumb-active-item');

            if (activeDropdownLink) {
                const parentItem = activeDropdownLink.closest('.sidebar-item');
                const parentLinkText = parentItem ? parentItem.querySelector('.sidebar-link span').textContent.trim() : '';
                const childLinkText = activeDropdownLink.querySelector('span').textContent.trim();
                if (breadcrumbActive) {
                    breadcrumbActive.innerHTML = `${parentLinkText} <span class="text-muted mx-2">/</span> ${childLinkText}`;
                }
            } else if (activeLink) {
                const linkText = activeLink.querySelector('span').textContent.trim();
                if (breadcrumbActive) {
                    breadcrumbActive.textContent = linkText;
                }
            }

            // GLOBAL FORM SUBMIT LOADING STATE (Pencegahan Double Submit)
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        // Jangan override form delete (sudah ditangani Swal)
                        if (this.classList.contains('form-delete') || submitBtn.classList.contains('btn-delete') || submitBtn.getAttribute('id') === 'btn-delete') {
                            return;
                        }
                        // Cek form yang targetnya blank (misal ekspor laporan), tidak perlu loading
                        if (this.getAttribute('target') === '_blank') {
                            return;
                        }
                        
                        submitBtn.disabled = true;
                        // Simpan lebar tombol agar tidak menyusut/membesar tiba-tiba
                        submitBtn.style.minWidth = submitBtn.offsetWidth + 'px';
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses...';
                    }
                });
            });

            @if(request()->is('dashboard'))
            // ==========================================
            // REAL-TIME NOTIFICATION POLLING
            // ==========================================
            function fetchNotifications() {
                fetch('{{ route('api.notifications') }}')
                    .then(response => response.json())
                    .then(data => {
                        const badge = document.getElementById('notif-badge');
                        const emptyState = document.getElementById('notif-empty');
                        
                        const itemApproval = document.getElementById('notif-item-approval');
                        const countApproval = document.getElementById('notif-count-approval');
                        
                        const itemLowStock = document.getElementById('notif-item-low-stock');
                        const countLowStock = document.getElementById('notif-count-low-stock');

                        const itemOutOfStock = document.getElementById('notif-item-out-of-stock');
                        const countOutOfStock = document.getElementById('notif-count-out-of-stock');

                        const itemBackOrder = document.getElementById('notif-item-back-order');
                        const countBackOrder = document.getElementById('notif-count-back-order');

                        const itemPiutang = document.getElementById('notif-item-piutang');
                        const countPiutang = document.getElementById('notif-count-piutang');

                        const itemUtang = document.getElementById('notif-item-utang');
                        const countUtang = document.getElementById('notif-count-utang');

                        const itemRetur = document.getElementById('notif-item-retur');
                        const countRetur = document.getElementById('notif-count-retur');

                        // Update Bell Badge
                        if (data.total > 0) {
                            badge.textContent = data.total;
                            badge.classList.remove('d-none');
                            badge.classList.add('animate__animated', 'animate__headShake');
                            setTimeout(() => { badge.classList.remove('animate__animated', 'animate__headShake'); }, 1000);
                            emptyState.classList.add('d-none');
                        } else {
                            badge.classList.add('d-none');
                            emptyState.classList.remove('d-none');
                        }

                        // Update Approval SO
                        if (data.pending_approvals > 0) {
                            itemApproval.classList.remove('d-none');
                            countApproval.textContent = data.pending_approvals;
                        } else {
                            itemApproval.classList.add('d-none');
                        }

                        // Update Low Stock
                        if (data.low_stock > 0) {
                            itemLowStock.classList.remove('d-none');
                            countLowStock.textContent = data.low_stock;
                        } else {
                            itemLowStock.classList.add('d-none');
                        }

                        // Update Out of Stock
                        if (data.out_of_stock > 0) {
                            itemOutOfStock.classList.remove('d-none');
                            countOutOfStock.textContent = data.out_of_stock;
                        } else {
                            itemOutOfStock.classList.add('d-none');
                        }

                        // Update Back Order
                        if (data.back_order > 0) {
                            itemBackOrder.classList.remove('d-none');
                            countBackOrder.textContent = data.back_order;
                        } else {
                            itemBackOrder.classList.add('d-none');
                        }

                        // Update Overdue Piutang
                        if (data.overdue_piutang > 0) {
                            itemPiutang.classList.remove('d-none');
                            countPiutang.textContent = data.overdue_piutang;
                        } else {
                            itemPiutang.classList.add('d-none');
                        }

                        // Update Overdue Utang
                        if (data.overdue_utang > 0) {
                            itemUtang.classList.remove('d-none');
                            countUtang.textContent = data.overdue_utang;
                        } else {
                            itemUtang.classList.add('d-none');
                        }

                        // Update Retur Pending
                        if (data.retur_pending > 0) {
                            itemRetur.classList.remove('d-none');
                            countRetur.textContent = data.retur_pending;
                        } else {
                            itemRetur.classList.add('d-none');
                        }
                    })
                    .catch(error => console.error('Error fetching notifications:', error));
            }

            // Jalankan sekali saat halaman dimuat
            fetchNotifications();
            
            // Polling setiap 15 detik (15000 ms)
            setInterval(fetchNotifications, 15000);
            @endif
            
        });
    </script>
    </div>

    <!-- MODAL UNDUH LAPORAN (MINIMALIS) -->
    <div class="modal fade" id="modalUnduhLaporan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
                <div class="modal-header text-white border-0" style="background-color: #10b981; padding: 1rem 1.25rem;">
                    <h5 class="modal-title fw-bold mb-0"><i class="fas fa-file-export me-2"></i> Form Unduh Laporan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4 bg-light">
                    <form action="{{ route('laporan.generate') }}" method="POST" target="_blank">
                        @csrf
                        
                        {{-- 1. KATEGORI --}}
                        <div class="mb-3">
                            <label class="form-label text-slate-dark fw-bold small text-uppercase mb-1" style="font-size: 0.7rem;">Kategori Laporan</label>
                            <select name="kategori_laporan" id="modal_kategori_laporan" class="form-select form-select-sm border-0 shadow-sm" required style="border-radius: 0.5rem; padding: 0.5rem;">
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                <option value="penjualan">Sales Order (Penjualan)</option>
                                <option value="pembelian">Purchase Order (Pembelian)</option>
                                <option value="piutang">Keuangan (Piutang Customer)</option>
                                <option value="utang">Keuangan (Utang Supplier)</option>
                                <option value="cn">Credit Note (Potongan Jual)</option>
                                <option value="dn">Debit Note (Potongan Beli)</option>
                                <option value="retur_jual">Riwayat Retur Penjualan</option>
                                <option value="retur_beli">Riwayat Retur Pembelian</option>
                                <option value="backorder">Backorder (Tunggu Stok)</option>
                            </select>
                        </div>

                        {{-- 2. RENTANG WAKTU --}}
                        <div class="mb-3">
                            <label class="form-label text-slate-dark fw-bold small text-uppercase mb-1" style="font-size: 0.7rem;">Rentang Waktu</label>
                            <select name="periode" id="modal_periode" class="form-select form-select-sm border-0 shadow-sm mb-2" style="border-radius: 0.5rem; padding: 0.5rem;">
                                <option value="custom">Per Periode Tanggal</option>
                                <option value="bulan_ini">Bulan Ini</option>
                                <option value="tahun_ini">Tahun Ini</option>
                                <option value="semua">Semua Transaksi (Total)</option>
                            </select>

                            <div class="row g-2 d-none" id="modal_custom_date_wrapper">
                                <div class="col-6">
                                    <input type="date" name="start_date" class="form-control form-control-sm border-0 shadow-sm" style="border-radius: 0.5rem;">
                                </div>
                                <div class="col-6">
                                    <input type="date" name="end_date" class="form-control form-control-sm border-0 shadow-sm" style="border-radius: 0.5rem;">
                                </div>
                            </div>
                        </div>

                        {{-- 3. FILTER GROUPING --}}
                        <div class="mb-3 d-none" id="modal_grouping_section">
                            <label class="form-label text-slate-dark fw-bold small text-uppercase mb-1" style="font-size: 0.7rem;">Parameter Kelompok</label>
                            <div class="p-2 bg-white shadow-sm" id="modal_grouping_options" style="border-radius: 0.5rem; border: 1px dashed #cbd5e1;">
                                <!-- Dinamis -->
                            </div>
                        </div>

                        {{-- 4. FORMAT --}}
                        <div class="mb-3">
                            <label class="form-label text-slate-dark fw-bold small text-uppercase mb-1" style="font-size: 0.7rem;">Format</label>
                            <div class="d-flex gap-3 bg-white p-2 shadow-sm" style="border-radius: 0.5rem;">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="format_export" id="modal_format_excel" value="excel" checked>
                                    <label class="form-check-label small fw-bold text-success" for="modal_format_excel"><i class="fas fa-file-excel me-1"></i>Excel</label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="format_export" id="modal_format_pdf" value="pdf">
                                    <label class="form-check-label small fw-bold text-danger" for="modal_format_pdf"><i class="fas fa-file-pdf me-1"></i>PDF</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn text-white rounded-pill fw-bold btn-sm py-2 shadow-sm" style="background-color: #10b981; transition: 0.2s;" onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10b981'">
                                <i class="fas fa-download me-2"></i> Ekspor Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi manual untuk buka modal Unduh Laporan (mencegah bentrok dengan Bootstrap toggle)
        window.openUnduhLaporanModal = function(e) {
            if(e) e.preventDefault();
            // 1. Tutup sidebar secara paksa
            var sidebar = document.querySelector('.sidebar');
            var backdrop = document.getElementById('sidebar-backdrop');
            if (sidebar) sidebar.classList.remove('show-mobile-sidebar');
            if (backdrop) backdrop.classList.add('d-none');

            // 2. Tampilkan modal setelah sidebar ditutup (berikan jeda sedikit)
            setTimeout(function() {
                var modalEl = document.getElementById('modalUnduhLaporan');
                if (modalEl) {
                    var modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
            }, 150);
        };

        document.addEventListener('DOMContentLoaded', function() {
            const modalKategori = document.getElementById('modal_kategori_laporan');
            const modalPeriode = document.getElementById('modal_periode');
            
            if(!modalKategori) return;

            function toggleModalCustomDate() {
                var wrapper = document.getElementById('modal_custom_date_wrapper');
                if (modalPeriode.value === 'custom') {
                    wrapper.classList.remove('d-none');
                } else {
                    wrapper.classList.add('d-none');
                }
            }

            function updateModalGroupingOptions() {
                var kategori = modalKategori.value;
                var section = document.getElementById('modal_grouping_section');
                var container = document.getElementById('modal_grouping_options');
                
                container.innerHTML = ''; 
                var options = [];

                if(kategori === 'penjualan') {
                    options = [
                        {val: 'salesman', label: 'Per Salesman (Staf)'},
                        {val: 'merek', label: 'Per Merek / Brand'},
                        {val: 'customer', label: 'Per Customer'}
                    ];
                } else if(kategori === 'pembelian' || kategori === 'utang' || kategori === 'dn') {
                    options = [
                        {val: 'supplier', label: 'Per Supplier'},
                        {val: 'merek', label: 'Per Merek Barang'}
                    ];
                } else if(kategori === 'piutang' || kategori === 'cn') {
                    options = [
                        {val: 'customer', label: 'Per Customer'}
                    ];
                } else if(kategori === 'retur_jual') {
                    options = [
                        {val: 'customer', label: 'Per Customer'},
                        {val: 'merek', label: 'Per Merek Barang'}
                    ];
                } else if(kategori === 'retur_beli') {
                    options = [
                        {val: 'supplier', label: 'Per Supplier'}
                    ];
                } else if(kategori === 'backorder') {
                    options = [
                        {val: 'customer', label: 'Per Customer'},
                        {val: 'barang', label: 'Per Barang'}
                    ];
                }

                if (options.length > 0) {
                    section.classList.remove('d-none');
                    options.forEach(function(opt) {
                        container.innerHTML += `
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="group_by[]" id="modal_chk_${opt.val}" value="${opt.val}">
                            <label class="form-check-label small" for="modal_chk_${opt.val}">${opt.label}</label>
                        </div>`;
                    });
                } else {
                    section.classList.add('d-none');
                }
            }

            modalKategori.addEventListener('change', updateModalGroupingOptions);
            modalPeriode.addEventListener('change', toggleModalCustomDate);

            // Initialize on load
            updateModalGroupingOptions();
            toggleModalCustomDate();
        });
    </script>
    @if(Auth::check() && Auth::user()->role === 'direktur')
    <div class="fab-container" id="fabMenuContainer">
        <button class="btn-fab btn-fab-main shadow-lg" id="fabMainBtn" title="Menu Cepat Direktur">
            <i class="fas fa-eye" id="fabMainIcon"></i>
        </button>
        <div class="fab-options" id="fabOptions">
            <div class="fab-item">
                <span class="fab-label">Audit Trail</span>
                <a href="{{ route('activity_logs.index') }}" class="btn-fab btn-fab-audit shadow-sm">
                    <i class="fas fa-history"></i>
                </a>
            </div>
            <div class="fab-item">
                <span class="fab-label">Laba / Profit</span>
                <a href="{{ route('laba.index') }}" class="btn-fab btn-fab-laba shadow-sm">
                    <i class="fas fa-chart-line"></i>
                </a>
            </div>
            <div class="fab-item">
                <span class="fab-label">Approval SO</span>
                <a href="{{ route('penjualan.approval') }}" class="btn-fab btn-fab-approval shadow-sm">
                    <i class="fas fa-clipboard-check"></i>
                </a>
            </div>
        </div>
    </div>
    <style>
                  .fab-container {
              position: fixed;
              bottom: 2rem;
              right: 2rem;
              z-index: 1050;
              display: flex;
              flex-direction: column;
              align-items: flex-end;
          }
          @media (max-width: 991.98px) {
              .fab-container {
                  bottom: 5.5rem !important; /* Naik ke atas bottom nav */
                  right: 1.5rem !important;
              }
              .btn-fab {
                  width: 50px !important;
                  height: 50px !important;
              }
          }
        .btn-fab {
            width: 60px;
            height: 60px;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            border: none;
            cursor: pointer;
            outline: none;
        }
        .btn-fab-main {
            background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
            box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.4), 0 4px 6px -2px rgba(14, 165, 233, 0.2);
            z-index: 2;
        }
        .btn-fab-main:hover { transform: scale(1.05); }
        .fab-container.active .btn-fab-main {
            background: linear-gradient(135deg, #334155 0%, #0f172a 100%);
            transform: rotate(180deg);
        }
        
        .fab-options {
            display: flex;
            flex-direction: column-reverse;
            gap: 1rem;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: absolute;
            bottom: 100%;
            right: 0;
            margin-bottom: 1rem;
            align-items: flex-end;
        }
        .fab-container.active .fab-options {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .fab-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .fab-label {
            background: white;
            color: #334155;
            padding: 0.4rem 0.8rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            opacity: 0;
            transform: translateX(10px);
            transition: all 0.3s ease;
        }
        .fab-container.active .fab-label {
            opacity: 1;
            transform: translateX(0);
        }

        .btn-fab-laba, .btn-fab-approval, .btn-fab-audit { width: 50px; height: 50px; font-size: 1.25rem; }
        
        .btn-fab-audit { background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%); }
        .btn-fab-audit:hover { transform: scale(1.1); color: white; }

        .btn-fab-approval { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .btn-fab-approval:hover { transform: scale(1.1); color: white; }

        .btn-fab-laba { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .btn-fab-laba:hover { transform: scale(1.1); color: white; }

        @media print { .fab-container { display: none !important; } }
          /* FIX MOBILE SIDEBAR INVISIBLE MENU */
      @media (max-width: 991.98px) {
          .sidebar.show-mobile-sidebar .sidebar-brand,
          .sidebar.show-mobile-sidebar .sidebar-menu {
              opacity: 1 !important;
              visibility: visible !important;
          }
      }

        /* BOTTOM NAVIGATION - GLASSMORPHISM */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 65px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-top: 1px solid rgba(226, 232, 240, 0.6);
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1000;
            padding-bottom: env(safe-area-inset-bottom);
        }
        [data-theme="dark"] .bottom-nav {
            background: rgba(30, 41, 59, 0.85);
            border-top: 1px solid #334155;
        }
        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
            width: 100%;
            height: 100%;
        }
        .bottom-nav-item i {
            font-size: 1.25rem;
            margin-bottom: 4px;
            transition: transform 0.2s, color 0.2s;
        }
        .bottom-nav-item.active {
            color: var(--accent);
        }
        .bottom-nav-item.active i {
            transform: translateY(-2px);
            color: var(--accent);
            text-shadow: 0 4px 10px rgba(16, 185, 129, 0.4);
        }
        .bottom-nav-item:hover {
            color: var(--accent);
        }
        
        /* Give padding to body on mobile so content isn't hidden behind bottom nav */
        @media (max-width: 991.98px) {
            body {
                padding-bottom: 70px;
            }
        }
</style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fabContainer = document.getElementById('fabMenuContainer');
            const fabMainBtn = document.getElementById('fabMainBtn');
            const fabMainIcon = document.getElementById('fabMainIcon');
            
            if(fabMainBtn) {
                fabMainBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    fabContainer.classList.toggle('active');
                    if(fabContainer.classList.contains('active')) {
                        fabMainIcon.classList.remove('fa-eye');
                        fabMainIcon.classList.add('fa-times');
                    } else {
                        fabMainIcon.classList.remove('fa-times');
                        fabMainIcon.classList.add('fa-eye');
                    }
                });
                
                // Close when clicking outside
                document.addEventListener('click', function(e) {
                    if (!fabContainer.contains(e.target)) {
                        fabContainer.classList.remove('active');
                        fabMainIcon.classList.remove('fa-times');
                        fabMainIcon.classList.add('fa-eye');
                    }
                });
            }
        });
    </script>
    
    {{-- SCRIPT: GACOR FLOATING TOAST NOTIFICATIONS --}}
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}"
            });
        @endif

        @if(session('error') || $errors->any())
            Toast.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') ?? $errors->first() }}"
            });
        @endif
        
        // FORCE CLEAR DARK MODE (since user skipped it)
        localStorage.removeItem('theme');
        document.documentElement.removeAttribute('data-theme');

        // SKELETON LOADER SCRIPT & BOTTOM NAV FIX
        let skeletonTimeout = null;

        function triggerTransition(targetUrl) {
            // Start top loader progress bar immediately
            const topLoader = document.getElementById('top-loader');
            if (topLoader) {
                topLoader.style.width = '30%';
                setTimeout(() => { topLoader.style.width = '70%'; }, 150);
            }

            // Clear any active timers
            if (skeletonTimeout) clearTimeout(skeletonTimeout);

            // Hide skeleton overlay completely. We will only rely on topLoader to give a sleek native app feel.
            // skeletonTimeout = setTimeout(() => {
            //     const overlay = document.getElementById('skeleton-overlay');
            //     if (overlay) {
            //         overlay.classList.add('active');
            //     }
            // }, 100);
        }

        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            
            // Check if it's a valid navigation link
            if (link && link.href && !link.href.includes('#') && !link.target && !link.hasAttribute('download')) {
                // Ignore javascript: links or same page hashes
                if (link.href.startsWith('javascript:') || link.href.includes('javascript:')) return;
                
                // If the link points to the exact same page, do not trigger skeleton
                if (link.href.replace(/\/$/, '') === window.location.href.replace(/\/$/, '')) {
                    return;
                }

                // Ignore clicks meant for a new tab (Ctrl/Cmd/Shift click or middle mouse button)
                if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button !== 0) return;

                // Stop default browser navigation immediately to prevent DOM freezing
                e.preventDefault();

                // Close mobile sidebar immediately if open
                const sidebar = document.querySelector('.sidebar');
                const backdrop = document.getElementById('sidebar-backdrop');
                if (sidebar) sidebar.classList.remove('show-mobile-sidebar');
                if (backdrop) backdrop.classList.add('d-none');
                
                // If bottom-nav-item, update active navigation state
                if (link.classList.contains('bottom-nav-item')) {
                    document.querySelectorAll('.bottom-nav-item').forEach(nav => nav.classList.remove('active'));
                    link.classList.add('active');
                }
                
                triggerTransition(link.href);

                // Allow UI animations to render for 150ms before the browser main thread freezes for navigation
                setTimeout(() => {
                    window.location.href = link.href;
                }, 150);
            }
        });
        
        // Hide skeleton overlay if user clicks back button in browser (pageshow event)
        window.addEventListener('pageshow', function(event) {
            if (skeletonTimeout) clearTimeout(skeletonTimeout);
            const overlay = document.getElementById('skeleton-overlay');
            if (overlay) {
                overlay.classList.remove('active');
            }
            const topLoader = document.getElementById('top-loader');
            if (topLoader) {
                topLoader.style.width = '0%';
            }
        });

    </script>
    @endif
    <!-- Modal Profil Saya -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 1.25rem; overflow: hidden;">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center pt-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=10b981&color=fff&size=128&bold=true"
                         class="rounded-circle shadow-sm mb-3" style="width: 90px; height: 90px; border: 4px solid #fff;" alt="Profile Picture">
                    
                    <h5 class="fw-bolder text-slate-dark mb-1">{{ Auth::user()->name }}</h5>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success fw-bold px-3 py-2 rounded-pill text-uppercase mb-4">
                        {{ str_replace('_', ' ', Auth::user()->role) }}
                    </span>

                    <form action="{{ route('profile.update') }}" method="POST" class="text-start mt-2">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-slate-dark">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required style="border-radius: 0.5rem; background-color: #f8fafc;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-slate-dark">Email Perusahaan</label>
                            <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" required style="border-radius: 0.5rem; background-color: #f8fafc;">
                        </div>

                        <hr class="my-3 border-light">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-slate-dark">Kata Sandi Baru (Opsional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tak diubah" style="border-radius: 0.5rem; background-color: #f8fafc;">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-slate-dark">Konfirmasi Kata Sandi</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi sandi baru" style="border-radius: 0.5rem; background-color: #f8fafc;">
                        </div>

                        <button type="submit" class="btn btn-emerald-custom w-100 fw-bold py-2 shadow-sm" style="border-radius: 0.5rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none;">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @if(session('profile_updated'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'success',
                title: 'Profil Diperbarui!',
                text: 'Perubahan akun Anda berhasil disimpan.',
                confirmButtonColor: '#10b981'
            });
        });
    </script>
    @endif

    @if($errors->has('name') || $errors->has('email') || $errors->has('password'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var myModal = new bootstrap.Modal(document.getElementById('profileModal'), {
                keyboard: false
            });
            myModal.show();
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menyimpan',
                text: 'Periksa kembali formulir profil Anda.',
                confirmButtonColor: '#ef4444'
            });
        });
    </script>
    @endif

    {{-- BOTTOM NAVIGATION FOR MOBILE (ROLE-BASED & PERMISSION-BASED) --}}
    @auth
    <div class="bottom-nav d-lg-none shadow-lg">
        @php
            $roleLower = strtolower(Auth::user()->role);
            $dashUrl = url('/dashboard');
            if (in_array($roleLower, ['admin gudang', 'staf gudang'])) {
                $dashUrl = route('warehouse.dashboard');
            } elseif (in_array($roleLower, ['admin keuangan', 'staf keuangan', 'akunting'])) {
                $dashUrl = route('keuangan.dashboard');
            } elseif (in_array($roleLower, ['sales'])) {
                $dashUrl = route('sales.dashboard');
            }

            // Ambil hak akses (array)
            $hak = is_string(Auth::user()->hak_akses) ? json_decode(Auth::user()->hak_akses, true) ?? [] : (Auth::user()->hak_akses ?? []);
            // Helper untuk mengecek apakah user diizinkan mengakses menu (Direktur otomatis bisa semua)
            $canAccess = function($key) use ($hak, $roleLower) {
                if (in_array($roleLower, ['direktur', 'superadmin', 'admin'])) return true;
                return in_array($key, $hak);
            };
        @endphp

        {{-- 1. TOMBOL HOME (Semua Role & Selalu Tampil) --}}
        <a href="{{ $dashUrl }}" class="bottom-nav-item {{ request()->is('dashboard') || request()->is('sales/dashboard') || request()->is('keuangan/dashboard') || request()->is('warehouse/dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>

        {{-- 2. SALES --}}
        @if(in_array($roleLower, ['sales']))
            <a href="{{ url('/penjualan/buat') }}" class="bottom-nav-item {{ request()->is('penjualan/buat') ? 'active' : '' }}">
                <i class="fas fa-plus-circle"></i>
                <span>Buat Order</span>
            </a>
            <a href="{{ url('/penjualan') }}" class="bottom-nav-item {{ request()->is('penjualan') && !request()->is('penjualan/buat') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>Riwayat</span>
            </a>
        @endif

        {{-- 3. GUDANG --}}
        @if(in_array($roleLower, ['admin gudang', 'staf gudang', 'warehouse']))
            @if($canAccess('backorder'))
            <a href="{{ url('/backorder') }}" class="bottom-nav-item {{ request()->is('backorder*') ? 'active' : '' }}">
                <i class="fas fa-hourglass-half"></i>
                <span>Backorder</span>
            </a>
            @endif
            @if($canAccess('pembelian_stok'))
            <a href="{{ url('/pembelian') }}" class="bottom-nav-item {{ request()->is('pembelian*') ? 'active' : '' }}">
                <i class="fas fa-shopping-bag"></i>
                <span>PO</span>
            </a>
            @endif
        @endif

        {{-- 4. KEUANGAN --}}
        @if(in_array($roleLower, ['admin keuangan', 'staf keuangan', 'akunting', 'keuangan']))
            @if($canAccess('akses_keuangan'))
            <a href="{{ url('/keuangan/piutang') }}" class="bottom-nav-item {{ request()->is('keuangan/piutang*') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-usd"></i>
                <span>Piutang</span>
            </a>
            <a href="{{ url('/keuangan/utang') }}" class="bottom-nav-item {{ request()->is('keuangan/utang*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Utang</span>
            </a>
            @endif
        @endif

        {{-- 5. MANAGER --}}
        @if(in_array($roleLower, ['manager']))
            @if($canAccess('approval_so'))
            <a href="{{ url('/penjualan/approval') }}" class="bottom-nav-item {{ request()->is('penjualan/approval*') ? 'active' : '' }}">
                <i class="fas fa-check-circle"></i>
                <span>Approval</span>
            </a>
            @endif
            @if($canAccess('unduh_laporan'))
            <a href="#" class="bottom-nav-item" onclick="openUnduhLaporanModal(event)">
                <i class="fas fa-file-alt"></i>
                <span>Laporan</span>
            </a>
            @endif
        @endif

        {{-- 6. DIREKTUR --}}
        @if(in_array($roleLower, ['direktur', 'super admin', 'admin']))
            @if($canAccess('approval_so'))
            <a href="{{ url('/penjualan/approval') }}" class="bottom-nav-item {{ request()->is('penjualan/approval*') ? 'active' : '' }}">
                <i class="fas fa-check-circle"></i>
                <span>Approval</span>
            </a>
            @endif
            @if($canAccess('profit_laba'))
            <a href="{{ url('/laporan/laba') }}" class="bottom-nav-item {{ request()->is('laporan/laba*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Profit/Laba</span>
            </a>
            @endif
        @endif

        {{-- 7. TOMBOL PROFIL (Semua Role & Selalu Tampil) --}}
        <a href="#" class="bottom-nav-item" data-bs-toggle="modal" data-bs-target="#profileModal">
            <i class="fas fa-user-circle"></i>
            <span>Profil</span>
        </a>
    </div>
    @endauth

    @stack('scripts')

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
</body>

</html>
