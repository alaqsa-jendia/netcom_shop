<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NetCom Shop')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-light: #818cf8;
            --secondary-color: #6366f1;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #0ea5e9;
            --dark-color: #1e293b;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --sidebar-width: 260px;
            --topbar-height: 60px;
            --transition-speed: 0.3s;
        }
        
        *, *::before, *::after {
            box-sizing: border-box;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--gray-50);
            color: var(--dark-color);
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ===== SCROLLBAR STYLING ===== */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }

        /* ===== SIDEBAR STYLES ===== */
        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: transform var(--transition-speed) ease;
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        .sidebar-brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }
        
        .sidebar-brand .brand-icon {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 10px;
        }
        
        .sidebar-brand h5 {
            color: white;
            font-weight: 700;
            margin: 0;
            font-size: 18px;
        }
        
        .sidebar-menu {
            flex: 1;
            padding: 15px 10px;
            overflow-y: auto;
        }
        
        .sidebar-menu .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 12px 16px;
            border-radius: 10px;
            margin: 4px 0;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .sidebar-menu .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(-4px);
        }
        
        .sidebar-menu .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .sidebar-menu .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
        
        .sidebar-footer {
            padding: 15px;
            border-top: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }
        
        .sidebar-footer .nav-link {
            color: rgba(255,255,255,0.6);
            padding: 12px 16px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }
        
        .sidebar-footer .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        /* ===== MAIN CONTENT ===== */
        .main-wrapper {
            margin-right: var(--sidebar-width);
            min-height: 100vh;
            transition: margin var(--transition-speed) ease;
        }
        
        .main-content {
            padding: 24px;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* ===== TOP BAR ===== */
        .top-bar {
            background: white;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .top-bar .menu-toggle {
            display: none;
            width: 40px;
            height: 40px;
            border: none;
            background: var(--gray-100);
            border-radius: 10px;
            cursor: pointer;
            color: var(--dark-color);
            font-size: 18px;
            transition: all 0.2s;
        }
        
        .top-bar .menu-toggle:hover {
            background: var(--primary-color);
            color: white;
        }

        /* ===== CARDS ===== */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            background: white;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--gray-100);
            padding: 16px 20px;
            font-weight: 600;
        }
        
        .card-body {
            padding: 20px;
        }

        /* ===== STAT CARDS ===== */
        .stat-card {
            position: relative;
            padding: 20px;
            border-radius: 16px;
            overflow: hidden;
            background: white;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.1);
        }
        
        .stat-card .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 15px;
        }
        
        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 4px;
        }
        
        .stat-card .stat-label {
            font-size: 14px;
            color: var(--gray-500);
            font-weight: 500;
        }
        
        .stat-card.primary .stat-icon { background: #e0e7ff; color: var(--primary-color); }
        .stat-card.success .stat-icon { background: #d1fae5; color: var(--success-color); }
        .stat-card.warning .stat-icon { background: #fef3c7; color: var(--warning-color); }
        .stat-card.danger .stat-icon { background: #fee2e2; color: var(--danger-color); }
        .stat-card.info .stat-icon { background: #e0f2fe; color: var(--info-color); }

        /* ===== BUTTONS ===== */
        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-sm {
            padding: 6px 14px;
            font-size: 13px;
            border-radius: 8px;
        }
        
        .btn-lg {
            padding: 14px 28px;
            font-size: 16px;
            border-radius: 12px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }
        
        .btn-success {
            background: var(--success-color);
            border: none;
        }
        
        .btn-success:hover {
            background: #059669;
        }
        
        .btn-danger {
            background: var(--danger-color);
            border: none;
        }
        
        .btn-danger:hover {
            background: #dc2626;
        }
        
        .btn-warning {
            background: var(--warning-color);
            border: none;
            color: white;
        }
        
        .btn-warning:hover {
            background: #d97706;
        }
        
        .btn-secondary {
            background: var(--gray-200);
            border: none;
            color: var(--dark-color);
        }
        
        .btn-secondary:hover {
            background: var(--gray-300);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        /* ===== FORMS ===== */
        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            background: white;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            outline: none;
        }
        
        .form-control::placeholder {
            color: var(--gray-400);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-check-input {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 2px solid var(--gray-300);
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* ===== TABLES ===== */
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
            vertical-align: middle;
        }
        
        .table thead th {
            background: var(--gray-50);
            border: none;
            padding: 14px 16px;
            font-weight: 700;
            color: var(--dark-color);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }
        
        .table tbody tr {
            transition: all 0.2s;
            border-bottom: 1px solid var(--gray-100);
        }
        
        .table tbody tr:hover {
            background: var(--gray-50);
        }
        
        .table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            font-size: 14px;
        }

        /* ===== BADGES ===== */
        .badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
        }
        
        .badge-status {
            padding: 8px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }
        
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }

        /* ===== MODALS ===== */
        .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            border-bottom: 1px solid var(--gray-100);
            padding: 20px 24px;
        }
        
        .modal-body {
            padding: 24px;
        }
        
        .modal-footer {
            border-top: 1px solid var(--gray-100);
            padding: 16px 24px;
        }
        
        .btn-close {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--gray-100);
            opacity: 0.6;
        }
        
        .btn-close:hover {
            opacity: 1;
            background: var(--gray-200);
        }

        /* ===== ALERTS ===== */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 14px 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .alert-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .alert-info {
            background: #e0f2fe;
            color: #075985;
        }

        /* ===== PACKAGE CARDS ===== */
        .package-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            border: 2px solid var(--gray-100);
        }
        
        .package-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.15);
        }
        
        .package-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
            color: white;
        }
        
        .package-price {
            font-size: 32px;
            font-weight: 800;
            color: var(--primary-color);
        }
        
        .package-price span {
            font-size: 16px;
            font-weight: 500;
            color: var(--gray-500);
        }

        /* ===== BALANCE CARD ===== */
        .balance-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 20px;
            padding: 24px;
            color: white;
        }
        
        .balance-card .balance-label {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 8px;
        }
        
        .balance-card .balance-amount {
            font-size: 36px;
            font-weight: 800;
        }
        
        .wallet-icon {
            width: 56px;
            height: 56px;
            background: rgba(255,255,255,0.2);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        /* ===== PAGINATION ===== */
        .pagination {
            gap: 6px;
        }
        
        .page-link {
            border: none;
            border-radius: 8px;
            padding: 8px 14px;
            color: var(--dark-color);
            background: var(--gray-100);
            font-weight: 600;
            margin: 0 2px;
        }
        
        .page-link:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .page-item.active .page-link {
            background: var(--primary-color);
            color: white;
        }

        /* ===== ANIMATIONS ===== */
        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* ===== MOBILE NAV ===== */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
            z-index: 1030;
            padding: 8px 0;
            padding-bottom: calc(8px + env(safe-area-inset-bottom));
        }
        
        .mobile-bottom-nav .nav-item {
            flex: 1;
            text-align: center;
        }
        
        .mobile-bottom-nav .nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 8px;
            color: var(--gray-500);
            font-size: 12px;
            border-radius: 10px;
            transition: all 0.2s;
        }
        
        .mobile-bottom-nav .nav-link.active {
            color: var(--primary-color);
        }
        
        .mobile-bottom-nav .nav-link i {
            font-size: 20px;
            margin-bottom: 4px;
        }

        /* ===== RESPONSIVE STYLES ===== */
        @media (max-width: 1199px) {
            .main-content {
                padding: 20px;
            }
        }
        
        @media (max-width: 991px) {
            :root {
                --sidebar-width: 280px;
            }
            
            .sidebar {
                transform: translateX(100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-wrapper {
                margin-right: 0;
            }
            
            .top-bar .menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .mobile-bottom-nav {
                display: flex;
            }
            
            body {
                padding-bottom: 70px;
            }
        }
        
        @media (max-width: 767px) {
            .main-content {
                padding: 16px;
            }
            
            .stat-card {
                padding: 16px;
            }
            
            .stat-card .stat-value {
                font-size: 24px;
            }
            
            .stat-card .stat-icon {
                width: 44px;
                height: 44px;
                font-size: 18px;
            }
            
            .card-body {
                padding: 16px;
            }
            
            .btn {
                padding: 10px 16px;
                font-size: 13px;
            }
            
            .table-responsive {
                font-size: 13px;
            }
            
            .table thead th, 
            .table tbody td {
                padding: 10px 12px;
            }
            
            h1, h2, h3, h4, h5, h6 {
                font-size: 90%;
            }
            
            .balance-card .balance-amount {
                font-size: 28px;
            }
            
            .package-card {
                padding: 20px;
            }
            
            .package-price {
                font-size: 26px;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding: 12px;
            }
            
            .stat-card {
                padding: 14px;
            }
            
            .stat-card .stat-value {
                font-size: 22px;
            }
            
            .btn {
                padding: 8px 14px;
                font-size: 12px;
            }
            
            .form-control, .form-select {
                padding: 10px 14px;
                font-size: 14px;
            }
            
            .modal-body {
                padding: 16px;
            }
            
            .modal-header, .modal-footer {
                padding: 14px 16px;
            }
        }

        /* Extra utilities */
        .gap-2 { gap: 0.5rem !important; }
        .gap-3 { gap: 1rem !important; }
        
        .text-primary { color: var(--primary-color) !important; }
        .text-success { color: var(--success-color) !important; }
        .text-danger { color: var(--danger-color) !important; }
        .text-warning { color: var(--warning-color) !important; }
        .text-muted { color: var(--gray-500) !important; }
        
        .bg-primary { background-color: var(--primary-color) !important; }
        .bg-success { background-color: var(--success-color) !important; }
        .bg-danger { background-color: var(--danger-color) !important; }
        .bg-warning { background-color: var(--warning-color) !important; }
        .bg-light { background-color: var(--gray-50) !important; }
        .bg-white { background-color: white !important; }

        /* Image handling */
        img {
            max-width: 100%;
            height: auto;
        }
        
        .img-thumbnail {
            border-radius: 10px;
            border: 2px solid var(--gray-200);
            padding: 4px;
        }

        /* ===== RESPONSIVE TABLE STYLES ===== */
        @media (max-width: 767px) {
            .table {
                font-size: 12px;
            }
            
            .table thead th,
            .table tbody td {
                padding: 8px 10px;
            }
            
            .table-responsive-sm .table {
                font-size: 11px;
            }
            
            .table-responsive-sm .table thead th,
            .table-responsive-sm .table tbody td {
                padding: 6px 8px;
            }
        }

        /* ===== ACTION BUTTONS RESPONSIVE ===== */
        .table .btn {
            padding: 4px 8px;
            font-size: 12px;
        }
        
        .table .btn i {
            font-size: 12px;
        }
        
        @media (max-width: 576px) {
            .table .btn {
                padding: 4px 6px;
            }
            
            .table .btn-sm {
                padding: 3px 6px;
                font-size: 11px;
            }
        }

        /* Auth pages */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        
        .auth-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }
        
        @media (max-width: 480px) {
            .auth-card {
                padding: 24px;
                border-radius: 16px;
            }
        }

        /* ===== ADMIN PANEL RESPONSIVE IMPROVEMENTS ===== */
        
        /* Mobile-first container */
        .main-content .container-fluid {
            width: 100%;
        }

        /* Top bar responsive */
        .top-bar {
            padding: 12px 16px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .top-bar .menu-toggle {
            width: 44px;
            height: 44px;
            min-width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .top-bar h5 {
            font-size: 16px;
        }
        
        .top-bar .btn {
            min-height: 44px;
            padding: 8px 12px;
        }

        /* Main content padding */
        .main-content {
            padding: 16px;
        }

        @media (min-width: 992px) {
            .main-content {
                padding: 24px;
            }
        }

        @media (min-width: 1200px) {
            .main-content {
                padding: 28px;
            }
        }

        /* Sidebar menu items - truncate/wrap on mobile */
        .sidebar-menu .nav-link {
            padding: 14px 16px;
            min-height: 48px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        }
        
        .sidebar-menu .nav-link span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        @media (max-width: 991px) {
            .sidebar-menu .nav-link {
                padding: 12px 14px;
                font-size: 13px;
            }
            
            .sidebar-menu .nav-link span {
                max-width: 140px;
            }
        }

        /* Touch-friendly buttons */
        .btn {
            min-height: 44px;
            padding: 10px 20px;
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
        
        .btn-sm {
            min-height: 36px;
            padding: 8px 14px;
        }
        
        .btn-lg {
            min-height: 52px;
            padding: 14px 28px;
        }

        /* Mobile stacked buttons */
        @media (max-width: 768px) {
            .btn {
                width: 100%;
                margin-bottom: 8px;
            }
            
            .btn:last-child {
                margin-bottom: 0;
            }
            
            .d-flex.gap-2,
            .d-flex.gap-3 {
                flex-direction: column;
            }
            
            .d-flex.gap-2 > .btn,
            .d-flex.gap-3 > .btn {
                width: 100%;
                margin-bottom: 8px;
            }
            
            .d-flex.gap-2 > .btn:last-child,
            .d-flex.gap-3 > .btn:last-child {
                margin-bottom: 0;
            }
            
            /* Action buttons in tables - horizontal on mobile */
            td .btn {
                width: auto;
                margin-bottom: 0;
                display: inline-flex;
                margin: 2px;
            }
            
            td .d-flex {
                flex-direction: row !important;
                flex-wrap: wrap;
                gap: 4px !important;
            }
            
            td .d-flex .btn {
                margin: 2px;
            }
        }

        /* Cards responsive */
        .card {
            margin-bottom: 16px;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .card-body {
            padding: 16px;
        }
        
        @media (min-width: 768px) {
            .card {
                border-radius: 16px;
            }
            
            .card-body {
                padding: 20px;
            }
        }

        /* Table responsive wrapper - always wrap tables */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 12px;
            margin-bottom: 16px;
        }
        
        .table-responsive .table {
            margin-bottom: 0;
            min-width: 600px;
        }
        
        /* Ensure all tables have responsive wrapper */
        .card > .table-responsive:last-child,
        .card-body > .table-responsive:last-child {
            margin-bottom: 0;
        }

        /* Sidebar responsive behavior */
        @media (max-width: 991px) {
            .sidebar {
                width: 100%;
                max-width: 300px;
                transform: translateX(100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-wrapper {
                margin-right: 0;
            }
            
            .sidebar-overlay {
                display: block;
                visibility: hidden;
                opacity: 0;
                transition: opacity 0.3s, visibility 0.3s;
            }
            
            .sidebar-overlay.show {
                visibility: visible;
                opacity: 1;
            }
        }

        /* Stat cards responsive grid */
        .stat-card {
            padding: 16px;
        }
        
        @media (min-width: 576px) {
            .stat-card {
                padding: 20px;
            }
        }

        /* Form controls mobile */
        .form-control, .form-select {
            min-height: 44px;
            padding: 12px 16px;
            font-size: 16px;
        }
        
        @media (min-width: 768px) {
            .form-control, .form-select {
                min-height: 46px;
            }
        }

        /* Modal responsive */
        .modal-dialog {
            margin: 16px;
        }
        
        @media (min-width: 576px) {
            .modal-dialog {
                margin: 1.75rem auto;
            }
        }

        /* User avatar initials - circle clip */
        .user-avatar, .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .user-avatar img, .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Ensure heading responsive */
        h4.fw-bold {
            font-size: 18px;
            margin-bottom: 16px;
        }
        
        @media (min-width: 768px) {
            h4.fw-bold {
                font-size: 22px;
                margin-bottom: 20px;
            }
        }

        /* Page header responsive */
        .row.mb-4 {
            margin-bottom: 16px !important;
        }
        
        @media (min-width: 768px) {
            .row.mb-4 {
                margin-bottom: 24px !important;
            }
        }

        /* Alert responsive */
        .alert {
            padding: 12px 16px;
            font-size: 14px;
        }

        /* Badge responsive */
        .badge {
            font-size: 11px;
            padding: 5px 10px;
        }
        
        .badge-status {
            font-size: 11px;
            padding: 6px 12px;
        }
    </style>
    @yield('styles')
</head>
<body>
    @yield('content')
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Sidebar toggle for mobile
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
            document.querySelector('.sidebar-overlay').classList.toggle('show');
        }
        
        // Close sidebar when clicking overlay
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            const toggle = document.querySelector('.menu-toggle');
            
            if (window.innerWidth <= 991) {
                if (sidebar && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            }
        });

        // Delete confirmation
        function deleteConfirm(url, message = 'هل أنت متأكد من حذف هذا العنصر؟') {
            Swal.fire({
                title: 'تأكيد الحذف',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'حذف',
                cancelButtonText: 'إلغاء',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form dynamically and submit via POST
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    
                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
            return false;
        }

        // Toggle method status
        function toggleMethodStatus(url, message) {
            Swal.fire({
                title: 'تأكيد',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'تأكيد',
                cancelButtonText: 'إلغاء',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-success me-2',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form dynamically and submit via POST
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    
                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
            return false;
        }

        // Global fetch with CSRF token for non-GET requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            const originalFetch = window.fetch;
            window.fetch = function(url, options = {}) {
                const method = options.method?.toUpperCase() || 'GET';
                if (method !== 'GET' && method !== 'HEAD') {
                    options.headers = {
                        ...options.headers,
                        'X-CSRF-TOKEN': csrfToken
                    };
                }
                return originalFetch(url, options);
            };
        }

        // Auto refresh
        (function() {
            const AUTO_REFRESH_INTERVAL = 30000;
            
            function triggerRefresh() {
                location.reload();
            }
            
            setInterval(() => {
                fetch(window.location.href, { method: 'HEAD', cache: 'no-cache' })
                    .then(response => {
                        const etag = response.headers.get('ETag');
                        if (etag && window.lastEtag && etag !== window.lastEtag) {
                            triggerRefresh();
                        }
                        window.lastEtag = etag;
                    })
                    .catch(() => {});
            }, AUTO_REFRESH_INTERVAL);
            
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    fetch(window.location.href, { method: 'HEAD', cache: 'no-cache' })
                        .then(response => {
                            const etag = response.headers.get('ETag');
                            if (etag && window.lastEtag && etag !== window.lastEtag) {
                                triggerRefresh();
                            }
                            window.lastEtag = etag;
                        })
                        .catch(() => {});
                }
            });
        })();
    </script>
    <script src="{{ asset('js/ajax-admin.js') }}"></script>
    @yield('scripts')
</body>
</html>