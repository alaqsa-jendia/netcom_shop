@extends('layouts.user')

@section('title', 'شحن الرصيد')

@section('user_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold mb-3">
                <i class="fas fa-wallet me-2 text-primary"></i>
                شحن الرصيد
            </h4>
        </div>
    </div>

    @if($paymentMethods->isEmpty())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            لا توجد طرق دفع متاحة حالياً
        </div>
    @else
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="fw-bold mb-3">اختر طريقة الدفع</h5>
            </div>
            @foreach($paymentMethods as $method)
            <div class="col-sm-6 mb-3">
                <div class="card h-100 method-card {{ $loop->first ? 'border-primary' : '' }}" id="method-{{ $method->id }}" style="cursor: pointer" data-id="{{ $method->id }}" data-name="{{ $method->name }}" data-account-name="{{ $method->account_name }}" data-account-number="{{ $method->account_number }}" data-qr-code="{{ $method->qr_code ? asset('storage/' . $method->qr_code) : '' }}" data-has-qr-code="{{ $method->qr_code ? '1' : '0' }}" data-logo="{{ $method->logo ? asset('storage/' . $method->logo) : '' }}" data-has-logo="{{ $method->logo ? '1' : '0' }}" onclick="showMethodModal(this)">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="method-icon me-3">
                                @if($method->logo)
                                    <img src="{{ asset('storage/' . $method->logo) }}" alt="{{ $method->name }}" style="width: 40px; height: 40px; object-fit: contain;">
                                @else
                                    <i class="fas fa-university fa-2x text-primary"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1">{{ $method->name }}</h5>
                                <p class="text-muted mb-0">{{ $method->account_name }}</p>
                            </div>
                            <div class="selected-check">
                                <i class="fas fa-check-circle text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endforeach
        </div>

        <div class="card mt-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    تأكيد التحويل
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('recharge') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="payment_method_id" id="paymentMethodId" value="{{ $paymentMethods->first()->id }}" required>
                    
                    <div class="mb-3">
                        <label class="form-label">المبلغ المحول (شيكل)</label>
                        <input type="number" name="amount" class="form-control" min="10" step="0.01" required>
                        <small class="text-muted">أقل مبلغ 10 شيكل</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">اسم المحول الرباعي</label>
                        <input type="text" name="sender_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">رقم المحفظة المحول منها</label>
                        <input type="tel" name="sender_phone" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">صورة إثبات التحويل</label>
                        <input type="file" name="proof_image" class="form-control" accept="image/*" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane me-2"></i>
                        إرسال طلب المراجعة
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>

<div class="modal fade" id="methodModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <div class="d-flex align-items-center">
                    <img id="modalLogo" src="" alt="" style="width: 40px; height: 40px; object-fit: contain;" class="me-2">
                    <h5 class="modal-title fw-bold" id="methodModalTitle"></h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="text-muted small">اسم صاحب الحساب</label>
                    <div class="d-flex align-items-center">
                        <span class="fw-bold" id="modalAccountName"></span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">رقم الحساب</label>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="fw-bold" id="modalAccountNumber"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard(document.getElementById('modalAccountNumber').textContent)">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div id="modalQrCodeContainer" class="text-center mt-3" style="display: none;">
                    <label class="text-muted small">QR Code</label>
                    <div class="mt-2">
                        <img id="modalQrCode" src="" alt="QR Code" class="img-fluid" style="max-width: 200px;">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="selectCurrentMethod()">
                    <i class="fas fa-check me-2"></i>
                    اختيار
                </button>
            </div>
        </div>
    </div>
</div>
 @endsection

@section('scripts')
@parent
<script>
document.addEventListener('DOMContentLoaded', function() {
    const firstMethod = document.querySelector('.method-card');
    if (firstMethod) {
        firstMethod.classList.add('border-primary');
    }
});

function showMethodModal(el) {
    var id = el.dataset.id;
    var name = el.dataset.name;
    var accountName = el.dataset.accountName;
    var accountNumber = el.dataset.accountNumber;
    var qrCode = el.dataset.qrCode;
    var hasQrCode = el.dataset.hasQrCode === '1';
    var logo = el.dataset.logo;
    var hasLogo = el.dataset.hasLogo === '1';
    
    document.getElementById('methodModalTitle').textContent = name;
    document.getElementById('modalAccountName').textContent = accountName;
    document.getElementById('modalAccountNumber').textContent = accountNumber;
    
    if (hasLogo && logo) {
        document.getElementById('modalLogo').src = logo;
        document.getElementById('modalLogo').style.display = 'block';
    } else {
        document.getElementById('modalLogo').style.display = 'none';
    }
    
    if (hasQrCode && qrCode) {
        document.getElementById('modalQrCode').src = qrCode;
        document.getElementById('modalQrCodeContainer').style.display = 'block';
    } else {
        document.getElementById('modalQrCodeContainer').style.display = 'none';
    }
    
    document.getElementById('paymentMethodId').value = id;
    
    var modal = new bootstrap.Modal(document.getElementById('methodModal'));
    modal.show();
}

function selectCurrentMethod() {
    var modal = bootstrap.Modal.getInstance(document.getElementById('methodModal'));
    if (modal) {
        modal.hide();
    }
}

function toggleMethodDetails(id) {
    const details = document.getElementById('method-details-' + id);
    if (details.style.display === 'none') {
        details.style.display = 'block';
    } else {
        details.style.display = 'none';
    }
}

function selectMethod(id, el) {
    document.getElementById('paymentMethodId').value = id;
    document.querySelectorAll('.method-card').forEach(card => {
        card.classList.remove('border-primary');
    });
    el.classList.add('border-primary');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        Swal.fire({
            icon: 'success',
            title: 'تم النسخ',
            text: 'تم نسخ البيانات',
            timer: 1500,
            showConfirmButton: false
        });
});
}
</script>
@endsection
