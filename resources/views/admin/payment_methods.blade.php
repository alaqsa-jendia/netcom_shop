@extends('layouts.admin')

@section('title', 'طرق الدفع')

@section('admin_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold">
                <i class="fas fa-university me-2 text-primary"></i>
                طرق الدفع
            </h4>
            <a href="{{ route('admin.create_payment_method') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                إضافة طريقة
            </a>
        </div>
    </div>

    <div class="row g-3">
@foreach($methods as $method)
        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="method-icon me-3">
                            @if($method->logo)
                                <img src="{{ asset('storage/' . $method->logo) }}" alt="{{ $method->name }}" style="width: 50px; height: 50px; object-fit: contain;">
                            @else
                                <i class="fas fa-university fa-2x text-primary"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold">{{ $method->name }}</h5>
                            <p class="text-muted mb-1">{{ $method->account_name }}</p>
                            <h6 class="text-primary mb-0">{{ $method->account_number }}</h6>
                            @if($method->qr_code)
                                <img src="{{ asset('storage/' . $method->qr_code) }}" alt="QR" class="mt-2" style="max-width: 80px;">
                            @endif
                        </div>
                        <div>
                            <span class="badge badge-status {{ $method->is_active ? 'badge-approved' : 'badge-rejected' }}">
                                {{ $method->is_active ? 'نشط' : 'موقوف' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-sm {{ $method->is_active ? 'btn-warning' : 'btn-success' }}" onclick="toggleMethodStatus('{{ route('admin.toggle_payment_method', $method->id) }}', '{{ $method->is_active ? 'هل أنت متأكد من تعطيل طريقة الدفع هذه؟' : 'هل أنت متأكد من تفعيل طريقة الدفع هذه؟' }}')">
                        <i class="fas fa-{{ $method->is_active ? 'pause' : 'play' }}"></i>
                    </button>
                    <a href="{{ route('admin.edit_payment_method', $method->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteConfirm('{{ route('admin.delete_payment_method', $method->id) }}', 'هل أنت متأكد من حذف طريقة الدفع هذه؟')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
