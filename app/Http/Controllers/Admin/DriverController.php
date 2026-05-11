<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDriverRequest;
use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Driver;
use App\Models\Team;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DriverController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('driver_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::with(['team'])->get();

        $teams = Team::get();

        return view('admin.drivers.index', compact('drivers', 'teams'));
    }

    public function create()
    {
        abort_if(Gate::denies('driver_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.drivers.create');
    }

    public function store(StoreDriverRequest $request)
    {
        $driver = Driver::create($request->all());

        return redirect()->route('admin.drivers.index');
    }

    public function edit(Driver $driver)
    {
        abort_if(Gate::denies('driver_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $driver->load('team');

        return view('admin.drivers.edit', compact('driver'));
    }

    public function update(UpdateDriverRequest $request, Driver $driver)
    {
        $driver->update($request->all());

        return redirect()->route('admin.drivers.index');
    }

    public function show(Driver $driver)
    {
        abort_if(Gate::denies('driver_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $driver->load('team', 'driverVehicles', 'driverVehicleOpposites');

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

    public function quickStore(Request $request)
    {
        abort_if(Gate::denies('driver_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'name'       => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
        ]);

        $nameParts = explode(' ', trim($request->name), 2);
        $firstName = $nameParts[0];
        $lastName  = $nameParts[1] ?? '';
        $company   = Company::find($request->company_id);
        $teamId    = $company->team_id ?? auth()->user()->team_id;

        $contact = Contact::create([
            'first_name'  => $firstName,
            'last_name'   => $lastName,
            'email'       => $request->input('email', ''),
            'company_id'  => $request->company_id,
            'team_id'     => $teamId,
            'is_driver'   => true,
            'create_user' => 0,
        ]);

        $driver = Driver::create([
            'contact_id' => $contact->id,
            'company_id' => $request->company_id,
            'team_id'    => $teamId,
        ]);

        return response()->json([
            'driver_id' => $driver->id,
            'message'   => 'Chauffeur ' . trim($request->name) . ' is succesvol aangemaakt!',
            'type'      => 'alert-success',
        ]);
    }
}
