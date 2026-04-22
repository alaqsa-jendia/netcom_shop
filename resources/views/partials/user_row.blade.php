<tr id="user-{{ $user->id }}">
    <td>{{ $user->id }}</td>
    <td>{{ $user->name }}</td>
    <td>{{ $user->phone }}</td>
    <td>{{ number_format($user->balance, 2) }} شيكل</td>
    <td>
        @if($user->status === 'active')
            <span class="badge bg-success">مفعل</span>
        @else
            <span class="badge bg-danger">غير مفعل</span>
        @endif
    </td>
    <td>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('admin.edit_user', $user->id) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit"></i>
            </a>
            <button type="button" class="btn btn-outline-{{ $user->status === 'active' ? 'danger' : 'success' }}" onclick="return toggleMethodStatus('{{ route('admin.toggle_user', $user->id) }}', 'هل أنت متأكد من تغيير حالة هذا المستخدم؟')">
                <i class="fas fa-toggle-{{ $user->status === 'active' ? 'off' : 'on' }}"></i>
            </button>
            <button type="button" class="btn btn-outline-danger" onclick="ajaxDelete('/admin/users/{{ $user->id }}/delete', 'user-{{ $user->id }}')">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </td>
</tr>