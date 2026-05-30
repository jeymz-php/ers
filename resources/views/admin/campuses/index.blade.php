@extends('layouts.admin')

@section('title', 'Campuses Management')
@section('page-title', 'Campuses & Establishments')

@section('content')
<style>
    .campuses-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        height: calc(100vh - 180px);
        overflow: hidden;
    }
    
    .campuses-list, .establishments-list {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
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
        flex-shrink: 0;
    }
    
    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 18px;
        font-weight: 700;
        color: #1a7a3e;
    }
    
    .section-title img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: contain;
    }
    
    .btn-add {
        background: #2db84f;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
    }
    
    /* Search and Filter Bar */
    .search-filter-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
        flex-wrap: wrap;
        flex-shrink: 0;
    }
    
    .search-input {
        flex: 1;
        padding: 8px 12px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        font-size: 13px;
    }
    
    .filter-select {
        padding: 8px 12px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        font-size: 13px;
        background: white;
        cursor: pointer;
    }
    
    /* Scrollable Content */
    .campuses-scroll {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 5px;
        max-height: calc(100vh - 250px);
        min-height: 300px;
    }

    .establishments-scroll {
        flex: 1;
        overflow-y: auto;
        overflow-x: auto;
        padding-right: 5px;
        max-height: calc(100vh - 350px);
        min-height: 300px;
    }

    /* Make sure tables don't break layout */
    .establishment-table {
        width: 100%;
        min-width: 600px;
        border-collapse: collapse;
    }

    /* Ensure proper container heights */
    .establishments-list {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
    }

    #establishmentsContent {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .selected-campus-info {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 15px;
        text-align: center;
        flex-shrink: 0;
    }

    .result-count {
        font-size: 12px;
        color: #6e7f72;
        margin-bottom: 10px;
        padding: 5px 0;
        flex-shrink: 0;
    }

    /* Custom Scrollbar */
    .campuses-scroll::-webkit-scrollbar,
    .establishments-scroll::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .campuses-scroll::-webkit-scrollbar-track,
    .establishments-scroll::-webkit-scrollbar-track {
        background: #f0faf3;
        border-radius: 10px;
    }

    .campuses-scroll::-webkit-scrollbar-thumb,
    .establishments-scroll::-webkit-scrollbar-thumb {
        background: #b0bdb3;
        border-radius: 10px;
    }

    .campuses-scroll::-webkit-scrollbar-thumb:hover,
    .establishments-scroll::-webkit-scrollbar-thumb:hover {
        background: #8a9c8f;
    }
    
    .campus-card {
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid transparent;
        position: relative;
        background-size: cover;
        background-position: center;
        min-height: 120px;
        overflow: hidden;
    }
    
    .campus-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 0;
    }
    
    .campus-card:hover {
        transform: translateX(5px);
    }
    
    .campus-card.selected {
        border-color: #2db84f;
        box-shadow: 0 4px 20px rgba(45, 184, 79, 0.3);
    }
    
    .campus-card.inactive {
        opacity: 0.7;
    }
    
    .campus-content {
        position: relative;
        z-index: 1;
        color: white;
    }
    
    .campus-name {
        font-weight: 700;
        font-size: 18px;
        margin-bottom: 8px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
    
    .campus-code, .campus-address {
        font-size: 12px;
        margin-bottom: 4px;
        opacity: 0.9;
        text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
    }
    
    .campus-actions {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        gap: 5px;
        z-index: 2;
    }
    
    .action-btn {
        background: rgba(255,255,255,0.9);
        border: none;
        padding: 5px 8px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.3s;
    }
    
    .action-btn:hover {
        background: white;
        transform: scale(1.05);
    }
    
    .status-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        margin-top: 8px;
        background: rgba(255,255,255,0.9);
        color: #1a7a3e;
    }
    
    .status-active {
        background: #d4f5df;
        color: #1a7a3e;
    }
    
    .status-inactive {
        background: #e8eee9;
        color: #6e7f72;
    }
    
    .establishment-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .establishment-table th,
    .establishment-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #e8eee9;
    }
    
    .establishment-table th {
        color: #1a7a3e;
        font-size: 12px;
        font-weight: 600;
        background: #f7faf8;
        position: sticky;
        top: 0;
    }
    
    .establishment-table tr:hover {
        background: #f7faf8;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    .modal.active {
        display: flex;
    }
    
    .modal-content {
        background: white;
        border-radius: 20px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-header {
        padding: 20px;
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 20px 20px 0 0;
    }
    
    .modal-header h3 {
        margin: 0;
    }
    
    .close-modal {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #1a7a3e;
        font-size: 13px;
    }
    
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        font-size: 14px;
    }
    
    .btn-submit {
        background: #2db84f;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        width: 100%;
        font-size: 14px;
        font-weight: 600;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #b0bdb3;
    }
    
    .selected-campus-info {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 20px;
        text-align: center;
        flex-shrink: 0;
    }
    
    .selected-campus-name {
        font-size: 18px;
        font-weight: 700;
    }
    
    .result-count {
        font-size: 12px;
        color: #6e7f72;
        margin-bottom: 10px;
        padding: 5px 0;
        flex-shrink: 0;
    }
    
    @media (max-width: 768px) {
        .campuses-container {
            grid-template-columns: 1fr;
            height: auto;
        }
        .campuses-list, .establishments-list {
            height: 400px;
        }
        .establishment-table {
            font-size: 12px;
        }
        .establishment-table th,
        .establishment-table td {
            padding: 8px;
        }
        .search-filter-bar {
            flex-direction: column;
        }
    }
