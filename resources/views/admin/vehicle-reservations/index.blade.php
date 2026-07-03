@extends('layouts.admin')

@section('title', 'Vehicle Reservations')
@section('page-title', 'Pickup Vehicle Reservations')

@section('content')
<style>
    .filters-bar {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
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

    .status-approved { background: #d4f5df; color: #1a7a3e; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-rejected { background: #fef2f2; color: #dc2626; }
    .status-cancelled { background: #e8eee9; color: #6e7f72; }

    .btn-action {
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
    }

    .btn-view { color: #2db84f; }
    .btn-delete { color: #dc2626; }

    .reservations-table {
        background: white;
        border-radius: 16px;
        padding: 20px;
        overflow-x: auto;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    table { width: 100%; border-collapse: collapse; }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #e8eee9;
        font-size: 13px;
    }

    th {
        color: #1a7a3e;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
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
        background: #1a7a3e;
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
        .pagination {
            flex-direction: column;
            align-items: center;
        }
    }
</style>

<div class="filters-bar">
    <form method="GET" action="{{ route('admin.vehicle-reservations.index') }}" id="filterForm">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px;">
            <div class="filter-group">
                <label>📅 Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="filter-group">
                <label>📅 End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}">
            </div>
            <div class="filter-group">
                <label>🔍 Search Requester</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name...">
            </div>
            <div class="filter-group">
                <label>🏛️ Origin Campus</label>
                <select name="campus_id">
                    <option value="all">All Campuses</option>
                    @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>🎯 Purpose</label>
                <select name="purpose">
                    <option value="all">All</option>
                    <option value="transporting" {{ request('purpose') == 'transporting' ? 'selected' : '' }}>Transporting</option>
                    <option value="delivery" {{ request('purpose') == 'delivery' ? 'selected' : '' }}>Items Delivery</option>
                    <option value="other" {{ request('purpose') == 'other' ? 'selected' : '' }}>Other</option>
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
                <a href="{{ route('admin.vehicle-reservations.index') }}" class="btn-reset">Reset</a>
            </div>
        </div>
    </form>
</div>

<div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
    <a href="{{ route('admin.vehicle-reservations.create') }}" class="generate-report-btn">➕ Add Vehicle Reservation</a>
</div>

@if(session('success'))
    <div style="background: #d4f5df; color: #1a7a3e; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

<div class="reservations-table">
    <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h3 style="color: #1a7a3e;">All Pickup Vehicle Reservations</h3>
        <span style="font-size: 12px; color: #6e7f72;">Total: {{ $reservations->total() }} requests</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Requester</th>
                <th>Type</th>
                <th>Origin</th>
                <th>Purpose</th>
                <th>Destination</th>
                <th>Trip Date</th>
                <th>Pickup Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations as $res)
            <tr>
                <td>{{ $res->user->name ?? 'Unknown' }}</td>
                <td>{{ $res->requester_type_label }}</td>
                <td>{{ $res->originCampus->name ?? 'N/A' }}</td>
                <td>{{ $res->purpose_label }}</td>
                <td>{{ $res->destination_label }}</td>
                <td>{{ $res->trip_date->format('M d, Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($res->pickup_time)->format('g:i A') }}</td>
                <td><span class="status-badge status-{{ $res->status }}">{{ strtoupper($res->status) }}</span></td>
                <td>
                    <a href="{{ route('admin.vehicle-reservations.show', $res->id) }}" class="btn-action btn-view">View Details</a>
                    @if($res->status === 'approved')
                        <a href="{{ route('report.vehicle.single', $res->id) }}" class="btn-action" style="color:#2db84f;" target="_blank">Generate Report</a>
                    @endif
                    <form method="POST" action="{{ route('admin.vehicle-reservations.destroy', $res->id) }}" style="display: inline;" onsubmit="return confirm('Delete this reservation?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 40px;">No pickup vehicle reservations found.</td>
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
        <div>{{ $reservations->appends(request()->query())->links() }}</div>
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