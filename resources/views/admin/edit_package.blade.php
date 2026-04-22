@extends('layouts.admin')

@section('title', 'تعديل باقة')

@section('admin_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold">
                <i class="fas fa-edit me-2 text-primary"></i>
                تعديل باقة
            </h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.edit_package', $package->id) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">اسم الباقة</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $package->name) }}" required>
                    @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">السعر (شيكل)</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', $package->price) }}" step="0.01" min="0" required>
                    @error('price')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">عدد البطاقات</label>
                    <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $package->quantity) }}" min="1" required>
                    @error('quantity')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">الأيقونة</label>
                    <select name="icon" class="form-select" id="iconSelect">
                        <option value="bolt" {{ $package->icon == 'bolt' ? 'selected' : '' }}>⚡ bolt</option>
                        <option value="boxes" {{ $package->icon == 'boxes' ? 'selected' : '' }}>📦 boxes</option>
                        <option value="gem" {{ $package->icon == 'gem' ? 'selected' : '' }}>💎 gem</option>
                        <option value="wifi" {{ $package->icon == 'wifi' ? 'selected' : '' }}>📶 wifi</option>
                        <option value="satellite" {{ $package->icon == 'satellite' ? 'selected' : '' }}>🛰️ satellite</option>
                    </select>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ $package->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-save me-2"></i>
                        تحديث
                    </button>
                    <a href="{{ route('admin.packages') }}" class="btn btn-secondary flex-grow-1">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection