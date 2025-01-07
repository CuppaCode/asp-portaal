<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Gate;

class SuperAdminController extends Controller
{
    public function index()
    {
        //

        return view('home', compact('claims', 'popular', 'claims_count', 'company_claims', 'personal_tasks', 'companies', 'expertise_offices', 'injury_offices', 'recovery_offices', 'teams', 'vehicle_opposites', 'vehicles', 'tasks', 'teams', 'users'));
    }

    public function isSuperAdmin(User $user)
    {
        // Maybe a different right can be created in the future?
        abort_if(Gate::denies('claim_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $superAdminEmails = [
            'jeffrey@cuppacode.nl'
        ];

    // IPV4
        $superAdminIPs = [
            '188.91.67.52', //Erik home
            '82.168.180.202', // Jeffrey home
            '127.0.0.1' //locally
        ];

        $IP = null;

        // if user from the share internet   
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {   
            $IP = $_SERVER['HTTP_CLIENT_IP'];   
        }   
        //if user is from the proxy   
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   
            $IP = $_SERVER['HTTP_X_FORWARDED_FOR'];   
        }   
        //if user is from the remote address   
        else{   
            $IP = $_SERVER['REMOTE_ADDR'];   
        }   

        if (!$user->isAdmin) return false;

        if (!in_array($user->email, $superAdminEmails)) return false;

        if (!in_array($IP, $superAdminIPs)) return false;

        return true;

    }
}
