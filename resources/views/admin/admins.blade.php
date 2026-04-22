@extends('layouts.admin')

@section('title', 'المدراء')

@section('admin_content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-12 col-md-6">
            <h4 class="fw-bold">
                <i class="fas fa-user-shield me-2 text-primary"></i>
                المدراء
            </h4>
        </div>
        <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.create_admin') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                إضافة مدير
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم المستخدم</th>
                        <th>الاسم</th>
                        <th>الدور</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
                    <tr>
                        <td>{{ $admin->id }}</td>
                        <td>{{ $admin->username }}</td>
                        <td>{{ $admin->name }}</td>
                        <td>
                            <span class="badge badge-status {{ $admin->role === 'super_admin' ? 'badge-approved' : 'badge-pending' }}">
                                {{ $admin->role === 'super_admin' ? 'مدير عام' : 'مدير' }}
                            </span>
                        </td>
                        <td>{{ optional($admin->created_at)->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.edit_admin', $admin->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteConfirm('{{ route('admin.delete_admin', $admin->id) }}', 'هل أنت متأكد من حذف هذا المدير؟')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection