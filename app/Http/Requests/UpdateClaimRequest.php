<?php

namespace App\Http\Requests;

use App\Models\Claim;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateClaimRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('claim_edit');
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
            'date_accident' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'injury_other' => [
                'string',
                'nullable',
            ],
            'vehicle_id' => [
                'required',
                'integer',
            ],
            'requested_at' => [
                'nullable',
                'date_format:' . config('panel.date_format'),
            ],
            'report_received_at' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'damage_files' => [
                'array',
                'max:10',
            ],
            'damage_files.*' => [
                'file',
                'max:10240', // 10MB
                'mimetypes:image/jpeg,image/png,image/gif,application/pdf',
            ],
            'report_files' => [
                'array',
                'max:10',
            ],
            'report_files.*' => [
                'file',
                'max:10240', // 10MB
                'mimetypes:image/jpeg,image/png,image/gif,application/pdf',
            ],
            'financial_files' => [
                'array',
                'max:10',
            ],
            'financial_files.*' => [
                'file',
                'max:10240', // 10MB
                'mimetypes:image/jpeg,image/png,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
            'other_files' => [
                'array',
                'max:10',
            ],
            'other_files.*' => [
                'file',
                'max:10240', // 10MB
                'mimetypes:image/jpeg,image/png,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
        ];
    }
}
