@extends('layouts.app')

@section('title', 'تسجيل دخول الأدمن')

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
                    <i class="fas fa-store fa-3x text-primary"></i>
                @endif
            </div>
            <h3 class="fw-bold">{{ $systemName }} - لوحة التحكم</h3>
            <p class="text-muted">تسجيل دخول الأدمن</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">اسم المستخدم</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-user text-muted"></i>
                    </span>
                    <input type="text" name="username" class="form-control border-start-0" placeholder="ajendia" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">كلمة المرور</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-lock text-muted"></i>
                    </span>
                    <input type="password" name="password" class="form-control border-start-0" id="passwordInput" placeholder="********" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3">
                <i class="fas fa-sign-in-alt me-2"></i>
                تسجيل الدخول
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection
