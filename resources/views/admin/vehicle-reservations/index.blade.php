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
    .btn-icon-delete { color: #dc2626; background: #fef2f2; }
    .btn-icon-edit { color: #f5a524; background: #fff7e6; }
    .btn-icon-toggle { color: #6e7f72; background: #f0faf3; }

    /* Vehicle Fleet Modal */
    .vehicle-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(10, 61, 31, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 3000;
        padding: 20px;
    }

    .vehicle-modal-overlay.active { display: flex; }

    .vehicle-modal {
        background: white;
        border-radius: 20px;
        width: 100%;
        max-width: 780px;
        max-height: 88vh;
        overflow-y: auto;
        padding: 25px;
    }

    .vehicle-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e8eee9;
    }

    .vehicle-modal-header h3 {
        color: #1a7a3e;
        font-size: 18px;
    }

    .vehicle-modal-close {
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: #6e7f72;
    }

    .vehicle-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
        background: #f7faf8;
        padding: 16px;
        border-radius: 14px;
    }

    .vehicle-form-grid label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: #1a7a3e;
        margin-bottom: 5px;
    }

    .vehicle-form-grid input,
    .vehicle-form-grid select {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        font-size: 13px;
    }

    .vehicle-form-actions {
        grid-column: 1 / -1;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .btn-save-vehicle {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border: none;
        padding: 9px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
    }

    .btn-cancel-edit {
        background: #e8eee9;
        color: #3c4a3f;
        border: none;
        padding: 9px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        display: none;
    }

    .vehicle-list-table {
        width: 100%;
        border-collapse: collapse;
    }

    .vehicle-list-table th, .vehicle-list-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #f0faf3;
        font-size: 12px;
    }

    .vehicle-status-pill {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
    }

    .vehicle-status-active { background: #d4f5df; color: #1a7a3e; }
    .vehicle-status-inactive { background: #e8eee9; color: #6e7f72; }

    .add-vehicle-btn {
        background: white;
        color: #1a7a3e;
        border: 2px solid #1a7a3e;
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

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

<div style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
    <button type="button" class="add-vehicle-btn" onclick="openVehicleModal()">🚗 Add Vehicle</button>
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
                <th>Vehicle</th>
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
                <td>{{ $res->vehicle?->label ?? '—' }}</td>
                <td>{{ $res->purpose_label }}</td>
                <td>{{ $res->destination_label }}</td>
                <td>
                    @if(count($res->trip_dates) > 1)
                        <span title="{{ $res->trip_dates_display }}">{{ $res->trip_dates_display }}</span>
                    @else
                        {{ $res->trip_date->format('M d, Y') }}
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($res->pickup_time)->format('g:i A') }}</td>
                <td><span class="status-badge status-{{ $res->status }}">{{ strtoupper($res->status) }}</span></td>
                <td>
                    <div class="action-icons">
                        <a href="{{ route('admin.vehicle-reservations.show', $res->id) }}" class="btn-icon btn-icon-view" title="View Details">👁️</a>
                        <a href="{{ route('admin.vehicle-reservations.edit', $res->id) }}" class="btn-icon btn-icon-edit" title="Edit Reservation">✏️</a>
                        @if($res->status === 'approved')
                            <a href="{{ route('report.vehicle.single', $res->id) }}" class="btn-icon btn-icon-report" title="Generate Report" target="_blank">📄</a>
                        @endif
                        <form method="POST" action="{{ route('admin.vehicle-reservations.destroy', $res->id) }}" style="display: inline;" onsubmit="return confirm('Delete this reservation?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon btn-icon-delete" title="Delete">🗑️</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align: center; padding: 40px;">No pickup vehicle reservations found.</td>
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

<!-- Vehicle Fleet Management Modal -->
<div id="vehicleModalOverlay" class="vehicle-modal-overlay">
    <div class="vehicle-modal">
        <div class="vehicle-modal-header">
            <h3>🚗 Manage Pickup Vehicles</h3>
            <button type="button" class="vehicle-modal-close" onclick="closeVehicleModal()">&times;</button>
        </div>

        <div id="vehicleFormAlert"></div>

        <form id="vehicleForm" onsubmit="return submitVehicleForm(event)">
            <input type="hidden" id="vehicleId" value="">
            <div class="vehicle-form-grid">
                <div>
                    <label>Campus</label>
                    <select id="vehicleCampusId" required>
                        <option value="">Select campus...</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Vehicle Name</label>
                    <input type="text" id="vehicleName" placeholder="e.g. Toyota Hiace" required>
                </div>
                <div>
                    <label>Plate Number</label>
                    <input type="text" id="vehiclePlateNumber" placeholder="e.g. ABC 1234">
                </div>
                <div>
                    <label>Type</label>
                    <input type="text" id="vehicleType" placeholder="e.g. Van, Bus, SUV">
                </div>
                <div>
                    <label>Capacity</label>
                    <input type="number" id="vehicleCapacity" min="1" placeholder="e.g. 12">
                </div>
                <div class="vehicle-form-actions">
                    <button type="button" class="btn-cancel-edit" id="cancelVehicleEdit" onclick="resetVehicleForm()">Cancel Edit</button>
                    <button type="submit" class="btn-save-vehicle" id="saveVehicleBtn">➕ Add Vehicle</button>
                </div>
            </div>
        </form>

        <table class="vehicle-list-table">
            <thead>
                <tr>
                    <th>Vehicle</th>
                    <th>Campus</th>
                    <th>Type</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="vehicleListBody">
                <tr><td colspan="6" style="text-align:center; padding: 20px; color:#b0bdb3;">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    function changePerPage() {
        const perPage = document.getElementById('perPage').value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        window.location.href = url.toString();
    }

    // ===== Vehicle Fleet Management =====
    let vehiclesCache = [];

    function openVehicleModal() {
        document.getElementById('vehicleModalOverlay').classList.add('active');
        loadVehicles();
    }

    function closeVehicleModal() {
        document.getElementById('vehicleModalOverlay').classList.remove('active');
        resetVehicleForm();
    }

    function loadVehicles() {
        fetch('{{ route('admin.vehicles.index') }}')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    vehiclesCache = data.vehicles;
                    renderVehicleList();
                }
            })
            .catch(() => {
                document.getElementById('vehicleListBody').innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px; color:#dc2626;">Failed to load vehicles.</td></tr>';
            });
    }

    function renderVehicleList() {
        const body = document.getElementById('vehicleListBody');

        if (!vehiclesCache.length) {
            body.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px; color:#b0bdb3;">No vehicles added yet.</td></tr>';
            return;
        }

        body.innerHTML = vehiclesCache.map(v => `
            <tr>
                <td>${v.name}${v.plate_number ? ' <span style="color:#6e7f72;">(' + v.plate_number + ')</span>' : ''}</td>
                <td>${v.campus ? v.campus.name : 'N/A'}</td>
                <td>${v.type || '—'}</td>
                <td>${v.capacity || '—'}</td>
                <td><span class="vehicle-status-pill ${v.is_active ? 'vehicle-status-active' : 'vehicle-status-inactive'}">${v.is_active ? 'ACTIVE' : 'INACTIVE'}</span></td>
                <td>
                    <div class="action-icons">
                        <button type="button" class="btn-icon btn-icon-edit" title="Edit" onclick='editVehicle(${JSON.stringify(v)})'>✏️</button>
                        <button type="button" class="btn-icon btn-icon-toggle" title="${v.is_active ? 'Deactivate' : 'Activate'}" onclick="toggleVehicleStatus(${v.id})">${v.is_active ? '⏸️' : '▶️'}</button>
                        <button type="button" class="btn-icon btn-icon-delete" title="Delete" onclick="deleteVehicle(${v.id})">🗑️</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function submitVehicleForm(e) {
        e.preventDefault();

        const id = document.getElementById('vehicleId').value;
        const payload = {
            campus_id: document.getElementById('vehicleCampusId').value,
            name: document.getElementById('vehicleName').value,
            plate_number: document.getElementById('vehiclePlateNumber').value,
            type: document.getElementById('vehicleType').value,
            capacity: document.getElementById('vehicleCapacity').value,
        };

        const url = id ? `/admin/vehicles/${id}` : '{{ route('admin.vehicles.store') }}';

        fetch(url, {
            method: id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload),
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showVehicleAlert('✅ Vehicle saved successfully.', 'success');
                    resetVehicleForm();
                    loadVehicles();
                } else {
                    showVehicleAlert('❌ ' + (data.message || 'Something went wrong.'), 'error');
                }
            })
            .catch(() => showVehicleAlert('❌ Failed to save vehicle.', 'error'));

        return false;
    }

    function editVehicle(v) {
        document.getElementById('vehicleId').value = v.id;
        document.getElementById('vehicleCampusId').value = v.campus_id;
        document.getElementById('vehicleName').value = v.name;
        document.getElementById('vehiclePlateNumber').value = v.plate_number || '';
        document.getElementById('vehicleType').value = v.type || '';
        document.getElementById('vehicleCapacity').value = v.capacity || '';
        document.getElementById('saveVehicleBtn').textContent = '💾 Update Vehicle';
        document.getElementById('cancelVehicleEdit').style.display = 'inline-block';
        document.getElementById('vehicleForm').scrollIntoView({ behavior: 'smooth' });
    }

    function resetVehicleForm() {
        document.getElementById('vehicleForm').reset();
        document.getElementById('vehicleId').value = '';
        document.getElementById('saveVehicleBtn').textContent = '➕ Add Vehicle';
        document.getElementById('cancelVehicleEdit').style.display = 'none';
    }

    function toggleVehicleStatus(id) {
        fetch(`/admin/vehicles/${id}/toggle-status`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadVehicles();
                }
            });
    }

    function deleteVehicle(id) {
        if (!confirm('Delete this vehicle? This cannot be undone.')) {
            return;
        }

        fetch(`/admin/vehicles/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadVehicles();
                } else {
                    showVehicleAlert('❌ ' + (data.message || 'Failed to delete.'), 'error');
                }
            });
    }

    function showVehicleAlert(message, type) {
        const el = document.getElementById('vehicleFormAlert');
        const bg = type === 'success' ? '#d4f5df' : '#fef2f2';
        const color = type === 'success' ? '#1a7a3e' : '#dc2626';
        el.innerHTML = `<div style="background:${bg};color:${color};padding:10px 14px;border-radius:8px;margin-bottom:15px;font-size:13px;">${message}</div>`;
        setTimeout(() => { el.innerHTML = ''; }, 4000);
    }
</script>
@endsection