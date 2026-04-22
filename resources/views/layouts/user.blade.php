@extends('layouts.app')

@php
$settings = \App\Models\TelegramSettings::first();
$systemName = $settings && $settings->system_name ? $settings->system_name : 'كروت';
$logo = $settings && $settings->logo ? asset('storage/' . $settings->logo) : null;
@endphp

@section('title', $systemName . ' - ' . ($title ?? 'لوحة التحكم'))

@section('content')
<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<nav class="sidebar">
    <div class="sidebar-brand">
        @if($logo)
            <img src="{{ $logo }}" alt="{{ $systemName }}" style="height: 40px; width: auto; margin-bottom: 8px;">
        @else
            <div class="brand-icon">
                <i class="fas fa-cards"></i>
            </div>
        @endif
        <h5>{{ $systemName }}</h5>
    </div>
    
    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-home"></i>
                    <span>الرئيسية</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('my_cards') ? 'active' : '' }}" href="{{ route('my_cards') }}">
                    <i class="fas fa-credit-card"></i>
                    <span>بطاقاتي</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('recharge') ? 'active' : '' }}" href="{{ route('recharge') }}">
                    <i class="fas fa-wallet"></i>
                    <span>شحن الرصيد</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('support') ? 'active' : '' }}" href="{{ route('support') }}">
                    <i class="fas fa-headset"></i>
                    <span>الدعم الفني</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <a class="nav-link" href="{{ route('auth.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>تسجيل الخروج</span>
        </a>
        <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</nav>

<div class="main-wrapper">
    <div class="top-bar">
        <button class="menu-toggle d-none d-md-flex" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="d-none d-md-block"></div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn position-relative p-2" data-bs-toggle="dropdown" data-bs-auto-close="outside" style="background: var(--gray-100); border-radius: 12px;">
                    <i class="fas fa-bell" style="font-size: 18px; color: var(--dark-color);"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge" style="display: none; font-size: 10px;">
                        <span class="notification-count">0</span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end p-0" style="width: 320px; max-height: 400px; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                        <h6 class="mb-0 fw-bold">الإشعارات</h6>
                        <button class="btn btn-sm btn-link text-primary p-0" onclick="markAllRead()">تعليم الكل كمقروء</button>
                    </div>
                    <div class="notifications-list" style="max-height: 300px; overflow-y: auto;">
                        <div class="text-center text-muted p-4">
                            <i class="fas fa-bell-slash fa-2x mb-2 d-block"></i>
                            <p class="mb-0 small">لا توجد إشعارات</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="dropdown">
                <button class="d-flex align-items-center gap-2" data-bs-toggle="dropdown" style="background: transparent; border: none; cursor: pointer;">
                     <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                         <i class="fas fa-user"></i>
                     </div>
                    <span class="d-none d-md-inline fw-bold">{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-down small text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <li><a class="dropdown-item py-2" href="{{ route('dashboard') }}"><i class="fas fa-home me-2"></i>الرئيسية</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2 text-danger" href="{{ route('auth.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('user_content')
    </div>
</div>

<nav class="mobile-bottom-nav d-md-none">
    <div class="container-fluid">
        <div class="d-flex justify-content-around">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="fas fa-home"></i>
                <small>الرئيسية</small>
            </a>
            <a class="nav-link {{ request()->routeIs('my_cards') ? 'active' : '' }}" href="{{ route('my_cards') }}">
                <i class="fas fa-credit-card"></i>
                <small>البطاقات</small>
            </a>
            <a class="nav-link {{ request()->routeIs('recharge') ? 'active' : '' }}" href="{{ route('recharge') }}">
                <i class="fas fa-wallet"></i>
                <small>شحن</small>
            </a>
            <a class="nav-link {{ request()->routeIs('support') ? 'active' : '' }}" href="{{ route('support') }}">
                <i class="fas fa-headset"></i>
                <small>دعم</small>
            </a>
        </div>
    </div>
</nav>
@endsection

@section('scripts')
<script>
let notificationsInterval;

function loadNotifications() {
    console.log('Loading notifications...');
    fetch('{{ route('notifications') }}')
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                console.error('Notifications response not ok:', response.status, response.statusText);
                return [];
            }
            return response.json();
        })
        .then(data => {
            console.log('Notifications data:', data);
            console.log('Notifications count:', data.length);
            
            if (!Array.isArray(data)) {
                console.error('Notifications data is not an array:', data);
                return [];
            }
            
            const list = document.querySelector('.notifications-list');
            const badge = document.querySelector('.notification-badge');
            const count = document.querySelector('.notification-count');
            
            const unreadCount = data.filter(n => !n.is_read).length;
            
            if (unreadCount > 0) {
                badge.style.display = 'inline-flex';
                count.textContent = unreadCount > 9 ? '9+' : unreadCount;
            } else {
                badge.style.display = 'none';
            }
            
            if (data.length > 0) {
                list.innerHTML = data.map(n => `
                    <div class="notification-item p-3 border-bottom notification-item" style="cursor: pointer; background: ${n.is_read ? '#fff' : '#f8f9fa'}" onclick="markAsRead(${n.id})">
                        <div class="d-flex align-items-start">
                            <div class="me-2">
                                <i class="fas ${n.type === 'recharge_approved' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-1 fw-bold small">${n.title}</p>
                                <p class="mb-0 small text-muted">${n.message}</p>
                                <small class="text-muted">${formatDate(n.created_at)}</small>
                            </div>
                            ${!n.is_read ? '<div class="badge bg-primary rounded-circle" style="width: 8px; height: 8px; padding: 0;"></div>' : ''}
                        </div>
                    </div>
                `).join('');
            } else {
                list.innerHTML = '<div class="text-center text-muted p-4"><i class="fas fa-bell-slash fa-2x mb-2 d-block"></i><p class="mb-0 small">لا توجد إشعارات</p></div>';
            }
        });
}

 function formatDate(dateString) {
     const date = new Date(dateString);
     if (isNaN(date.getTime())) {
         return dateString; // fallback to raw string
     }
     const now = new Date();
     const diff = now - date;
     const minutes = Math.floor(diff / 60000);
     const hours = Math.floor(diff / 3600000);
     const days = Math.floor(diff / 86400000);
     
     if (minutes < 1) return 'الآن';
     if (minutes < 60) return 'منذ ' + minutes + ' دقيقة';
     if (hours < 24) return 'منذ ' + hours + ' ساعة';
     if (days < 7) return 'منذ ' + days + ' يوم';
     return date.toLocaleDateString('ar');
 }

function markAsRead(id) {
    fetch('/notifications/' + id + '/read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(() => loadNotifications());
}

function markAllRead() {
    fetch('{{ route('notifications.read_all') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(() => loadNotifications());
}

 document.addEventListener('DOMContentLoaded', function() {
     loadNotifications();
     notificationsInterval = setInterval(loadNotifications, 5000);
 });
</script>
@endsection

<style>
.notifications-dropdown .notification-item:hover {
    background: #f1f1f1 !important;
}

.mobile-bottom-nav .nav-link.active {
    color: var(--primary-color) !important;
}

.mobile-bottom-nav .nav-link i {
    font-size: 20px;
}
</style>