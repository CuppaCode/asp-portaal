<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSLARequest;
use App\Models\SLA;
use App\Models\Company;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SLAController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('sla_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $SLA = SLA::get();

        // dd($SLA);
        return view('admin.sla.index', compact('SLA'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.sla.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSLARequest $request)
    {
        // dd($request->all());
        $multiSelects = ['analytics_options'];
        $sla = SLA::create($request->except($multiSelects));


        $sla->analytics_options = $request->input('analytics_options') ? json_encode($request->input('analytics_options')) : null;
        $sla->save();

        return redirect()->route('admin.sla.index')->with('message', 'SLA toegevoegd');
    }

    /**
     * Display the specified resource.
     */
    public function show(SLA $sla)
    {
        abort_if(Gate::denies('sla_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // dd($sla);
        $company = Company::where('id', $sla->company)->first();

        return view('admin.sla.show', compact('sla', 'company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SLA $SLA)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SLA $sLA)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SLA $sLA)
    {
        //
    }
}
