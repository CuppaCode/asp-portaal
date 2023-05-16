<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDriverRequest;
use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Models\Driver;
use App\Models\Team;
use App\Models\Vehicle;
use App\Models\VehicleOpposite;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DriverController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('driver_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::with(['vehicle', 'vehicle_opposite', 'team'])->get();

        $vehicles = Vehicle::get();

        $vehicle_opposites = VehicleOpposite::get();

        $teams = Team::get();

        return view('admin.drivers.index', compact('drivers', 'teams', 'vehicle_opposites', 'vehicles'));
    }

    public function create()
    {
        abort_if(Gate::denies('driver_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vehicles = Vehicle::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vehicle_opposites = VehicleOpposite::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.drivers.create', compact('vehicle_opposites', 'vehicles'));
    }

    public function store(StoreDriverRequest $request)
    {
        $driver = Driver::create($request->all());

        return redirect()->route('admin.drivers.index');
    }

    public function edit(Driver $driver)
    {
        abort_if(Gate::denies('driver_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vehicles = Vehicle::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vehicle_opposites = VehicleOpposite::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $driver->load('vehicle', 'vehicle_opposite', 'team');

        return view('admin.drivers.edit', compact('driver', 'vehicle_opposites', 'vehicles'));
    }

    public function update(UpdateDriverRequest $request, Driver $driver)
    {
        $driver->update($request->all());

        return redirect()->route('admin.drivers.index');
    }

    public function show(Driver $driver)
    {
        abort_if(Gate::denies('driver_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $driver->load('vehicle', 'vehicle_opposite', 'team');

        return view('admin.drivers.show', compact('driver'));
    }

    public function destroy(Driver $driver)
    {
        abort_if(Gate::denies('driver_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $driver->delete();

        return back();
    }

    public function massDestroy(MassDestroyDriverRequest $request)
    {
        $drivers = Driver::find(request('ids'));

        foreach ($drivers as $driver) {
            $driver->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
