<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Gate;

class SuperAdminController extends Controller
{
    public function index()
    {
        //

        return view('super-admin');
    }
}
