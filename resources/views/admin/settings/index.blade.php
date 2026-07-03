@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'System Settings')

@section('content')
<style>
    .settings-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .settings-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    
    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a7a3e;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e8eee9;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #1a7a3e;
        font-size: 13px;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        font-size: 14px;
        resize: vertical;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        width: 100%;
    }
    
    .btn-secondary {
        background: #2db84f;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        margin-right: 10px;
    }
    
    .alert {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .alert-success {
        background: #d4f5df;
        color: #1a7a3e;
    }
    
    .alert-error {
        background: #fef2f2;
        color: #dc2626;
    }
    
    .backup-list {
        margin-top: 20px;
    }
    
    .backup-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        background: #f7faf8;
        border-radius: 8px;
        margin-bottom: 8px;
    }
    
    .backup-info {
        font-size: 13px;
    }
    
    .backup-name {
        font-weight: 600;
        color: #1a7a3e;
    }
    
    .backup-date {
        font-size: 11px;
        color: #6e7f72;
    }
    
    .backup-size {
        font-size: 11px;
        color: #6e7f72;
    }
    
    .backup-actions {
        display: flex;
        gap: 8px;
    }
    
    .backup-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        font-size: 16px;
    }
    
    .password-field {
        position: relative;
    }
    
    .password-field input {
        padding-right: 40px;
    }
    
    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #6e7f72;
    }

    /* System Updates */
    .version-pill {
        display: inline-block;
        background: #e8eee9;
        color: #1a7a3e;
        font-weight: 700;
        font-size: 12px;
        padding: 4px 12px;
        border-radius: 999px;
        margin-left: 8px;
    }

    .toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f7faf8;
        padding: 12px 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .toggle-row-label {
        font-size: 13px;
        font-weight: 600;
        color: #33413a;
    }

    .toggle-row-sub {
        font-size: 11px;
        color: #6e7f72;
        margin-top: 2px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
        flex-shrink: 0;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .switch-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background-color: #cfd8d2;
        transition: 0.3s;
        border-radius: 999px;
    }

    .switch-slider::before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    .switch input:checked + .switch-slider {
        background-color: #1a7a3e;
    }

    .switch input:checked + .switch-slider::before {
        transform: translateX(22px);
    }

    .update-entry {
        background: #f7faf8;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 10px;
    }

    .update-entry-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .update-entry-version {
        font-weight: 700;
        color: #1a7a3e;
        font-size: 13px;
    }

    .update-entry-date {
        font-size: 11px;
        color: #6e7f72;
    }

    .update-entry ul {
        margin: 0;
        padding-left: 18px;
        font-size: 13px;
        color: #33413a;
        line-height: 1.6;
    }

    .update-entry-delete {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
        color: #dc2626;
    }
    
    @media (max-width: 768px) {
        .settings-container {
            padding: 15px;
        }
    }
</style>

