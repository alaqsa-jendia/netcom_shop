@extends('layouts.user')

@section('title', 'لوحة التحكم')

@section('user_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12">
            <div class="balance-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="opacity-75">مرحباً بك</h5>
                        <h3 class="fw-bold mb-0">{{ $user->name }}</h3>
                    </div>
                    <div class="wallet-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <hr class="opacity-25">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="opacity-75">رصيد المحفظة</small>
                        <h2 class="fw-bold mb-0">{{ number_format($user->balance, 2) }} <small>شيكل</small></h2>
                    </div>
                    <a href="{{ route('recharge') }}" class="btn btn-light">
                        <i class="fas fa-plus me-2"></i>
                        شحن رصيد
                    </a>
                </div>
            </div>
        </div>
    </div>

     <div class="row g-4">
         @foreach($packages as $package)
             @php
                 // Icon to gradient mapping
                 $iconGradients = [
                     'bolt' => 'linear-gradient(135deg, #22c55e, #16a34a)',
                     'boxes' => 'linear-gradient(135deg, #f59e0b, #d97706)',
                     'gem' => 'linear-gradient(135deg, #4f46e5, #6366f1)',
                     'wifi' => 'linear-gradient(135deg, #8b5cf6, #7c3aed)',
                     'satellite' => 'linear-gradient(135deg, #06b6d4, #0891b2)',
                 ];
                 $icon = $package->icon ?? 'bolt';
                 $gradient = $iconGradients[$icon] ?? 'linear-gradient(135deg, #6b7280, #9ca3af)';
             @endphp
              <div class="col-md-4 col-sm-6">
                  <div class="package-card">
                      <div class="package-icon" style="background: {{ $gradient }}">
                          <i class="fas fa-{{ $icon }}"></i>
                      </div>
                    <h5 class="fw-bold mb-2">{{ $package->name }}</h5>
                    <p class="text-muted mb-3">{{ $package->quantity }} بطاقات</p>
                    <div class="package-price mb-3">
                        {{ number_format($package->price, 2) }} <small>شيكل</small>
                    </div>
                    <button class="btn btn-primary w-100" onclick="openBuyModal({{ $package->id }}, '{{ $package->name }}', {{ $package->price }}, {{ $package->quantity }})">
                        <i class="fas fa-shopping-cart me-2"></i>
                        شراء الآن
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="modal fade" id="buyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-shopping-cart me-2 text-primary"></i>
                    شراء باقة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <h5 id="packageName" class="fw-bold"></h5>
                    <p class="text-muted mb-0">السعر: <span id="packagePrice" class="fw-bold text-primary"></span> شيكل</p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">عدد الباقات</label>
                    <input type="number" id="quantity" class="form-control" value="1" min="1" onchange="updateTotal()">
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    الإجمالي: <span id="totalPrice" class="fw-bold"></span> شيكل
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-wallet me-2"></i>
                    رصيدك الحالي: <span class="fw-bold">{{ number_format($user->balance, 2) }}</span> شيكل
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="confirmPurchase()">
                    <i class="fas fa-check me-2"></i>
                    تأكيد الشراء
                </button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="selectedPackageId" value="">
 @endsection

@section('scripts')
@parent
<script>
let currentPrice = 0;

function openBuyModal(id, name, price, maxQty) {
    document.getElementById('selectedPackageId').value = id;
    document.getElementById('packageName').textContent = name;
    document.getElementById('packagePrice').textContent = price;
    currentPrice = price;
    document.getElementById('quantity').value = 1;
    document.getElementById('quantity').max = maxQty;
    updateTotal();
    new bootstrap.Modal(document.getElementById('buyModal')).show();
}

function updateTotal() {
    const qty = document.getElementById('quantity').value;
    const total = qty * currentPrice;
    document.getElementById('totalPrice').textContent = total.toFixed(2);
}

function confirmPurchase() {
    const packageId = document.getElementById('selectedPackageId').value;
    const quantity = document.getElementById('quantity').value;
    
    fetch('{{ route('buy_package') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            package_id: packageId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('buyModal')).hide();
            Swal.fire({
                icon: 'success',
                title: 'تم الشراء بنجاح',
                text: data.message,
                confirmButtonText: 'حسناً'
            }).then(() => window.location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: data.error
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'حدث خطأ أثناء العملية'
        });
    });
}
</script>
@endsection
