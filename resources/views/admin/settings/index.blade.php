@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'System Settings')

@section('content')
<style>
    .settings-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }
    
    .settings-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
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
    
    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        font-size: 14px;
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
    
    @media (max-width: 768px) {
        .settings-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="settings-container">
    <!-- Change Password -->
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
                <div style="position: relative;">
                    <input type="password" id="current_password" name="current_password" required style="width: 100%; padding: 10px 40px 10px 10px; border: 1px solid #e8eee9; border-radius: 8px;">
                    <button type="button" onclick="togglePassword('current_password')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">👁️</button>
                </div>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <div style="position: relative;">
                    <input type="password" id="new_password" name="new_password" required style="width: 100%; padding: 10px 40px 10px 10px; border: 1px solid #e8eee9; border-radius: 8px;">
                    <button type="button" onclick="togglePassword('new_password')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">👁️</button>
                </div>
                <small style="font-size: 11px; color: #6e7f72;">Minimum 8 characters</small>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <div style="position: relative;">
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" required style="width: 100%; padding: 10px 40px 10px 10px; border: 1px solid #e8eee9; border-radius: 8px;">
                    <button type="button" onclick="togglePassword('new_password_confirmation')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">👁️</button>
                </div>
            </div>
            <button type="submit" class="btn-primary">Update Password</button>
        </form>
    </div>
    
    <!-- Backup & Restore -->
    <div class="settings-card">
        <div class="card-title">💾 Backup & Restore</div>
        
        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <a href="{{ route('admin.settings.backup') }}" class="btn-secondary">Download Backup</a>
            <label class="btn-secondary" style="cursor: pointer; display: inline-block; text-align: center;">
                Restore Backup
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