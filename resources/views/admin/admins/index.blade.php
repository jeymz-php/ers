@extends('layouts.admin')

@section('title', 'Administrators')
@section('page-title', 'Administrator Management')

@section('content')
<style>
    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .btn-create {
        background: #1a7a3e;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
    }
    .btn-edit {
        background: #1a7a3e;
        color: white;
        padding: 5px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 12px;
        display: inline-block;
    }
    .btn-edit:hover {
        background: #155e30;
        color: white;
    }
    .admins-table {
        background: white;
        border-radius: 16px;
        padding: 20px;
        overflow-x: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #e8eee9;
    }
    th {
        color: #1a7a3e;
        font-weight: 600;
        font-size: 13px;
    }
    .role-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .role-super_admin {
        background: #1a7a3e;
        color: white;
    }
    .role-admin {
        background: #4cca68;
        color: #1a7a3e;
    }
    .btn-delete {
        background: #dc2626;
        color: white;
        padding: 5px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 12px;
        border: none;
        cursor: pointer;
    }
    .warning {
        background: #fff3cd;
        color: #856404;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>

<div class="header-actions">
    <h2 style="color: #1a7a3e;">Administrator Accounts</h2>
    <a href="{{ route('admin.admins.create') }}" class="btn-create">+ Add New Admin</a>
</div>

@if(session('success'))
    <div class="warning" style="background: #d4f5df; color: #1a7a3e;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="warning">
        {{ session('error') }}
    </div>
@endif

<div class="admins-table">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Campus</th>
                <th>Role</th>
                <th>Created</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($admins as $admin)
            <tr>
                <td>{{ $admin->name }} @if($admin->id === auth()->id()) (You) @endif</td>
                <td>{{ $admin->email }}</td>
                <td>{{ $admin->phone_number }}</td>
                <td>{{ $admin->campus->name ?? 'N/A' }}</td>
                <td>
                    <span class="role-badge role-{{ $admin->role }}">
                        {{ $admin->role === 'super_admin' ? 'Super Admin' : 'Admin' }}
                    </span>
                </td>
                <td>{{ $admin->created_at->format('M d, Y') }}</td>
                <td style="display: flex; gap: 5px; align-items: center;">
                    @if($admin->id !== auth()->id())
                        <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn-edit">Edit</a>
                        <form method="POST" action="{{ route('admin.admins.destroy', $admin->id) }}" style="display: inline;" onsubmit="return confirm('Delete this admin account?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    @else
                        <span style="color: #b0bdb3;">Current</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">No administrators found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="pagination" style="margin-top: 20px;">
        {{ $admins->links() }}
    </div>
</div>
@endsection