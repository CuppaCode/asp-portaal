<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Claim;
use Gate;

class SuperAdminController extends Controller
{
    public function index()
    {  
        $ClaimStatusses = Claim::STATUS_SELECT;
        $claims = Claim::get();

        return view('super-admin', compact('ClaimStatusses', 'claims'));
    }

    public function migrateStatus(Request $request)
    {
        $claims = Claim::
            whereIn('id', $request->input('claimsSA'))
            ->where('status', $request->input('old_status'))
            ->update(['status' => $request->input('new_status')]);

        return back()->with('claims', $claims);
    }
}
