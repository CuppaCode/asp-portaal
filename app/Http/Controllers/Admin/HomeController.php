<?php

namespace App\Http\Controllers\Admin;

use App\Models\Claim;
use App\Models\Company;
use App\Models\Contact;
use App\Models\ExpertiseOffice;
use App\Models\InjuryOffice;
use App\Models\RecoveryOffice;
use App\Models\Team;
use App\Models\Vehicle;
use App\Models\VehicleOpposite;
use App\Models\Task;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

class HomeController
{
    public function index()
    {
        $user = auth()->user();

        $claims = Claim::whereNot('status', 'finished')->with(['company', 'injury_office', 'vehicle', 'vehicle_opposite', 'recovery_office', 'expertise_office', 'team', 'media'])->paginate(7, ['*'], 'claims');
        $claims_all = Claim::whereNot('status', 'finished')->count();
        $claims_asp_open = Claim::whereNot('status', 'finished')->where('assign_self', 0)->count();
        $company_claims = Claim::whereNot('status', 'finished')->where('company_id', $user->team_id)->get();
        $company_claims_open = Claim::where('status', 'finished')->where('company_id', $user->team_id)->where('assign_self', 1)->count();
        
        $unassignedClaims = count(Claim::where('assignee_id', null)->whereNot('status', 'finished')->get());
        $longestClaim = Claim::whereNotIn('status', ['finished', 'claim_denied'])->orderBy('created_at', 'asc')->get()->first();

        $claims_count = [
            'claims_all' => $claims_all,
            'company_claims_open' => $company_claims_open,
            'claims_asp_open' => $claims_asp_open,
        ];

        $companies = Company::get();
        
        $injury_offices = InjuryOffice::get();
        
        $vehicles = Vehicle::get();
        
        $vehicle_opposites = VehicleOpposite::get();
        
        $recovery_offices = RecoveryOffice::get();
        
        $expertise_offices = ExpertiseOffice::get();

        $most_origin_damage = Claim::select('damage_origin')
        ->groupBy('damage_origin')
        ->whereNotNull('damage_origin')
        ->get();

        $all_damages = json_decode($most_origin_damage, true);
        $most_origin_damages = [];   

        foreach($all_damages as $all_damage) {
            $damages = json_decode($all_damage['damage_origin']);

            foreach($damages as $damage) {
                $most_origin_damages[] = $damage;
            }
        }

        $most_origin_damages = array_count_values($most_origin_damages);
        arsort($most_origin_damages);
        $popular = array_slice(array_keys($most_origin_damages), 0, 1, true);

        $tasks = Task::with(['user', 'claim', 'team'])->whereNot('status', 'done')->orderBy('deadline_at')->paginate(5, ['*'], 'tasks');
        $personal_tasks = Task::where('user_id', $user->id)->whereNot('status', 'done')->orderBy('deadline_at')->paginate(5, ['*'], 'ptasks');
        
        $users = User::get();
        $teams = Team::get();

        return view('home', compact('claims', 'popular', 'claims_count', 'company_claims', 'personal_tasks', 'companies', 'expertise_offices', 'injury_offices', 'recovery_offices', 'teams', 'vehicle_opposites', 'vehicles', 'tasks', 'teams', 'users', 'unassignedClaims', 'longestClaim'));
    }
}
