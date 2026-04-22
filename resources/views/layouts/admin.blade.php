@extends('layouts.app')

@php
$settings = \App\Models\TelegramSettings::first();
$systemName = $settings && $settings->system_name ? $settings->system_name : 'كروت';
$logo = $settings && $settings->logo ? asset('storage/' . $settings->logo) : null;
@endphp

@section('title', $systemName . ' - لوحة التحكم')

@php
$currentRoute = Route::currentRouteName();
$vapidPublicKey = config('webpush.vapid.public_key');
@endphp

@section('styles')
@if($vapidPublicKey)
<script>
    window.VAPID_PUBLIC_KEY = '{{ $vapidPublicKey }}';
    window.Laravel = {
        csrfToken: '{{ csrf_token() }}'
    };
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        async function initPushNotifications() {
            try {
                const registration = await navigator.serviceWorker.register('/service-worker.js');
                console.log('Service Worker registered:', registration);
                
                const permission = await Notification.permission;
                updateNotificationButton(permission);
                
                const existingSubscription = await registration.pushManager.getSubscription();
                if (existingSubscription) {
                    await saveSubscription(existingSubscription);
                }
            } catch (error) {
                console.error('Service Worker registration failed:', error);
            }
        }
        
        function updateNotificationButton(permission) {
            const btn = document.getElementById('push-notification-btn');
            if (!btn) return;
            
            if (permission === 'granted') {
                btn.innerHTML = '<i class="fas fa-bell"></i><span>الإشعارات مفعلة</span>';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
            } else if (permission === 'denied') {
                btn.innerHTML = '<i class="fas fa-bell-slash"></i><span>الإشعارات محظورة</span>';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-danger');
            } else {
                btn.innerHTML = '<i class="fas fa-bell"></i><span>تفعيل الإشعارات</span>';
                btn.classList.add('btn-primary');
                btn.classList.remove('btn-success', 'btn-danger');
            }
        }
        
        async function subscribeToPush() {
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                alert('يرجى السماح بالإشعارات لتلقي تنبيهات طلبات الشحن');
                return;
            }
            
            const registration = await navigator.serviceWorker.ready;
            
            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(window.VAPID_PUBLIC_KEY)
            });
            
            await saveSubscription(subscription);
            updateNotificationButton('granted');
        }
        
        async function saveSubscription(subscription) {
            const response = await fetch('{{ route("admin.push.subscribe") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint,
                    public_key: subscription.toJSON().keys?.p256dh,
                    auth_token: subscription.toJSON().keys?.auth,
                    content_encoding: 'aes128gcm'
                })
            });
            
            const data = await response.json();
            console.log('Subscription saved:', data);
        }
        
        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
        
        window.subscribeToPush = subscribeToPush;
        
        initPushNotifications();
    }
});
</script>
@endif
@endsection

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
                <a class="nav-link {{ strpos($currentRoute, 'admin.dashboard') !== false ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-home"></i>
                    <span>الرئيسية</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ strpos($currentRoute, 'admin.users') !== false ? 'active' : '' }}" href="{{ route('admin.users') }}">
                    <i class="fas fa-users"></i>
                    <span>المستخدمين</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ strpos($currentRoute, 'admin.admins') !== false ? 'active' : '' }}" href="{{ route('admin.admins') }}">
                    <i class="fas fa-user-shield"></i>
                    <span>المدراء</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ strpos($currentRoute, 'admin.packages') !== false ? 'active' : '' }}" href="{{ route('admin.packages') }}">
                    <i class="fas fa-box"></i>
                    <span>الباقات</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ strpos($currentRoute, 'admin.cards') !== false ? 'active' : '' }}" href="{{ route('admin.cards') }}">
                    <i class="fas fa-credit-card"></i>
                    <span>البطاقات</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ strpos($currentRoute, 'admin.payment_methods') !== false ? 'active' : '' }}" href="{{ route('admin.payment_methods') }}">
                    <i class="fas fa-university"></i>
                    <span>طرق الدفع</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ strpos($currentRoute, 'admin.recharge') !== false ? 'active' : '' }}" href="{{ route('admin.recharge_requests') }}">
                    <i class="fas fa-wallet"></i>
                    <span>طلبات الشحن</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ strpos($currentRoute, 'admin.chat') !== false ? 'active' : '' }}" href="{{ route('admin.chat') }}">
                    <i class="fas fa-comments"></i>
                    <span>المحادثات</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ strpos($currentRoute, 'admin.settings') !== false ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                    <i class="fas fa-cog"></i>
                    <span>الإعدادات</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <a class="nav-link" href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>تسجيل الخروج</span>
        </a>
        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</nav>

<div class="main-wrapper">
        <div class="top-bar">
            <div class="d-flex align-items-center gap-3 w-100">
                <button class="menu-toggle d-flex d-lg-none" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0 fw-bold d-none d-lg-block">{{ $systemName }}</h5>
                @if(config('webpush.vapid.public_key'))
                <button id="push-notification-btn" class="btn btn-primary btn-sm me-auto">
                    <i class="fas fa-bell"></i>
                    <span class="d-none d-sm-inline">تفعيل الإشعارات</span>
                </button>
                @endif
            </div>
        </div>

        <div class="main-content">
            <div class="container-fluid px-0">
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

        @yield('admin_content')
    </div>
</div>
@endsection