</style>

<div class="campuses-container">
    <!-- Left: Campuses List -->
    <div class="campuses-list">
        <div class="section-header">
            <div class="section-title">
                <img src="{{ asset('images/UCC_Logo.png') }}" alt="UCC Logo">
                UCC Campuses
            </div>
            <button class="btn-add" onclick="openCampusModal()">+ Add Campus</button>
        </div>
        <div class="campuses-scroll" id="campusesScroll">
            @foreach($campuses as $campus)
            @php
                $bgImage = '';
                switch($campus->code) {
                    case 'MC':
                    case 'UCC-SOUTH':
                        $bgImage = asset('images/UCC_South.png');
                        break;
                    case 'CEC':
                    case 'UCC-CONGRESS':
                        $bgImage = asset('images/UCC_Congressional.png');
                        break;
                    case 'CAM':
                    case 'UCC-CAMARIN':
                        $bgImage = asset('images/UCC_Camarin.png');
                        break;
                    case 'BS':
                    case 'UCC-COENGG':
                        $bgImage = asset('images/UCC_BagongSilang.png');
                        break;
                    default:
                        $bgImage = asset('images/UCC_South.png');
                }
            @endphp
            <div class="campus-card {{ !$campus->is_active ? 'inactive' : '' }}" 
                 data-id="{{ $campus->id }}"
                 data-name="{{ strtolower($campus->name) }}"
                 style="background-image: url('{{ $bgImage }}');">
                <div class="campus-content">
                    <div class="campus-name">{{ $campus->name }}</div>
                    <div class="campus-code">Code: {{ $campus->code }}</div>
                    <div class="campus-address">{{ $campus->address ?? 'Caloocan City' }}</div>
                    <span class="status-badge {{ $campus->is_active ? 'status-active' : 'status-inactive' }}">
                        {{ $campus->is_active ? 'ACTIVE' : 'INACTIVE' }}
                    </span>
                </div>
                <div class="campus-actions">
                    <button class="action-btn" onclick="event.stopPropagation(); editCampus({{ $campus->id }})">✏️</button>
                    <button class="action-btn" onclick="event.stopPropagation(); toggleCampusStatus({{ $campus->id }})">
                        {{ $campus->is_active ? '🔴' : '🟢' }}
                    </button>
                    <button class="action-btn" onclick="event.stopPropagation(); deleteCampus({{ $campus->id }})">🗑️</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Right: Establishments List -->
    <div class="establishments-list">
        <div class="section-header">
            <div class="section-title">📍 Establishments</div>
            <button class="btn-add" id="addEstablishmentBtn" onclick="openEstablishmentModal()" disabled>+ Add Establishment</button>
        </div>
        
        <!-- Search and Filter Bar -->
        <div class="search-filter-bar" id="searchFilterBar" style="display: none;">
            <input type="text" id="searchEstablishment" class="search-input" placeholder="🔍 Search by name...">
            <select id="filterType" class="filter-select">
                <option value="all">All Types</option>
                <option value="Indoor">Indoor</option>
                <option value="Outdoor">Outdoor</option>
            </select>
            <select id="filterStatus" class="filter-select">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        
        <div id="establishmentsContent">
            <div class="empty-state">🏛️ Select a campus to view establishments</div>
        </div>
    </div>
