@extends('layouts.user')

@section('title', 'أرشيف البطاقات')

@section('user_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold mb-3">
                <i class="fas fa-archive me-2 text-info"></i>
                أرشيف البطاقات
            </h4>
            <div>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#clearArchiveModal">
                    <i class="fas fa-trash me-2"></i>
                    تفريغ الأرشيف
                </button>
                <a href="{{ route('my_cards') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-2"></i>
                    العودة للبطاقات
                </a>
            </div>
        </div>
    </div>

    @if($cards->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-archive fa-4x text-muted mb-4"></i>
                <h5 class="fw-bold mb-3">لا توجد بطاقات مستخدمة</h5>
                <p class="text-muted mb-4">لم تقم باستخدام أي بطاقات بعد</p>
            </div>
        </div>
    @else
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i>
            هذه البطاقات تم استخدامها ولا يمكن استخدامها مرة أخرى
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم المستخدم</th>
                            <th>كلمة المرور</th>
                            <th>تاريخ الاستخدام</th>
                            <th>الحالة</th>
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
                                    <span class="badge bg-secondary">مستخدمة</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
</div>
@endif
</div>

<div class="modal fade" id="clearArchiveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('my_cards_archive_clear') }}">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">تفريغ الأرشيف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        سيتم حذف جميع البطاقات المستخدمة من الأرشيف نهائياً ولا يمكن استرجاعها
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">حذف نهائي</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection