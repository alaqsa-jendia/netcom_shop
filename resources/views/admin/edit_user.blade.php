@extends('layouts.admin')

@section('title', 'تعديل المستخدم')

@section('admin_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold">
                <i class="fas fa-user-edit me-2 text-primary"></i>
                تعديل المستخدم
            </h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.edit_user', $user->id) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">الاسم</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">رقم الجوال</label>
                    <input type="text" class="form-control" value="{{ $user->phone }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">الرصيد</label>
                    <input type="number" name="balance" class="form-control" value="{{ old('balance', $user->balance) }}" step="0.01" min="0">
                </div>

                <div class="mb-3">
                    <label class="form-label">كلمة المرور (اتركها فارغة إذا لا تريد تغييرها)</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control" id="passwordInput" minlength="8">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select" required>
                        <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="suspended" {{ $user->status == 'suspended' ? 'selected' : '' }}>معلق</option>
                    </select>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-save me-2"></i>
                        تحديث
                    </button>
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary flex-grow-1">إلغاء</a>
                </div>
            </form>
        </div>
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