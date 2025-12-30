<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Driver;
use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Driver $driver)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.certificate.create', compact('driver'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Driver $driver)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $certificate = Certificate::create(
            [
                'driver_id' => $driver->id,
                'name' => $request->name,
                'notify_date' => $request->notify_date,
                'expiry_date' => $request->expiry_date,
                'team_id' => auth()->user()->team_id
            ]
        );

        // Prefer explicit back URL from the form (safer than relying on Referer header)
        $back = $request->input('back_to');

        // compute urls to avoid redirecting back to the same page (or the create page)
        $currentUrl = url()->current();
        $createUrl = route('admin.certificate.create', $driver->id);

        if ($back && Str::startsWith($back, url('/')) && $back !== $currentUrl && $back !== $createUrl) {
            return redirect($back)->with('success', 'Certificaat succesvol aangemaakt!');
        }

        return redirect()->route('admin.drivers.show', $driver->id)->with('success', 'Certificaat succesvol aangemaakt!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Certificate $certificate)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Certificate $certificate)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Certificate $certificate)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certificate $certificate)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        //
    }
}
