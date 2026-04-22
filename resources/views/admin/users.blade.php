@extends('layouts.admin')

@section('title', 'المستخدمين')

@section('admin_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold">
                <i class="fas fa-users me-2 text-primary"></i>
                المستخدمين
            </h4>
            <a href="{{ route('admin.create_user') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>
                إنشاء مستخدم
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الجوال</th>
                        <th>الرصيد</th>
                        <th>الحالة</th>
                        <th>تاريخ التسجيل</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->phone }}</td>
                        <td class="fw-bold">{{ number_format($user->balance, 2) }}</td>
                        <td>
                            <span class="badge badge-status {{ $user->status === 'active' ? 'badge-approved' : 'badge-rejected' }}">
                                {{ $user->status === 'active' ? 'نشط' : 'موقوف' }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.edit_user', $user->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-warning btn-sm" onclick="return toggleMethodStatus('{{ route('admin.toggle_user', $user->id) }}', 'هل أنت متأكد من تغيير حالة هذا المستخدم؟')">
                                    <i class="fas fa-toggle-on"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteConfirm('{{ route('admin.delete_user', $user->id) }}', 'هل أنت متأكد من حذف هذا المستخدم؟')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