</div>

<!-- Campus Modal -->
<div id="campusModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="campusModalTitle">Add Campus</h3>
            <button class="close-modal" onclick="closeCampusModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="campusForm">
                <input type="hidden" id="campus_id">
                <div class="form-group">
                    <label>Campus Name</label>
                    <input type="text" id="campus_name" required>
                </div>
                <div class="form-group">
                    <label>Campus Code</label>
                    <input type="text" id="campus_code" placeholder="e.g., MC" required>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" id="campus_address" placeholder="e.g., Caloocan City">
                </div>
                <div class="form-group">
                    <label>Display Order</label>
                    <input type="number" id="campus_display_order" value="0">
                </div>
                <button type="submit" class="btn-submit">Save Campus</button>
            </form>
        </div>
    </div>
</div>

<!-- Establishment Modal -->
<div id="establishmentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="establishmentModalTitle">Add Establishment</h3>
            <button class="close-modal" onclick="closeEstablishmentModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="establishmentForm">
                <input type="hidden" id="establishment_id">
                <div class="form-group">
                    <label>Establishment Name</label>
                    <input type="text" id="establishment_name" required>
                </div>
                <div class="form-group">
                    <label>Capacity</label>
                    <input type="number" id="establishment_capacity" required min="1">
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select id="establishment_type" required>
                        <option value="Indoor">Indoor</option>
                        <option value="Outdoor">Outdoor</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Save Establishment</button>
            </form>
        </div>
    </div>
</div>

