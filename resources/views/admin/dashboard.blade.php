@extends('layouts.admin')

@section('title', 'لوحة تحكم الأدمن')

@section('admin_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold">
                <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                الإحصائيات
            </h4>
        </div>
    </div>

     <div class="row g-4 mb-4">
         <div class="col-12 col-sm-6 col-lg-3">
             <div class="stat-card primary h-100">
                 <div class="d-flex justify-content-between align-items-center">
                     <div>
                         <p class="text-muted mb-1">المستخدمين</p>
                         <h3 class="fw-bold mb-0">{{ $stats['users_count'] }}</h3>
                     </div>
                     <div class="stat-icon">
                         <i class="fas fa-users fa-2x"></i>
                     </div>
                 </div>
             </div>
         </div>

         <div class="col-12 col-sm-6 col-lg-3">
             <div class="stat-card success h-100">
                 <div class="d-flex justify-content-between align-items-center">
                     <div>
                         <p class="text-muted mb-1">المدراء</p>
                         <h3 class="fw-bold mb-0">{{ $stats['admins_count'] }}</h3>
                     </div>
                     <div class="stat-icon">
                         <i class="fas fa-user-shield fa-2x"></i>
                     </div>
                 </div>
             </div>
         </div>

         <div class="col-12 col-sm-6 col-lg-3">
             <div class="stat-card warning h-100">
                 <div class="d-flex justify-content-between align-items-center">
                     <div>
                         <p class="text-muted mb-1">الطلبات المعلقة</p>
                         <h3 class="fw-bold mb-0">{{ $stats['pending_requests'] }}</h3>
                     </div>
                     <div class="stat-icon">
                         <i class="fas fa-clock fa-2x"></i>
                     </div>
                 </div>
             </div>
         </div>

          <div class="col-12 col-sm-6 col-lg-3">
              <div class="stat-card success h-100">
                  <div class="d-flex justify-content-between align-items-center">
                      <div>
                          <p class="text-muted mb-1">الطلبات المقبولة</p>
                          <h3 class="fw-bold mb-0">{{ $stats['approved_requests'] }}</h3>
                      </div>
                     <div class="stat-icon">
                         <i class="fas fa-check-circle fa-2x"></i>
                     </div>
                  </div>
              </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3">
              <div class="stat-card danger h-100">
                  <div class="d-flex justify-content-between align-items-center">
                      <div>
                          <p class="text-muted mb-1">الطلبات المرفوضة</p>
                          <h3 class="fw-bold mb-0">{{ $stats['rejected_requests'] }}</h3>
                      </div>
                     <div class="stat-icon">
                         <i class="fas fa-times-circle fa-2x"></i>
                     </div>
                  </div>
              </div>
          </div>
      </div>

      <div class="row g-4 mb-4">
          <div class="col-12 col-sm-6 col-lg-3">
              <div class="stat-card primary h-100 shadow-sm rounded-3">
                  <div class="d-flex justify-content-between align-items-center">
                      <div>
                          <p class="text-muted mb-1">إجمالي التحويلات</p>
                          <h3 class="fw-bold mb-0">{{ number_format($stats['total_transfers'], 2) }}</h3>
                          <small class="text-muted">شيكل</small>
                      </div>
                          <div class="stat-icon">
                              <i class="fas fa-shekel-sign fa-2x"></i>
                          </div>
                  </div>
              </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3">
              <div class="stat-card success h-100">
                  <div class="d-flex justify-content-between align-items-center">
                      <div>
                          <p class="text-muted mb-1">إجمالي المبيعات</p>
                          <h3 class="fw-bold mb-0">{{ $stats['total_sales'] }}</h3>
                      </div>
                      <div class="stat-icon">
                          <i class="fas fa-shopping-cart fa-2x"></i>
                      </div>
                  </div>
              </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3">
              <div class="stat-card warning h-500">
                  <div class="d-flex justify-content-between align-items-center">
                      <div>
                          <p class="text-muted mb-1">البطاقات المتبقية</p>
                          <h3 class="fw-bold mb-0">{{ $stats['cards_remaining'] }}</h3>
                      </div>
                      <div class="stat-icon">
                          <i class="fas fa-credit-card fa-2x"></i>
                      </div>
                  </div>
              </div>
          </div>
      </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-clock me-2"></i>
                        طلبات الشحن المعلقة
                    </h5>
                </div>
                <div class="card-body">
                    @if($pendingRequests->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-3x text-success"></i>
                            <p class="mt-3">لا توجد طلبات معلقة</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>المستخدم</th>
                                        <th>المبلغ</th>
                                        <th>طريقة الدفع</th>
                                        <th>التاريخ</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingRequests as $request)
                                    <tr>
                                        <td>
                                            <div>{{ $request->user->name }}</div>
                                            <small class="text-muted">{{ $request->user->phone }}</small>
                                        </td>
                                        <td class="fw-bold">{{ number_format($request->amount, 2) }} شيكل</td>
                                        <td>{{ $request->paymentMethod->name }}</td>
                                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-1 flex-wrap">
                                                <button class="btn btn-info btn-sm" onclick="viewRequestDetails({{ $request->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="{{ route('admin.approve_recharge', $request->id) }}" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <button class="btn btn-danger btn-sm" onclick="rejectRequest({{ $request->id }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
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
                <div id="requestDetailsContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <a href="#" id="approveLink" class="btn btn-success">موافقة</a>
                <button type="button" class="btn btn-danger" onclick="rejectFromDetails()">رفض</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentRequestId = null;

function viewRequestDetails(id) {
    currentRequestId = id;
    const content = document.getElementById('requestDetailsContent');
    content.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
    
    fetch('/admin/recharge-requests/' + id + '/details')
        .then(response => response.json())
        .then(data => {
            console.log('API Response:', data);
            let html = '<div class="row">';
            html += '<div class="col-md-6 mb-3"><label class="text-muted small">المستخدم</label><div class="fw-bold">' + data.user_name + '</div><small class="text-muted">' + data.user_phone + '</small></div>';
            html += '<div class="col-md-6 mb-3"><label class="text-muted small">المبلغ</label><div class="fw-bold text-success fs-5">' + data.amount + ' شيكل</div></div>';
            html += '<div class="col-md-6 mb-3"><label class="text-muted small">طريقة الدفع</label><div class="fw-bold">' + data.payment_method + '</div></div>';
            html += '<div class="col-md-6 mb-3"><label class="text-muted small">التاريخ</label><div class="fw-bold">' + data.created_at + '</div></div>';
            html += '<div class="col-md-6 mb-3"><label class="text-muted small">اسم المحول</label><div class="fw-bold">' + data.sender_name + '</div></div>';
            html += '<div class="col-md-6 mb-3"><label class="text-muted small">رقم المحفظة</label><div class="fw-bold">' + data.sender_phone + '</div></div>';
            if (data.proof_image) {
                html += '<div class="col-12 mt-3"><label class="text-muted small">صورة الإشعار</label><div class="mt-2"><img src="' + data.proof_image + '" alt="proof" class="img-fluid rounded" style="max-height: 300px; cursor: pointer;" onclick="showFullImage(this.src)"></div></div>';
            } else if (data.stored_path) {
                html += '<div class="col-12 mt-3"><div class="alert alert-warning">Image not found. Stored path: ' + data.stored_path + '</div></div>';
            } else {
                html += '<div class="col-12 mt-3"><div class="alert alert-info">No image attached</div></div>';
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

function rejectFromDetails() {
    if (currentRequestId) {
        bootstrap.Modal.getInstance(document.getElementById('detailsModal')).hide();
        rejectRequest(currentRequestId);
    }
}

function rejectRequest(id) {
    document.getElementById('rejectForm').action = '/admin/recharge-requests/' + id + '/reject';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function showFullImage(src) {
    document.getElementById('fullImage').src = src;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
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
