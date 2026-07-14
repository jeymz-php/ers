@extends('layouts.admin')

@section('title', 'Manage Reservations')
@section('page-title', 'All Reservations')

@section('content')
<style>
    .filters-bar {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
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
        background: white;
    }
    
    .btn-apply {
        background: #2db84f;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.3s;
    }
    
    .btn-apply:hover {
        background: #1a7a3e;
    }
    
    .btn-reset {
        background: #6e7f72;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        display: inline-block;
        text-align: center;
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
    
    .status-rejected {
        background: #fef2f2;
        color: #dc2626;
    }
    
    .btn-action {
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
    }
    
    .btn-view {
        color: #2db84f;
    }
    
    .btn-delete {
        color: #dc2626;
    }

    /* Icon action buttons */
    .action-icons {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        background: #f0faf3;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .btn-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
    }

    .btn-icon-view { color: #2db84f; }
    .btn-icon-report { color: #1a7a3e; background: #e8f5ea; }
    .btn-icon-edit { color: #f5a524; background: #fff7e6; }
    .btn-icon-delete { color: #dc2626; background: #fef2f2; }
    
    .reservations-table {
        background: white;
        border-radius: 16px;
        padding: 20px;
        overflow-x: auto;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
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
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
    }
    
    .multi-date-badge {
        display: inline-block;
        background: #2db84f;
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 9px;
        margin-left: 5px;
    }
    
    .date-range {
        display: block;
        font-size: 10px;
        color: #6e7f72;
        margin-top: 2px;
    }
    
    .pagination {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .generate-report-btn {
        background: #dc2626;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
    }
    
    @media (max-width: 768px) {
        .filters-bar .grid-layout {
            grid-template-columns: 1fr !important;
        }
        .filter-group {
            margin-bottom: 15px;
        }
        .btn-apply, .btn-reset {
            width: 100%;
        }
        .pagination {
            flex-direction: column;
            align-items: center;
        }
    }
</style>

<div class="filters-bar">
    <form method="GET" action="{{ route('admin.reservations.index') }}" id="filterForm">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div class="filter-group">
                <label>📅 Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="filter-group">
                <label>📅 End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}">
            </div>
            <div class="filter-group">
                <label>🔍 Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or event...">
            </div>
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
                <label>📊 Status</label>
                <select name="status">
                    <option value="all">All</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="filter-group" style="display: flex; gap: 10px; align-items: flex-end;">
                <button type="submit" class="btn-apply">Apply Filters</button>
                <a href="{{ route('admin.reservations.index') }}" class="btn-reset">Reset</a>
            </div>
        </div>
    </form>
</div>

<div style="display: flex; justify-content: flex-end; margin-bottom: 20px; gap: 10px; flex-wrap: wrap;">
    <a href="{{ route('admin.reservations.create') }}" class="generate-report-btn" style="background: #1a7a3e;">
        ➕ Add Reservation
    </a>
    <a href="{{ route('report.all') }}?{{ http_build_query(request()->query()) }}" class="generate-report-btn" target="_blank">
        📊 Generate Report
    </a>
</div>

@if(session('success'))
    <div style="background: #d4f5df; color: #1a7a3e; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

<div class="reservations-table">
    <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h3 style="color: #1a7a3e;">All Reservations</h3>
        <div>
            <span style="font-size: 12px; color: #6e7f72;">Total: {{ $reservations->total() }} reservations</span>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Event Name</th>
                <th>Event Date(s)</th>
                <th>Start</th>
                <th>End</th>
                <th>Venue</th>
                <th>Department</th>
                <th>Campus</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations as $res)
            @php
                $remarks = json_decode($res->remarks, true);
                $department = $remarks['department'] ?? 'N/A';
                $multipleDates = $remarks['multiple_dates'] ?? [$res->event_date];
                $isMultiDate = count($multipleDates) > 1;
                $clientName = $res->user ? $res->user->name : 'Unknown User';
                $venueName = $res->establishment ? $res->establishment->name : 'N/A';
                $campusName = $res->campus ? $res->campus->name : 'N/A';
            @endphp
            <tr>
                <td>{{ $clientName }}</td>
                <td>{{ Str::limit($res->event_name, 30) }}@if($isMultiDate) <span class="multi-date-badge">{{ count($multipleDates) }} dates</span>@endif</td>
                <td>
                    @if($isMultiDate)
                        <strong>{{ count($multipleDates) }} dates</strong>
                        <div class="date-range">
                            {{ \Carbon\Carbon::parse($multipleDates[0])->format('M d') }} - {{ \Carbon\Carbon::parse(end($multipleDates))->format('M d, Y') }}
                        </div>
                    @else
                        {{ \Carbon\Carbon::parse($res->event_date)->format('M d, Y') }}
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($res->start_time)->format('g:i A') }}</td>
                <td>{{ \Carbon\Carbon::parse($res->end_time)->format('g:i A') }}</td>
                <td>{{ $venueName }}</td>
                <td>{{ Str::limit($department, 15) }}</td>
                <td>{{ $campusName }}</td>
                <td>
                    <span class="status-badge status-{{ $res->status }}">
                        {{ strtoupper($res->status) }}
                    </span>
                </td>
                <td>
                    <div class="action-icons">
                        <a href="{{ route('admin.reservations.show', $res->id) }}" class="btn-icon btn-icon-view" title="View Details">👁️</a>
                        <a href="{{ route('report.single', $res->id) }}" class="btn-icon btn-icon-report" title="Generate Report" target="_blank">📄</a>
                        <a href="{{ route('admin.reservations.edit', $res->id) }}" class="btn-icon btn-icon-edit" title="Edit Reservation">✏️</a>
                        <form method="POST" action="{{ route('admin.reservations.destroy', $res->id) }}" style="display: inline;" onsubmit="return confirm('Delete this reservation?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon btn-icon-delete" title="Delete Reservation">🗑️</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align: center; padding: 40px;">No reservations found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="pagination">
        <div>
            Rows per page: 
            <select id="perPage" onchange="changePerPage()" style="padding: 4px; border-radius: 6px;">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            </select>
        </div>
        <div>
            {{ $reservations->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<script>
    function changePerPage() {
        const perPage = document.getElementById('perPage').value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        window.location.href = url.toString();
    }
</script>
@endsection