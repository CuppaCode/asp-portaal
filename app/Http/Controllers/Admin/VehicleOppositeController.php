<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyVehicleOppositeRequest;
use App\Http\Requests\StoreVehicleOppositeRequest;
use App\Http\Requests\UpdateVehicleOppositeRequest;
use App\Models\Driver;
use App\Models\Team;
use App\Models\VehicleOpposite;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VehicleOppositeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('vehicle_opposite_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vehicleOpposites = VehicleOpposite::with(['drivers', 'team'])->get();

        $drivers = Driver::get();

        $teams = Team::get();

        return view('admin.vehicleOpposites.index', compact('drivers', 'teams', 'vehicleOpposites'));
    }

    public function create()
    {
        abort_if(Gate::denies('vehicle_opposite_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('last_name', 'id');

        return view('admin.vehicleOpposites.create', compact('drivers'));
    }

    public function store(StoreVehicleOppositeRequest $request)
    {
        $vehicleOpposite = VehicleOpposite::create($request->all());
        $vehicleOpposite->drivers()->sync($request->input('drivers', []));

        return redirect()->route('admin.vehicle-opposites.index');
    }

    public function edit(VehicleOpposite $vehicleOpposite)
    {
        abort_if(Gate::denies('vehicle_opposite_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('last_name', 'id');

        $vehicleOpposite->load('drivers', 'team');

        return view('admin.vehicleOpposites.edit', compact('drivers', 'vehicleOpposite'));
    }

    public function update(UpdateVehicleOppositeRequest $request, VehicleOpposite $vehicleOpposite)
    {
        $vehicleOpposite->update($request->all());
        $vehicleOpposite->drivers()->sync($request->input('drivers', []));

        return redirect()->route('admin.vehicle-opposites.index');
    }

    public function show(VehicleOpposite $vehicleOpposite)
    {
        abort_if(Gate::denies('vehicle_opposite_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vehicleOpposite->load('drivers', 'team', 'vehicleOppositeClaims');

        return view('admin.vehicleOpposites.show', compact('vehicleOpposite'));
    }

    public function destroy(VehicleOpposite $vehicleOpposite)
    {
        abort_if(Gate::denies('vehicle_opposite_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vehicleOpposite->delete();

        return back();
    }

    public function massDestroy(MassDestroyVehicleOppositeRequest $request)
    {
        $vehicleOpposites = VehicleOpposite::find(request('ids'));

        foreach ($vehicleOpposites as $vehicleOpposite) {
            $vehicleOpposite->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
