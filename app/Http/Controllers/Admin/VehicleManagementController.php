<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleManagementController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('campus')->orderBy('campus_id')->orderBy('name')->get();
        $campuses = Campus::where('is_active', true)->orderBy('display_order')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'vehicles' => $vehicles,
            'campuses' => $campuses,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'name' => 'required|string|max:150',
            'plate_number' => 'nullable|string|max:30',
            'type' => 'nullable|string|max:50',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $vehicle = Vehicle::create([
            'campus_id' => $request->campus_id,
            'name' => $request->name,
            'plate_number' => $request->plate_number,
            'type' => $request->type,
            'capacity' => $request->capacity,
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'vehicle' => $vehicle->load('campus')]);
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'name' => 'required|string|max:150',
            'plate_number' => 'nullable|string|max:30',
            'type' => 'nullable|string|max:50',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $vehicle->update([
            'campus_id' => $request->campus_id,
            'name' => $request->name,
            'plate_number' => $request->plate_number,
            'type' => $request->type,
            'capacity' => $request->capacity,
        ]);

        return response()->json(['success' => true, 'vehicle' => $vehicle->load('campus')]);
    }

    public function toggleStatus($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->is_active = !$vehicle->is_active;
        $vehicle->save();

        return response()->json(['success' => true, 'is_active' => $vehicle->is_active]);
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();

        return response()->json(['success' => true]);
    }

    public function getVehiclesByCampus($campusId)
    {
        $vehicles = Vehicle::getByCampus($campusId);

        return response()->json([
            'success' => true,
            'vehicles' => $vehicles,
        ]);
    }
}