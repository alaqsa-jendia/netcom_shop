@extends('layouts.user')

@section('title', 'بطاقاتي')

@section('user_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold mb-3">
                <i class="fas fa-credit-card me-2 text-primary"></i>
                بطاقاتي
            </h4>
            <div>
                <a href="{{ route('my_cards_archive') }}" class="btn btn-info">
                    <i class="fas fa-archive me-2"></i>
                    الأرشيف
                </a>
            </div>
        </div>
    </div>

    @if($cards->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-credit-card fa-4x text-muted mb-4"></i>
                <h5 class="fw-bold mb-3">لا توجد بطاقات</h5>
                <p class="text-muted mb-4">لم تقم بشراء أي بطاقات بعد</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-cart me-2"></i>
                    اذهب للمتجر
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم المستخدم</th>
                            <th>كلمة المرور</th>
                            <th>تاريخ الشراء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cards as $index => $card)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <code class="bg-light px-2 py-1 rounded">{{ $card->username }}</code>
                            </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $card->password }}</code>
                                </td>
                                <td>{{ $card->sold_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="markAsUsed({{ $card->id }})">
                                        <i class="fas fa-check me-1"></i>
                                        تم الاستخدام
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success mt-3">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
</div>
@endif
 @endsection

@section('scripts')
@parent
<script>
function markAsUsed(cardId) {
    Swal.fire({
        title: 'تحديد البطاقة',
        text: 'هل أنت متأكد من تحديد هذه البطاقة ك مستخدمة؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'نعم',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/my-cards/' + cardId + '/use', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                }
            });
        }
    });
}
</script>
@endsection
