@extends('layouts.admin')

@section('title', 'أرشيف البطاقات')

@section('admin_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold">
                <i class="fas fa-archive me-2 text-info"></i>
                أرشيف البطاقات المباعة
            </h4>
            <div>
                <button class="btn btn-secondary" onclick="window.location.href='{{ route('admin.cards') }}'">
                    <i class="fas fa-arrow-right me-2"></i>
                    العودة
                </button>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#clearArchiveModal">
                    <i class="fas fa-trash me-2"></i>
                    تفريغ الأرشيف
                </button>
            </div>
        </div>
    </div>

    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        هذه البطاقات مباعة ولا يمكن بيعها مرة أخرى. يمكنك تفريغ الأرشيف للتخفيف على النظام.
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.cards_archive') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">تصفية حسب الباقة</label>
                    <select name="package_id" class="form-select">
                        <option value="">كل الباقات</option>
                        @foreach($packages as $package)
                        <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>
                            {{ $package->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>
                        تصفية
                    </button>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('admin.cards_archive') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-times me-2"></i>
                        إلغاء التصفية
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم المستخدم</th>
                        <th>كلمة المرور</th>
                        <th>الباقة</th>
                        <th>المشتري</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cards as $card)
                    <tr>
                        <td>{{ $card->id }}</td>
                        <td><code>{{ $card->username }}</code></td>
                        <td><code>{{ $card->password }}</code></td>
                        <td>{{ $card->package->name ?? '-' }}</td>
                        <td>{{ $card->user->name ?? '-' }}</td>
                        <td>
                            @if($card->status === 'used')
                                <span class="badge bg-secondary">مستخدمة</span>
                            @else
                                <span class="badge bg-success">مباعة</span>
                            @endif
                        </td>
                        <td>{{ $card->sold_at ? $card->sold_at->format('Y-m-d H:i') : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>لا توجد بطاقات مباعة في الأرشيف</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $cards->links() }}
        </div>
    </div>
</div>

<div class="modal fade" id="clearArchiveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.clear_archive') }}">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">تفريغ الأرشيف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اختر الباقة (اختياري)</label>
                        <select name="package_id" class="form-select">
                            <option value="">كل الباقات</option>
                            @foreach($packages as $package)
                            <option value="{{ $package->id }}">{{ $package->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">إذا اخترت باقة معينة سيتم حذف بطاقات هذه الباقة فقط</small>
                    </div>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        سيتم حذف جميع البطاقات المباعة نهائياً ولا يمكن استرجاعها
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

<script>
function showArchive() {
    window.location.href = '{{ route('admin.cards_archive') }}';
}
</script>
@endsection