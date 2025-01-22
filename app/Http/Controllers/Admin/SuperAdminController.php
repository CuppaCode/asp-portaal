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
        if(!auth()->user()->isSuperAdmin){
            return back();
        }
        
        $claimStatusses = Claim::STATUS_SELECT;
        $claimOppositeTypes = Claim::OPPOSITE_TYPE_SELECT;
        $claimDamagedParts = Claim::DAMAGED_PART_SELECT;
        $claimDamagedPartsOpposite = Claim::DAMAGED_PART_OPPOSITE_SELECT;
        $claimDamageOrigin = Claim::DAMAGE_ORIGIN;
        $claimDamageOriginOpposite = Claim::DAMAGE_ORIGIN_OPPOSITE;
        $claimDamagedArea = Claim::DAMAGED_AREA_SELECT;
        $claimDamagedAreaOpposite = Claim::DAMAGED_AREA_OPPOSITE_SELECT;
        $claims = Claim::get();

        return view('super-admin', compact('claimStatusses', 'claimOppositeTypes', 'claimDamagedParts', 'claimDamagedPartsOpposite', 'claimDamageOrigin', 'claimDamageOriginOpposite', 'claimDamagedArea', 'claimDamagedAreaOpposite', 'claims'));
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

    public function migrateDamagedPartOpposite(Request $request)
    {
        $claims = Claim::
            whereIn('id', $request->input('migrateDamagedPartOppositeClaimsSA'))
            ->where('damaged_part_opposite', 'LIKE', '%'.$request->input('old_damaged_part_opposite').'%')
            ->get();

        if(empty($claims)){
        
            return back()->with('noDamagedPartOppositeClaimsSA', $this->noClaimsWithCriteria);

        }
        

        foreach($claims as $claim){

            $damagedPartsOpposite = array_values(json_decode($claim->damaged_part_opposite, true));


            foreach($damagedPartsOpposite as $key => $item){

                if( $item == $request->input('old_damaged_part_opposite')){

                    unset($damagedPartsOpposite[$key]);

                }

            }

            $damagedPartsOpposite[] = $request->input('new_damaged_part');

            $claim->damaged_part_opposite = json_encode($damagedPartsOpposite);

            $claim->save();
            
        }

        return back()->with('migrateDamagedPartOppositeClaims', $claims);
    }

    public function migrateDamageOrigin(Request $request)
    {
        $claims = Claim::
            whereIn('id', $request->input('migrateDamageOriginClaimsSA'))
            ->where('damage_origin', 'LIKE', '%'.$request->input('old_damage_origin').'%')
            ->get();

        if(empty($claims)){
        
            return back()->with('noDamageOriginClaimsSA', $this->noClaimsWithCriteria);

        }
        

        foreach($claims as $claim){

            $damageOrigin = array_values(json_decode($claim->damage_origin, true));


            foreach($damageOrigin as $key => $item){

                if( $item == $request->input('old_damage_opposite')){

                    unset($damageOrigin[$key]);

                }

            }

            $damageOrigin[] = $request->input('new_damage_opposite');

            $claim->damage_opposite = json_encode($damageOrigin);

            $claim->save();
            
        }

        return back()->with('migrateDamageOriginClaims', $claims);
    }

    public function migrateDamageOriginOpposite(Request $request)
    {
        $claims = Claim::
            whereIn('id', $request->input('migrateDamageOriginOppositeClaimsSA'))
            ->where('damage_origin_opposite', 'LIKE', '%'.$request->input('old_damage_origin_opposite').'%')
            ->get();

        if(empty($claims)){
        
            return back()->with('noDamageOriginOppositeClaimsSA', $this->noClaimsWithCriteria);

        }
        

        foreach($claims as $claim){

            $damageOriginOpposite = array_values(json_decode($claim->damage_origin_oppisite, true));


            foreach($damageOriginOpposite as $key => $item){

                if( $item == $request->input('old_damage_opposite')){

                    unset($damageOriginOpposite[$key]);

                }

            }

            $damageOriginOpposite[] = $request->input('new_damage_opposite');

            $claim->damage_opposite = json_encode($damageOriginOpposite);

            $claim->save();
            
        }

        return back()->with('migrateDamageOriginOppositeClaims', $claims);
    }

    public function migrateDamagedArea(Request $request)
    {
        $claims = Claim::
            whereIn('id', $request->input('migrateDamagedAreaClaimsSA'))
            ->where('damaged_area', 'LIKE', '%'.$request->input('old_damaged_area').'%')
            ->get();

        if(empty($claims)){
        
            return back()->with('noDamagedAreaClaimsSA', $this->noClaimsWithCriteria);

        }
        

        foreach($claims as $claim){

            $damagedArea = array_values(json_decode($claim->damaged_area, true));


            foreach($damagedArea as $key => $item){

                if( $item == $request->input('old_damaged_area')){

                    unset($damagedArea[$key]);

                }

            }

            $damagedArea[] = $request->input('new_damaged_area');

            $claim->damaged_area = json_encode($damagedArea);

            $claim->save();
            
        }

        return back()->with('migrateDamagedAreaClaims', $claims);
    }

    public function migrateDamagedAreaOpposite(Request $request)
    {
        $claims = Claim::
            whereIn('id', $request->input('migrateDamagedAreaOppositeClaimsSA'))
            ->where('damaged_area_opposite', 'LIKE', '%'.$request->input('old_damaged_area_opposite').'%')
            ->get();

        if(empty($claims)){
        
            return back()->with('noDamagedAreaOppositeClaimsSA', $this->noClaimsWithCriteria);

        }
        

        foreach($claims as $claim){

            $damagedAreaOpposite = array_values(json_decode($claim->damaged_area_opposite, true));


            foreach($damagedAreaOpposite as $key => $item){

                if( $item == $request->input('old_damaged_area_opposite')){

                    unset($damagedAreaOpposite[$key]);

                }

            }

            $damagedAreaOpposite[] = $request->input('new_damaged_area_opposite');

            $claim->damaged_area_opposite = json_encode($damagedAreaOpposite);

            $claim->save();
            
        }

        return back()->with('migrateDamagedAreaOppositeClaims', $claims);
    }
}
