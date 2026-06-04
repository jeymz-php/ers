<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

class SettingsController extends Controller
{
    public function index()
    {
        // Make sure only admin/super admin can access
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard');
        }

        $systemStatus = 'up';
        $maintenanceMessage = '';

        try {
            if (Schema::hasTable((new SystemSetting)->getTable())) {
                $systemStatus = SystemSetting::getValue('system_status', 'up');
                $maintenanceMessage = SystemSetting::getValue('maintenance_message', '');
            }
        } catch (QueryException $e) {
            Log::warning('Settings index skipped SystemSetting lookup: ' . $e->getMessage());
        }

        return view('admin.settings.index', [
            'systemStatus' => $systemStatus,
            'maintenanceMessage' => $maintenanceMessage,
        ]);
    }

    public function updateSystemStatus(Request $request)
    {
        $request->validate([
            'system_status' => 'required|in:up,down',
            'maintenance_message' => 'nullable|string',
        ]);

        try {
            if (!Schema::hasTable((new SystemSetting)->getTable())) {
                return back()->with('error', 'System settings storage is not available. Please run database migrations.');
            }

            SystemSetting::setValue('system_status', $request->system_status);
            SystemSetting::setValue('maintenance_message', $request->maintenance_message ?? '');

            return back()->with('systemSuccess', 'System status updated successfully.');
        } catch (QueryException $e) {
            Log::error('System status update failed: ' . $e->getMessage());
            return back()->with('error', 'System status update failed. Please check database configuration.');
        }
    }
    
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }
        
        $user->password = Hash::make($request->new_password);
        $user->is_password_generated = false;
        $user->save();
        
        return back()->with('success', 'Password changed successfully!');
    }
    
    public function backup()
    {
        try {
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            
            $filename = 'ucc_ers_backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = storage_path('app/backups/' . $filename);
            
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0777, true);
            }
            
            // For Windows XAMPP
            $mysqldump = '"C:\\xampp\\mysql\\bin\\mysqldump"';
            $command = sprintf(
                '%s --user=%s --password=%s --host=%s %s > "%s"',
                $mysqldump,
                $username,
                $password,
                $host,
                $database,
                $backupPath
            );
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($backupPath)) {
                return response()->download($backupPath)->deleteFileAfterSend(true);
            } else {
                return back()->with('error', 'Backup failed. Please check database configuration.');
            }
        } catch (\Exception $e) {
            Log::error('Backup error: ' . $e->getMessage());
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }
    
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql|max:51200'
        ]);
        
        try {
            $file = $request->file('backup_file');
            $sql = file_get_contents($file->getRealPath());
            
            DB::unprepared($sql);
            
            return response()->json(['success' => true, 'message' => 'Database restored successfully!']);
        } catch (\Exception $e) {
            Log::error('Restore error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function getBackupList()
    {
        $backups = [];
        $backupDir = storage_path('app/backups');
        
        if (file_exists($backupDir)) {
            $files = scandir($backupDir);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $backups[] = [
                        'name' => $file,
                        'size' => filesize($backupDir . '/' . $file),
                        'date' => date('Y-m-d H:i:s', filemtime($backupDir . '/' . $file))
                    ];
                }
            }
            usort($backups, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }
        
        return response()->json(['backups' => $backups]);
    }
    
    public function downloadBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        if (file_exists($path)) {
            return response()->download($path);
        }
        return back()->with('error', 'Backup file not found.');
    }
    
    public function deleteBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        if (file_exists($path)) {
            unlink($path);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
}