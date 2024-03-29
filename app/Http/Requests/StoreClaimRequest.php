<?php

namespace App\Http\Requests;

use App\Models\Claim;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreClaimRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('claim_create');
    }

    public function rules()
    {
        return [
            'company_id' => [
                'required',
                'integer',
            ],
            'subject' => [
                'string',
                'required',
            ],
            'claim_number' => [
                'string',
                'required',
            ],
            'status' => [
                'required',
            ],
            'date_accident' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'injury_other' => [
                'string',
                'nullable',
            ],
            'vehicle_id' => [
                'integer',
            ],
            'requested_at' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'report_received_at' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'damage_files' => [
                'array',
            ],
            'report_files' => [
                'array',
            ],
            'financial_files' => [
                'array',
            ],
            'other_files' => [
                'array',
            ],
        ];
    }
}
