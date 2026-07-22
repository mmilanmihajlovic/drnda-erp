<?php
namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreVehicleRequest;
use App\Http\Requests\Settings\UpdateVehicleRequest;
use App\Models\Vehicle;
class VehicleController extends Controller {
    public function index() { return view('settings.vehicles.index', ['vehicles' => Vehicle::latest()->paginate(20)]); }
    public function create() { return view('settings.vehicles.create'); }
    public function store(StoreVehicleRequest $request) { Vehicle::create($request->validated()); return redirect()->route('settings.vehicles.index')->with('success', 'Vozilo je uspesno dodato.'); }
    public function edit(Vehicle $vehicle) { return view('settings.vehicles.edit', compact('vehicle')); }
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle) { $vehicle->update($request->validated()); return redirect()->route('settings.vehicles.index')->with('success', 'Vozilo je uspesno izmenjeno.'); }
    public function destroy(Vehicle $vehicle) { $vehicle->delete(); return redirect()->route('settings.vehicles.index')->with('success', 'Vozilo je obrisano.'); }
}
