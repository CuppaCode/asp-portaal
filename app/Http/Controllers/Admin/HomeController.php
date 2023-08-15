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
        $company_claims = Claim::whereNot('status', 'finished')->where('company_id', $user->team_id)->get();
        
        $companies = Company::get();
        
        $injury_offices = InjuryOffice::get();
        
        $vehicles = Vehicle::get();
        
        $vehicle_opposites = VehicleOpposite::get();
        
        $recovery_offices = RecoveryOffice::get();
        
        $expertise_offices = ExpertiseOffice::get();
        
        $tasks = Task::with(['user', 'claim', 'team'])->whereNot('status', 'done')->orderBy('deadline_at')->paginate(5, ['*'], 'tasks');
        $personal_tasks = Task::where('user_id', $user->id)->whereNot('status', 'done')->orderBy('deadline_at')->paginate(5, ['*'], 'ptasks');
        
        $users = User::get();
        $teams = Team::get();

        return view('home', compact('claims', 'company_claims', 'personal_tasks', 'companies', 'expertise_offices', 'injury_offices', 'recovery_offices', 'teams', 'vehicle_opposites', 'vehicles', 'tasks', 'teams', 'users'));;
    }
}
