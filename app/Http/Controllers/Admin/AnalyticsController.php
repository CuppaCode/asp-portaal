<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Claim;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $dn = carbon::now()->format('d-m-Y');

        return view('analytics', compact('companies', 'dn'));
    }

    public function getData(Request $request)
    {
        $data = [
            'company' => $request->company,
            'startdate' => $request->startdate,
            'enddate' => $request->enddate,
        ];

        $from = Carbon::parse($request->startdate)->format('Y-m-d');
        $to = Carbon::parse($request->enddate)->format('Y-m-d');

        $claim = Claim::whereBetween('date_accident', [$from, $to])->Where('company_id', $request->company)->get();
        

        // dd($claim);

        return response()->json(
            [
                'claim' => $claim,
                'type' => 'alert-success',
                'message' => 'Status is succesvol aangepast!'
            ], 200);  
    }

}
