<?php

namespace App\Http\Requests;

use App\Models\InjuryOffice;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreInjuryOfficeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('injury_office_create');
    }

    public function rules()
    {
        return [
            'company_id' => [
                'required',
                'integer',
            ],
            'identifier' => [
                'string',
                'nullable',
            ],
        ];
    }
}
