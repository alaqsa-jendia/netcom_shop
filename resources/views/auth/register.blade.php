@extends('layouts.app')

@section('title', 'إنشاء حساب')

@section('content')
<div class="auth-container">
    <div class="auth-card fade-in">
        <div class="text-center mb-4">
            @php
                $settings = \App\Models\TelegramSettings::first();
                $systemName = $settings && $settings->system_name ? $settings->system_name : 'NetCom';
                $logo = $settings && $settings->logo ? asset('storage/' . $settings->logo) : null;
            @endphp
            <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle p-4 mb-3">
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ $systemName }}" style="width: 80px; height: 80px; object-fit: contain;">
                @else
                    <i class="fas fa-user-plus fa-3x text-primary"></i>
                @endif
            </div>
            <h3 class="fw-bold">إنشاء حساب جديد</h3>
            <p class="text-muted">انضم إلى {{ $systemName }}</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('auth.register') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">الاسم الرباعي الكامل</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-user text-muted"></i>
                    </span>
                    <input type="text" name="name" class="form-control border-start-0" 
                           placeholder="أحمد محمد علي محمد" value="{{ old('name') }}" required>
                </div>
                <small class="text-muted">مثال: أحمد محمد علي محمد</small>
            </div>

            <div class="mb-3">
                <label class="form-label">رقم الجوال</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-phone text-muted"></i>
                    </span>
                    <input type="tel" name="phone" class="form-control border-start-0" 
                           placeholder="0591234567" value="{{ old('phone') }}" required>
                </div>
                <small class="text-muted">يجب أن يبدأ بـ 059 أو 056</small>
            </div>

            <div class="mb-3">
                <label class="form-label">كلمة المرور</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-lock text-muted"></i>
                    </span>
                    <input type="password" name="password" class="form-control border-start-0" 
                           placeholder="********" required id="password">
                    <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword(this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small class="text-muted">8 خانات على الأقل، حرف كبير + أرقام</small>
            </div>

            <div class="mb-3">
                <label class="form-label">تأكيد كلمة المرور</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-lock text-muted"></i>
                    </span>
                    <input type="password" name="password_confirmation" class="form-control border-start-0" 
                           placeholder="********" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3">
                <i class="fas fa-user-plus me-2"></i>
                إنشاء حساب
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="text-muted mb-0">لديك حساب بالفعل؟ 
                <a href="{{ route('auth.login') }}" class="text-primary fw-bold">تسجيل الدخول</a>
            </p>
        </div>
    </div>
</div>

<script>
function togglePassword(btn) {
    const input = btn.previousElementSibling;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endsection
