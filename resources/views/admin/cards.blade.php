@extends('layouts.admin')

@section('title', 'البطاقات')

@section('admin_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold">
                <i class="fas fa-credit-card me-2 text-primary"></i>
                البطاقات
            </h4>
            <div>
                <button class="btn btn-info" onclick="showArchive()">
                    <i class="fas fa-archive me-2"></i>
                    الأرشيف
                </button>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import me-2"></i>
                    استيراد
                </button>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#clearModal">
                    <i class="fas fa-trash me-2"></i>
                    تفريغ
                </button>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.cards') }}" class="row g-3">
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
                    <a href="{{ route('admin.cards') }}" class="btn btn-secondary w-100">
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
                        <th>الحالة</th>
                        <th>تاريخ الإضافة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cards as $card)
                    <tr>
                        <td>{{ $card->id }}</td>
                        <td><code>{{ $card->username }}</code></td>
                        <td><code>{{ $card->password }}</code></td>
                        <td>{{ $card->package->name ?? '-' }}</td>
                        <td>
                            <span class="badge badge-status badge-{{ $card->status === 'available' ? 'approved' : ($card->status === 'sold' ? 'pending' : 'rejected') }}">
                                {{ $card->status === 'available' ? 'متاحة' : ($card->status === 'sold' ? 'مباعة' : 'مستخدمة') }}
                            </span>
                        </td>
                        <td>{{ $card->created_at->format('Y-m-d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $cards->links() }}
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.import_cards') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">استيراد بطاقات</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اختر الباقة</label>
                        <select name="package_id" class="form-control" required>
                            <option value="">اختر...</option>
                            @foreach(\App\Models\Package::all() as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملف Excel</label>
                        <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">الملف يجب أن يحتوي على عمودين: username, password</small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary">استيراد</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="clearModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.clear_cards') }}">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">تفريغ المخزون</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اختر الباقة</label>
                        <select name="package_id" class="form-control" required>
                            <option value="">اختر...</option>
                            @foreach(\App\Models\Package::all() as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        سيتم حذف جميع البطاقات المتاحة لهذه الباقة
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-danger">تفريغ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function showArchive() {
    window.location.href = '{{ route('admin.cards_archive') }}';
}
</script>
@endsection
