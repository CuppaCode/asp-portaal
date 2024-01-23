<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $claim_kind_a = Claim::whereBetween('date_accident', [$from, $to])->Where('company_id', $request->company)->where('damage_kind', 'transport')->count();
        $claim_kind_b = Claim::whereBetween('date_accident', [$from, $to])->Where('company_id', $request->company)->where('damage_kind', 'traffic')->count();
        $claim_kind_c = Claim::whereBetween('date_accident', [$from, $to])->Where('company_id', $request->company)->where('damage_kind', 'other')->count();
        
        $damage_costs = DB::table('claims')
        ->Where('company_id', $request->company)
        ->whereBetween('date_accident', [$from, $to])
        ->select(DB::raw('SUM(damage_costs) as damage_costs, monthname(date_accident) as month'))
        ->orderByRaw('FIELD(month, "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December")')
        ->groupBy('month')->get();

        $saved_costs = DB::table('claims')
        ->Where('company_id', $request->company)
        ->whereBetween('date_accident', [$from, $to])
        ->select(DB::raw('SUM(recovery_costs + replacement_vehicle_costs + expert_costs + other_costs + deductible_excess_costs + insurance_costs + invoice_amount) as saved_costs, monthname(date_accident) as month'))
        ->orderByRaw('FIELD(month, "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December")')
        ->groupBy('month')->get();

        // dd($saved_costs); - "replacement_vehicle_costs" - "expert_costs" - "other_costs" - "deductible_excess_costs" - "insurance_costs"

        return response()->json(
            [
                'transport' => $claim_kind_a,
                'traffic' => $claim_kind_b,
                'other' => $claim_kind_c,
                'damage_costs' => $damage_costs,
                'saved_costs' => $saved_costs,
            ], 200);  
    }

    public function invoices()
    {
        $claims = Claim::where('invoice_settlement_asp', 0)->orWhere('invoice_settlement_asp', NULL)->get();

        return view('invoices', compact('claims'));
    }

}
