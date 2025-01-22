<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Claim;
use Gate;

class SuperAdminController extends Controller
{
    private $noClaimsWithCriteria = 'Geen claims gevonden die voldoen aan criteria...';

    public function index()
    {  
        $claimStatusses = Claim::STATUS_SELECT;
        $claimDamagedParts = Claim::DAMAGED_PART_SELECT;
        $claimOppositeTypes = Claim::OPPOSITE_TYPE_SELECT;
        $claims = Claim::get();

        return view('super-admin', compact('claimStatusses', 'claimDamagedParts', 'claimOppositeTypes', 'claims'));
    }

    public function migrateStatus(Request $request)
    {
        $claims = Claim::
            whereIn('id', $request->input('migrateStatusClaimsSA'))
            ->where('status', $request->input('old_status'))
            ->get();

        if(empty($claims)){
        
            return back()->with('noStatusClaimsSA', $this->noClaimsWithCriteria);

        }
        
        foreach($claims as $claim){

            $claim->status = $request->input('new_status');
            $claim->save();

        }

        return back()->with('migrateStatusClaims', $claims);
    }

    public function migrateDamagedPart(Request $request)
    {
        $claims = Claim::
            whereIn('id', $request->input('migrateDamagedPartClaimsSA'))
            ->where('damaged_part', 'LIKE', '%'.$request->input('old_damaged_part').'%')
            ->get();

        if(empty($claims)){
        
            return back()->with('noDamagedPartClaimsSA', $this->noClaimsWithCriteria);

        }
        

        foreach($claims as $claim){

            $damagedParts = array_values(json_decode($claim->damaged_part, true));


            foreach($damagedParts as $key => $item){

                if( $item == $request->input('old_damaged_part')){

                    unset($damagedParts[$key]);

                }

            }

            $damagedParts[] = $request->input('new_damaged_part');

            $claim->damaged_part = json_encode($damagedParts);

            $claim->save();
            
        }

        return back()->with('migrateDamagedPartClaims', $claims);
    }

    public function migrateOppositeType(Request $request)
    {
        $claims = Claim::
            whereIn('id', $request->input('migrateOppositeTypeClaimsSA'))
            ->where('opposite_type', $request->input('old_opposite_type'))
            ->get();

        if(empty($claims)){
            
            return back()->with('noOppositeTypeClaimsSA', $this->noClaimsWithCriteria);

        }

        foreach($claims as $claim){

            $claim->opposite_type = $request->input('new_opposite_type');

            $claim->save();

        }

        return back()->with('migrateOppositeTypeClaims', $claims);
    }
}
