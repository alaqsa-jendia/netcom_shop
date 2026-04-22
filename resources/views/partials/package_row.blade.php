<tr id="package-{{ $package->id }}">
    <td>{{ $package->id }}</td>
    <td>{{ $package->name }}</td>
    <td class="fw-bold text-success">{{ number_format($package->price, 2) }} شيكل</td>
    <td>{{ $package->quantity }}</td>
    <td>
        @if($package->is_active)
            <span class="badge bg-success">مفعل</span>
        @else
            <span class="badge bg-secondary">غير مفعل</span>
        @endif
    </td>
    <td>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('admin.edit_package', $package->id) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit"></i>
            </a>
            <button type="button" class="btn btn-outline-danger" onclick="ajaxDelete('/admin/packages/{{ $package->id }}/delete', 'package-{{ $package->id }}')">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </td>
</tr>