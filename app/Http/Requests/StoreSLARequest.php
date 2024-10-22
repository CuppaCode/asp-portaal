<?php

namespace App\Http\Requests;

use App\Models\SLA;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreSLARequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('sla_access');
    }

    public function rules()
    {
        return [
            'company_id' => [
                'required',
                'integer',
            ],
            'startdate' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'enddate' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'amount_users' => [
                'numeric',
                'nullable',
            ],
            'label' => [
                'string',
            ],
            'max_amount' => [
                'numeric',
                'nullable',
            ],
            'reports' => [
                'string',
            ],
            'other' => [
                'string',
                'nullable',
            ],
        ];
    }
}

