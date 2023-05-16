<?php

namespace App\Http\Requests;

use App\Models\VehicleOpposite;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyVehicleOppositeRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('vehicle_opposite_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:vehicle_opposites,id',
        ];
    }
}
