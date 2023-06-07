<?php

namespace App\Http\Requests;

use App\Models\Vehicle;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreVehicleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('vehicle_create');
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
                'unique:vehicles',
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