<div class="settings-container">
    <!-- Change Password -->
    <div class="settings-card">
        <div class="card-title">🛠️ System Maintenance Mode</div>

        @if(session('systemSuccess'))
            <div class="alert alert-success">{{ session('systemSuccess') }}</div>
        @endif

        <div class="form-group">
            <label>Current Status</label>
            <input type="text" value="{{ strtoupper($systemStatus) }}" readonly>
        </div>

        <form method="POST" action="{{ route('admin.settings.system-status') }}">
            @csrf
            <div class="form-group">
                <label>Maintenance Message</label>
                <textarea name="maintenance_message" rows="4">{{ old('maintenance_message', $maintenanceMessage) }}</textarea>
            </div>

            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                <button type="submit" name="system_status" value="up" class="btn-secondary">System Up</button>
                <button type="submit" name="system_status" value="down" class="btn-secondary">System Down</button>
            </div>
        </form>

        <small style="color: #6e7f72; display: block; margin-top: 15px;">Admins can still access the app using the hidden admin login via the UCC logo.</small>
    </div>

    <!-- System Updates -->
    <div class="settings-card">
        <div class="card-title">📢 System Updates <span class="version-pill">Current: v{{ \App\Models\SystemUpdate::currentVersion() }}</span></div>

        @if(session('systemUpdateSuccess'))
            <div class="alert alert-success">{{ session('systemUpdateSuccess') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.settings.system-updates.toggle') }}">
            @csrf
            <input type="hidden" name="enabled" value="off">
            <div class="toggle-row">
                <div>
                    <div class="toggle-row-label">Show System Updates to Users</div>
                    <div class="toggle-row-sub">When on, the latest update pops up right after User/Admin login.</div>
                </div>
                <label class="switch">
                    <input type="checkbox" name="enabled" value="on" onchange="this.form.submit()" {{ $systemUpdatesEnabled ? 'checked' : '' }}>
                    <span class="switch-slider"></span>
                </label>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.settings.system-updates.publish') }}">
            @csrf
            <div class="form-group">
                <label>Next Version <span class="version-pill">v{{ $nextVersion }}</span></label>
                <small style="display: block; color: #6e7f72; margin-top: 4px; margin-bottom: 10px;">The version number is generated automatically by the system.</small>
            </div>
            <div class="form-group">
                <label>What's New (one item per line, each becomes a bullet point)</label>
                <textarea name="updates" rows="5" placeholder="Fixed PDF attachment viewing issue&#10;Added dark mode support&#10;Improved chat response time" required>{{ old('updates') }}</textarea>
            </div>
            <button type="submit" class="btn-primary">🚀 Publish Update (v{{ $nextVersion }})</button>
        </form>

        @if($systemUpdates->count())
            <div class="backup-list">
                <div class="card-title" style="font-size: 14px; margin-bottom: 10px;">Update History</div>
                @foreach($systemUpdates as $update)
                    <div class="update-entry">
                        <div class="update-entry-header">
                            <div>
                                <span class="update-entry-version">v{{ $update->version }}</span>
                                <span class="update-entry-date">&middot; {{ $update->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                            <form method="POST" action="{{ route('admin.settings.system-updates.destroy', $update->id) }}" onsubmit="return confirm('Delete this update entry?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="update-entry-delete" title="Delete">🗑️</button>
                            </form>
                        </div>
                        <ul>
                            @foreach($update->updates as $line)
                                <li>{{ $line }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="settings-card">
        <div class="card-title">🔐 Change Password</div>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        
        <form method="POST" action="{{ route('admin.settings.password') }}">
            @csrf
            <div class="form-group">
                <label>Current Password</label>
                <div class="password-field">
                    <input type="password" id="current_password" name="current_password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('current_password')">👁️</button>
                </div>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <div class="password-field">
                    <input type="password" id="new_password" name="new_password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('new_password')">👁️</button>
                </div>
                <small style="font-size: 11px; color: #6e7f72;">Minimum 8 characters</small>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <div class="password-field">
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('new_password_confirmation')">👁️</button>
                </div>
            </div>
            <button type="submit" class="btn-primary">Update Password</button>
        </form>
    </div>
    
    <!-- Backup & Restore -->
    <div class="settings-card">
        <div class="card-title">💾 Backup & Restore</div>
        
        <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
            <a href="{{ route('admin.settings.backup') }}" class="btn-secondary" style="text-decoration: none; display: inline-block;">📥 Download Backup</a>
            <label class="btn-secondary" style="cursor: pointer; display: inline-block; text-align: center;">
                📤 Restore Backup
                <input type="file" id="restoreFile" accept=".sql" style="display: none;">
            </label>
        </div>
        
        <div class="backup-list">
            <div class="card-title" style="font-size: 14px; margin-bottom: 10px;">Recent Backups</div>
            <div id="backupList">Loading...</div>
        </div>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }
    
    function loadBackups() {
        fetch('{{ route("admin.settings.backups.list") }}')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('backupList');
                if (data.backups.length === 0) {
                    container.innerHTML = '<div style="text-align: center; padding: 20px; color: #b0bdb3;">No backups found</div>';
                    return;
                }
                
                let html = '';
                data.backups.forEach(backup => {
                    const sizeKB = (backup.size / 1024).toFixed(2);
                    html += `
                        <div class="backup-item">
                            <div class="backup-info">
                                <div class="backup-name">${backup.name}</div>
                                <div class="backup-date">${backup.date}</div>
                                <div class="backup-size">${sizeKB} KB</div>
                            </div>
                            <div class="backup-actions">
                                <a href="/admin/settings/backup/download/${backup.name}" class="backup-btn" title="Download">📥</a>
                                <button class="backup-btn" onclick="deleteBackup('${backup.name}')" title="Delete">🗑️</button>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            });
    }
    
    function deleteBackup(filename) {
        if (confirm('Delete this backup file?')) {
            fetch(`/admin/settings/backup/delete/${filename}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadBackups();
                }
            });
        }
    }
    
    document.getElementById('restoreFile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('backup_file', file);
        
        if (confirm('Restoring will overwrite current data. Continue?')) {
            fetch('{{ route("admin.settings.restore") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Database restored successfully!');
                    location.reload();
                } else {
                    alert('Restore failed: ' + data.message);
                }
            });
        }
    });
    
    loadBackups();
</script>
@endsection