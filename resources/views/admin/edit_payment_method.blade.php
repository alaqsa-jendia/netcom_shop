@extends('layouts.admin')

@section('title', 'تعديل طريقة دفع')

@section('admin_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold">
                <i class="fas fa-edit me-2 text-primary"></i>
                تعديل طريقة دفع
            </h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.edit_payment_method', $method->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">اسم الطريقة</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $method->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">اسم صاحب الحساب</label>
                    <input type="text" name="account_name" class="form-control" value="{{ old('account_name', $method->account_name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">رقم الحساب</label>
                    <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $method->account_number) }}" required>
                </div>

                @if($method->logo)
                <div class="mb-3">
                    <label class="form-label">الشعار الحالي</label>
                    <div>
                        <img src="{{ asset('storage/' . $method->logo) }}" alt="Logo" style="max-width: 100px; max-height: 100px;">
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">تغيير الشعار (اختياري)</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    <small class="text-muted">الحد الأقصى 1 ميجابايت</small>
                </div>

                @if($method->qr_code)
                <div class="mb-3">
                    <label class="form-label">QR Code الحالي</label>
                    <div>
                        <img src="{{ asset('storage/' . $method->qr_code) }}" alt="QR Code" style="max-width: 100px; max-height: 100px;">
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">تغيير QR Code (اختياري)</label>
                    <input type="file" name="qr_code" class="form-control" accept="image/*">
                    <small class="text-muted">الحد الأقصى 1 ميجابايت</small>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ $method->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">نشط</label>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-save me-2"></i>
                        تحديث
                    </button>
                    <a href="{{ route('admin.payment_methods') }}" class="btn btn-secondary flex-grow-1">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection