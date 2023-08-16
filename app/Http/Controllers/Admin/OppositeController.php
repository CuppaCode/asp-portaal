<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOppositeRequest;
use App\Http\Requests\UpdateOppositeRequest;
use App\Models\Opposite;
use App\Models\Claim;
use App\Models\Note;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class OppositeController extends Controller
{

    public function store(StoreOppositeRequest $request)
    {
        $opposite = Opposite::create($request->all());
        $opposite->claims()->sync($request->input('claims', []));
    }

    public function update(UpdateOppositeRequest $request, Opposite $note)
    {
        $opposite = Opposite::create($request->all());
        $opposite->claims()->sync($request->input('claims', []));
    }
}