<script>
    let currentCampusId = null;
    let allEstablishments = [];
    
    function selectCampus(id) {
        currentCampusId = id;
        console.log('Selected campus ID:', id);
        
        // Update UI
        document.querySelectorAll('.campus-card').forEach(card => {
            card.classList.remove('selected');
        });
        const selectedCard = document.querySelector(`.campus-card[data-id="${id}"]`);
        if (selectedCard) {
            selectedCard.classList.add('selected');
        }
        
        // Enable add establishment button and show search bar
        const addBtn = document.getElementById('addEstablishmentBtn');
        if (addBtn) {
            addBtn.disabled = false;
        }
        
        // Show search/filter bar
        document.getElementById('searchFilterBar').style.display = 'flex';
        
        // Load establishments
        loadEstablishments(id);
    }
    
    function loadEstablishments(campusId) {
        const container = document.getElementById('establishmentsContent');
        container.innerHTML = '<div class="empty-state">⏳ Loading establishments...</div>';
        
        const url = `/admin/campuses/${campusId}/establishments`;
        console.log('Fetching establishments from:', url);
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Establishments data:', data);
            if (data.success) {
                allEstablishments = data.establishments;
                renderEstablishments(allEstablishments, data.campus);
                
                // Add event listeners for search and filter
                setupSearchAndFilter();
            } else {
                container.innerHTML = '<div class="empty-state">❌ Error loading establishments: ' + (data.message || 'Unknown error') + '</div>';
                document.getElementById('searchFilterBar').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            container.innerHTML = '<div class="empty-state">❌ Error loading establishments. Please check the console for details.</div>';
            document.getElementById('searchFilterBar').style.display = 'none';
        });
    }
    
    function renderEstablishments(establishments, campus) {
        const container = document.getElementById('establishmentsContent');
        
        // Create selected campus info header
        let html = `
            <div class="selected-campus-info">
                <div class="selected-campus-name">🏛️ ${campus.name}</div>
                <div style="font-size: 12px; margin-top: 5px;">${campus.address || 'Caloocan City'}</div>
            </div>
        `;
        
        if (!establishments || establishments.length === 0) {
            html += '<div class="empty-state">📌 No establishments found. Click "Add Establishment" to create one.</div>';
            container.innerHTML = html;
            return;
        }
        
        html += `
            <div class="result-count">📊 Showing ${establishments.length} establishment(s)</div>
            <div class="establishments-scroll">
                <table class="establishment-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Capacity</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        establishments.forEach(est => {
            html += `
                <tr>
                    <td><strong>${escapeHtml(est.name)}</strong></td>
                    <td>${est.capacity.toLocaleString()} persons</td>
                    <td><span style="background: #e8eee9; padding: 2px 8px; border-radius: 12px; font-size: 11px;">${est.type}</span></td>
                    <td>
                        <span class="status-badge ${est.is_active ? 'status-active' : 'status-inactive'}">
                            ${est.is_active ? 'ACTIVE' : 'INACTIVE'}
                        </span>
                    </td>
                    <td>
                        <button class="action-btn" onclick="editEstablishment(${est.id})" title="Edit">✏️</button>
                        <button class="action-btn" onclick="toggleEstablishmentStatus(${est.id})" title="Toggle Status">
                            ${est.is_active ? '🔴' : '🟢'}
                        </button>
                        <button class="action-btn" onclick="deleteEstablishment(${est.id})" title="Delete">🗑️</button>
                    </td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        container.innerHTML = html;
    }
    
    function setupSearchAndFilter() {
        const searchInput = document.getElementById('searchEstablishment');
        const typeFilter = document.getElementById('filterType');
        const statusFilter = document.getElementById('filterStatus');
        
        const filterFunction = () => {
            const searchTerm = searchInput.value.toLowerCase();
            const typeValue = typeFilter.value;
            const statusValue = statusFilter.value;
            
            const filtered = allEstablishments.filter(est => {
                const matchesSearch = est.name.toLowerCase().includes(searchTerm);
                const matchesType = typeValue === 'all' || est.type === typeValue;
                const matchesStatus = statusValue === 'all' || 
                    (statusValue === 'active' && est.is_active) ||
                    (statusValue === 'inactive' && !est.is_active);
                
                return matchesSearch && matchesType && matchesStatus;
            });
            
            // Update the result count and display
            const campusName = document.querySelector('.selected-campus-name')?.innerText.replace('🏛️ ', '') || '';
            const dummyCampus = { name: campusName, address: '' };
            renderEstablishments(filtered, dummyCampus);
        };
        
        searchInput.addEventListener('input', filterFunction);
        typeFilter.addEventListener('change', filterFunction);
        statusFilter.addEventListener('change', filterFunction);
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Campus CRUD (same as before)
    function openCampusModal(id = null) {
        document.getElementById('campusModal').classList.add('active');
        if (id) {
            document.getElementById('campusModalTitle').innerText = 'Edit Campus';
            const card = document.querySelector(`.campus-card[data-id="${id}"]`);
            const name = card.querySelector('.campus-name').innerText;
            const codeText = card.querySelector('.campus-code').innerText;
            const addressText = card.querySelector('.campus-address')?.innerText || '';
            
            document.getElementById('campus_id').value = id;
            document.getElementById('campus_name').value = name;
            document.getElementById('campus_code').value = codeText.replace('Code: ', '');
            document.getElementById('campus_address').value = addressText;
        } else {
            document.getElementById('campusModalTitle').innerText = 'Add Campus';
            document.getElementById('campusForm').reset();
            document.getElementById('campus_id').value = '';
        }
    }
    
    function closeCampusModal() {
        document.getElementById('campusModal').classList.remove('active');
    }
    
    document.getElementById('campusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('campus_id').value;
        const url = id ? `/admin/campuses/${id}` : '/admin/campuses';
        const method = id ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                name: document.getElementById('campus_name').value,
                code: document.getElementById('campus_code').value,
                address: document.getElementById('campus_address').value,
                display_order: document.getElementById('campus_display_order').value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error saving campus');
            }
        });
    });
    
    function editCampus(id) {
        openCampusModal(id);
    }
    
    function toggleCampusStatus(id) {
        if (confirm('Toggle campus status?')) {
            fetch(`/admin/campuses/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }
    
    function deleteCampus(id) {
        if (confirm('Are you sure? This will delete the campus if no establishments exist.')) {
            fetch(`/admin/campuses/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    }
    
    // Establishment CRUD
    function openEstablishmentModal(id = null) {
        if (!currentCampusId) {
            alert('Please select a campus first');
            return;
        }
        document.getElementById('establishmentModal').classList.add('active');
        if (id) {
            document.getElementById('establishmentModalTitle').innerText = 'Edit Establishment';
            fetch(`/admin/campuses/${currentCampusId}/establishments/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('establishment_id').value = data.establishment.id;
                        document.getElementById('establishment_name').value = data.establishment.name;
                        document.getElementById('establishment_capacity').value = data.establishment.capacity;
                        document.getElementById('establishment_type').value = data.establishment.type;
                    }
                });
        } else {
            document.getElementById('establishmentModalTitle').innerText = 'Add Establishment';
            document.getElementById('establishmentForm').reset();
            document.getElementById('establishment_id').value = '';
        }
    }
    
    function closeEstablishmentModal() {
        document.getElementById('establishmentModal').classList.remove('active');
    }
    
    document.getElementById('establishmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('establishment_id').value;
        const url = id ? `/admin/campuses/${currentCampusId}/establishments/${id}` : `/admin/campuses/${currentCampusId}/establishments`;
        const method = id ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                name: document.getElementById('establishment_name').value,
                capacity: document.getElementById('establishment_capacity').value,
                type: document.getElementById('establishment_type').value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeEstablishmentModal();
                loadEstablishments(currentCampusId);
            }
        });
    });
    
    function editEstablishment(id) {
        openEstablishmentModal(id);
    }
    
    function toggleEstablishmentStatus(id) {
        if (confirm('Toggle establishment status?')) {
            fetch(`/admin/campuses/${currentCampusId}/establishments/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadEstablishments(currentCampusId);
                }
            });
        }
    }
    
    function deleteEstablishment(id) {
        if (confirm('Delete this establishment?')) {
            fetch(`/admin/campuses/${currentCampusId}/establishments/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadEstablishments(currentCampusId);
                }
            });
        }
    }
    
    // Make campus cards clickable
    document.querySelectorAll('.campus-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.classList.contains('action-btn') || e.target.closest('.action-btn')) {
                return;
            }
            const id = this.getAttribute('data-id');
            if (id) {
                selectCampus(parseInt(id));
            }
        });
    });
    
    // Auto-select first campus on page load
    document.addEventListener('DOMContentLoaded', function() {
        const firstCard = document.querySelector('.campus-card');
        if (firstCard && !currentCampusId) {
            const id = firstCard.getAttribute('data-id');
            if (id) {
                setTimeout(() => {
                    selectCampus(parseInt(id));
                }, 500);
            }
        }
    });
</script>
@endsection