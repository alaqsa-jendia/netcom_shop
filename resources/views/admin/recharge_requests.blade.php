@extends('layouts.admin')

@section('title', 'طلبات الشحن')

@section('admin_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold">
                <i class="fas fa-money-bill-wave me-2 text-primary"></i>
                طلبات الشحن
            </h4>
            <div class="btn-group">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-home me-2"></i>
                    الرئيسية
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المستخدم</th>
                        <th>الجوال</th>
                        <th>المبلغ</th>
                        <th>طريقة الدفع</th>
                        <th>اسم المحول</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr>
                        <td>{{ $request->id }}</td>
                        <td>{{ $request->user?->name ?? 'غير موجود' }}</td>
                        <td>{{ $request->user?->phone ?? 'غير موجود' }}</td>
                        <td class="fw-bold text-success">{{ number_format($request->amount, 2) }} شيكل</td>
                        <td>{{ $request->paymentMethod?->name ?? 'غير موجودة' }}</td>
                        <td>{{ $request->sender_name }}</td>
                        <td>
                            @if($request->status === 'pending')
                                <span class="badge bg-warning text-dark">معلق</span>
                            @elseif($request->status === 'approved')
                                <span class="badge bg-success">مقبول</span>
                            @elseif($request->status === 'rejected')
                                <span class="badge bg-danger">مرفوض</span>
                            @endif
                        </td>
                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            @if($request->status === 'pending')
                                <div class="d-flex gap-1 flex-wrap">
                                    <button type="button" class="btn btn-success btn-sm" onclick="approveRequest({{ $request->id }})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="rejectRequest({{ $request->id }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endif
                            <div class="d-flex gap-1 flex-wrap">
                                <button type="button" class="btn btn-info btn-sm" onclick="viewDetails({{ $request->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteConfirm(&quot;{{ route('admin.delete_recharge', $request->id) }}&quot;, 'هل أنت متأكد من حذف هذا الطلب؟')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>لا توجد طلبات شحن</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $requests->links() }}
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="rejectForm">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">رفض الطلب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">سبب الرفض</label>
                        <textarea name="reason" class="form-control" rows="3" required minlength="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">رفض</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">تفاصيل طلب الشحن</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <a href="#" id="approveLink" class="btn btn-success">موافقة</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function rejectRequest(id) {
    document.getElementById('rejectForm').action = '/admin/recharge-requests/' + id + '/reject';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function viewDetails(id) {
    const content = document.getElementById('detailsContent');
    content.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
    
    fetch('/admin/recharge-requests/' + id + '/details')
        .then(response => response.json())
        .then(data => {
            let html = '<div class="row">';
            html += '<div class="col-md-6 mb-3"><label class="text-muted small">المستخدم</label><div class="fw-bold">' + data.user_name + '</div><small class="text-muted">' + data.user_phone + '</small></div>';
            html += '<div class="col-md-6 mb-3"><label class="text-muted small">المبلغ</label><div class="fw-bold text-success fs-5">' + data.amount + ' شيكل</div></div>';
            html += '<div class="col-md-6 mb-3"><label class="text-muted small">طريقة الدفع</label><div class="fw-bold">' + data.payment_method + '</div></div>';
            html += '<div class="col-md-6 mb-3"><label class="text-muted small">التاريخ</label><div class="fw-bold">' + data.created_at + '</div></div>';
            html += '<div class="col-md-6 mb-3"><label class="text-muted small">اسم المحول</label><div class="fw-bold">' + data.sender_name + '</div></div>';
            html += '<div class="col-md-6 mb-3"><label class="text-muted small">رقم المحفظة</label><div class="fw-bold">' + data.sender_phone + '</div></div>';
            if (data.proof_image) {
                html += '<div class="col-12 mt-3"><label class="text-muted small">صورة الإشعار</label><div class="mt-2"><img src="' + data.proof_image + '" alt="proof" class="img-fluid rounded" style="max-height: 300px; cursor: pointer;" onclick="showFullImage(this.src)"></div></div>';
            }
            html += '</div>';
            content.innerHTML = html;
            document.getElementById('approveLink').href = '/admin/recharge-requests/' + id + '/approve';
        })
        .catch(() => {
            content.innerHTML = '<div class="text-center text-danger">حدث خطأ في تحميل التفاصيل</div>';
        });
    
    new bootstrap.Modal(document.getElementById('detailsModal')).show();
}

function showFullImage(src) {
    document.getElementById('fullImage').src = src;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function approveRequest(id) {
    Swal.fire({
        title: 'تأكيد الموافقة',
        text: 'هل أنت متأكد من الموافقة على طلب الشحن هذا؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'موافقة',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/admin/recharge-requests/' + id + '/approve';
        }
    });
}

function rejectRequest(id) {
    document.getElementById('rejectForm').action = '/admin/recharge-requests/' + id + '/reject';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>

<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="fullImage" src="" alt="Full size image" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endsection