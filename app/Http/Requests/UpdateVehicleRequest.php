<?php

namespace App\Http\Requests;

use App\Models\Vehicle;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('vehicle_edit');
    }

    public function rules()
    {
        return [
            'company_id' => [
                'required',
                'integer',
            ],
            'name' => [
                'string',
                'nullable',
            ],
            'plates' => [
                'string',
                'required',
                'unique:vehicles,plates,' . request()->route('vehicle')->id,
            ],
            'drivers.*' => [
                'integer',
            ],
            'drivers' => [
                'array',
            ],
        ];
    }
}
