@extends('layouts.admin')

@section('title', 'Registered Users')
@section('page-title', 'User Management')

@section('content')
<style>
    .users-container {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }
    
    .users-section {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e8eee9;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a7a3e;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-count {
        background: #2db84f;
        color: white;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 12px;
    }
    
    .filters-bar {
        background: #f7faf8;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .filter-group {
        margin-bottom: 0;
    }
    
    .filter-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #1a7a3e;
        margin-bottom: 5px;
    }
    
    .filter-group input, .filter-group select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        font-size: 13px;
    }
    
    .btn-apply {
        background: #2db84f;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 24px;
    }
    
    .btn-apply-all {
        background: #ff9800;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table th, .data-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #e8eee9;
    }
    
    .data-table th {
        background: #f7faf8;
        color: #1a7a3e;
        font-weight: 700;
        font-size: 12px;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .status-approved {
        background: #d4f5df;
        color: #1a7a3e;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .btn-view {
        background: #2db84f;
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 11px;
    }
    
    .btn-delete {
        background: #dc3545;
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        border: none;
        font-size: 11px;
        cursor: pointer;
    }
    
    .btn-delete:hover {
        background: #b02a37;
    }
    
    .checkbox-col {
        width: 30px;
    }
    
    .select-all {
        cursor: pointer;
    }
    
    /* Pagination Styles */
    .pagination-container {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #e8eee9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .pagination-info {
        font-size: 13px;
        color: #6e7f72;
    }
    
    .pagination {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .pagination .page-link,
    .pagination a,
    .pagination span {
        display: inline-block;
        padding: 6px 12px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        font-size: 13px;
        text-decoration: none;
        color: #1a7a3e;
        background: white;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .pagination a:hover {
        background: #1a7a3e;
        color: white;
        border-color: #1a7a3e;
    }
    
    .pagination .active span,
    .pagination span.active {
        background: #2db84f;
        color: white;
        border-color: #2db84f;
    }
    
    .pagination .disabled span,
    .pagination span.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    @media (max-width: 768px) {
        .data-table {
            font-size: 11px;
        }
        .data-table th, .data-table td {
            padding: 8px;
        }
        .filters-bar .grid-layout {
            grid-template-columns: 1fr !important;
        }
        .pagination-container {
            flex-direction: column;
            align-items: center;
        }
    }
</style>

<div class="users-container">
    @if(session('success'))
        <div style="background: #d4f5df; color: #1a7a3e; padding: 15px 20px; border-radius: 10px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            ✅ {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div style="background: #fee2e2; color: #dc2626; padding: 15px 20px; border-radius: 10px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            ⚠️ {{ session('error') }}
        </div>
    @endif
    <!-- Filters -->
    <div class="filters-bar">
        <form method="GET" action="{{ route('admin.users.index') }}" id="filterForm">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div class="filter-group">
                    <label>🏛️ Campus</label>
                    <select name="campus_id">
                        <option value="all">All Campuses</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                                {{ $campus->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>🔍 Search</label>
                    <input type="text" name="search" placeholder="Name or Email" value="{{ request('search') }}">
                </div>
                <div class="filter-group">
                    <button type="submit" class="btn-apply">Apply Filters</button>
                    <a href="{{ route('admin.users.index') }}" class="btn-apply" style="background: #6e7f72; text-decoration: none; margin-left: 5px;">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Pending Users Section -->
    <div class="users-section">
        <div class="section-header">
            <div class="section-title">
                ⏳ Pending Approval
                <span class="section-count">{{ $pendingUsers->total() }}</span>
            </div>
            <div>
                <button class="btn-apply-all" onclick="bulkApprove()">✅ Approve Selected</button>
            </div>
        </div>
        
        <form id="bulkApproveForm" method="POST" action="{{ route('admin.users.bulk-approve') }}">
            @csrf
            <input type="hidden" name="ids" id="selectedIds" value="">
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="checkbox-col"><input type="checkbox" id="selectAllPending" class="select-all" onclick="toggleSelectAll('pending')"></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact Number</th>
                        <th>Campus</th>
                        <th>Registration Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingUsers as $user)
                    <tr>
                        <td><input type="checkbox" class="pending-checkbox" value="{{ $user->id }}"></td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone_number ?? 'N/A' }}</td>
                        <td>{{ $user->campus->name ?? 'N/A' }}</td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn-view">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">No pending users</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
        
        <div class="pagination-container">
            <div class="pagination-info">
                Showing {{ $pendingUsers->firstItem() ?? 0 }} to {{ $pendingUsers->lastItem() ?? 0 }} of {{ $pendingUsers->total() }} pending users
            </div>
            <div class="pagination">
                {{ $pendingUsers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Approved Users Section -->
    <div class="users-section">
        <div class="section-header">
            <div class="section-title">
                ✅ Registered Users
                <span class="section-count">{{ $approvedUsers->total() }}</span>
            </div>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Campus</th>
                    <th>Registration Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($approvedUsers as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone_number ?? 'N/A' }}</td>
                    <td>{{ $user->campus->name ?? 'N/A' }}</td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td><span class="status-badge status-approved">Approved</span></td>
                    <td style="display: flex; gap: 5px; align-items: center;">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn-view">View</a>
                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Are you sure you want to delete {{ addslashes($user->name) }}? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">No registered users</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="pagination-container">
            <div class="pagination-info">
                Showing {{ $approvedUsers->firstItem() ?? 0 }} to {{ $approvedUsers->lastItem() ?? 0 }} of {{ $approvedUsers->total() }} registered users
            </div>
            <div class="pagination">
                {{ $approvedUsers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSelectAll(type) {
        const checkboxes = document.querySelectorAll(`.${type}-checkbox`);
        const selectAll = document.getElementById(`selectAll${type.charAt(0).toUpperCase() + type.slice(1)}`);
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
    }
    
    function bulkApprove() {
        const selected = [];
        document.querySelectorAll('.pending-checkbox:checked').forEach(cb => {
            selected.push(cb.value);
        });
        
        if (selected.length === 0) {
            alert('Please select at least one user to approve');
            return;
        }
        
        if (confirm(`Approve ${selected.length} user(s)?`)) {
            // Send as JSON string, not as an array directly
            document.getElementById('selectedIds').value = JSON.stringify(selected);
            document.getElementById('bulkApproveForm').submit();
        }
    }
</script>
@endsection