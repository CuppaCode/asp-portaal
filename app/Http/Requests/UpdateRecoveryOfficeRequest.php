<?php

namespace App\Http\Requests;

use App\Models\RecoveryOffice;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateRecoveryOfficeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('recovery_office_edit');
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
