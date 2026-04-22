@extends('layouts.admin')

@section('title', 'إضافة باقة')

@section('admin_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold">
                <i class="fas fa-plus me-2 text-primary"></i>
                إضافة باقة
            </h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.create_package') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">اسم الباقة</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">السعر (شيكل)</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price') }}" step="0.01" min="0" required>
                    @error('price')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">عدد البطاقات</label>
                    <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" min="1" required>
                    @error('quantity')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">الأيقونة</label>
                    <select name="icon" class="form-select" id="iconSelect">
                        <option value="bolt">⚡ bolt</option>
                        <option value="boxes">📦 boxes</option>
                        <option value="gem">💎 gem</option>
                        <option value="wifi">📶 wifi</option>
                        <option value="satellite">🛰️ satellite</option>
                    </select>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-save me-2"></i>
                        حفظ
                    </button>
                    <a href="{{ route('admin.packages') }}" class="btn btn-secondary flex-grow-1">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection