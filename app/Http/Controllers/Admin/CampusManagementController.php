<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Establishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CampusManagementController extends Controller
{
    public function index()
    {
        $campuses = Campus::orderBy('display_order')->orderBy('name')->get();
        return view('admin.campuses.index', compact('campuses'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:campuses,name',
            'code' => 'required|string|max:20|unique:campuses,code',
            'address' => 'nullable|string|max:255',
            'display_order' => 'integer'
        ]);
        
        $campus = Campus::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'address' => $request->address,
            'is_active' => true,
            'display_order' => $request->display_order ?? 0
        ]);
        
        return response()->json(['success' => true, 'campus' => $campus]);
    }
    
    public function update(Request $request, $id)
    {
        $campus = Campus::findOrFail($id);
        
        $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('campuses')->ignore($campus->id)],
            'code' => ['required', 'string', 'max:20', Rule::unique('campuses')->ignore($campus->id)],
            'address' => 'nullable|string|max:255',
            'display_order' => 'integer'
        ]);
        
        $campus->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'address' => $request->address,
            'display_order' => $request->display_order ?? 0
        ]);
        
        return response()->json(['success' => true, 'campus' => $campus]);
    }
    
    public function toggleStatus($id)
    {
        $campus = Campus::findOrFail($id);
        $campus->is_active = !$campus->is_active;
        $campus->save();
        
        return response()->json(['success' => true, 'is_active' => $campus->is_active]);
    }
    
    public function destroy($id)
    {
        $campus = Campus::findOrFail($id);
        
        // Check if campus has establishments
        if ($campus->establishments()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete campus with existing establishments. Archive or delete establishments first.'], 400);
        }
        
        $campus->delete();
        return response()->json(['success' => true]);
    }
    
    public function getEstablishments($campusId)
    {
        try {
            $campus = Campus::findOrFail($campusId);
            $establishments = Establishment::where('campus_id', $campusId)
                ->orderBy('name')
                ->get();
            
            return response()->json([
                'success' => true,
                'campus' => $campus,
                'establishments' => $establishments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function storeEstablishment(Request $request, $campusId)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|in:Indoor,Outdoor'
        ]);
        
        $establishment = Establishment::create([
            'name' => $request->name,
            'campus_id' => $campusId,
            'capacity' => $request->capacity,
            'type' => $request->type,
            'is_active' => true
        ]);
        
        return response()->json(['success' => true, 'establishment' => $establishment]);
    }
    
    public function updateEstablishment(Request $request, $campusId, $id)
    {
        $establishment = Establishment::where('campus_id', $campusId)->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:200',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|in:Indoor,Outdoor'
        ]);
        
        $establishment->update([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'type' => $request->type
        ]);
        
        return response()->json(['success' => true, 'establishment' => $establishment]);
    }
    
    public function toggleEstablishmentStatus($campusId, $id)
    {
        $establishment = Establishment::where('campus_id', $campusId)->findOrFail($id);
        $establishment->is_active = !$establishment->is_active;
        $establishment->save();
        
        return response()->json(['success' => true, 'is_active' => $establishment->is_active]);
    }
    
    public function destroyEstablishment($campusId, $id)
    {
        $establishment = Establishment::where('campus_id', $campusId)->findOrFail($id);
        $establishment->delete();
        
        return response()->json(['success' => true]);
    }
}