<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $companies = Company::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');


        return view('analytics', compact('companies'));
    }

}
