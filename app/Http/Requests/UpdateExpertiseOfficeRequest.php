<?php

namespace App\Http\Requests;

use App\Models\ExpertiseOffice;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateExpertiseOfficeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('expertise_office_edit');
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
