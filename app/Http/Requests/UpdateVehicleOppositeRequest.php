<?php

namespace App\Http\Requests;

use App\Models\VehicleOpposite;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateVehicleOppositeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('vehicle_opposite_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'plates' => [
                'string',
                'required',
                'unique:vehicle_opposites,plates,' . request()->route('vehicle_opposite')->id,
            ],
        ];
    }
}
