@extends('layouts.admin')

@section('title', 'الباقات')

@section('admin_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12 col-md-6">
            <h4 class="fw-bold">
                <i class="fas fa-box me-2 text-primary"></i>
                الباقات
            </h4>
        </div>
        <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.create_package') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                إضافة باقة
            </a>
        </div>
    </div>

     <div class="row g-4">
         @foreach($packages as $package)
          <div class="col-md-4">
              <div class="card h-100">
                  <div class="card-body text-center">
                      <div class="package-icon mx-auto mb-3" style="background: linear-gradient(135deg, #6b7280, #9ca3af)">
                          <i class="fas fa-box"></i>
                      </div>
                    <h5 class="fw-bold">{{ $package->name }}</h5>
                    <p class="text-muted">{{ $package->quantity }} بطاقات</p>
                    <h3 class="text-primary fw-bold">{{ number_format($package->price, 2) }} شيكل</h3>
                    <span class="badge badge-status {{ $package->is_active ? 'badge-approved' : 'badge-rejected' }}">
                        {{ $package->is_active ? 'نشط' : 'موقوف' }}
                    </span>
                </div>
                <div class="card-footer bg-white text-center">
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="{{ route('admin.edit_package', $package->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteConfirm('{{ route('admin.delete_package', $package->id) }}', 'هل أنت متأكد من حذف هذه الباقة؟')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
