<?php

namespace App\Http\Requests;

use App\Models\InjuryOffice;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateInjuryOfficeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('mail_template_edit');
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
