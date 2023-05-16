<?php

namespace App\Http\Requests;

use App\Models\InjuryOffice;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyInjuryOfficeRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('injury_office_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:injury_offices,id',
        ];
    }
}
