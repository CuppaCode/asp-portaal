<?php

namespace App\Http\Requests;

use App\Models\MailTemplate;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateMailTemplateRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('mail_template_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'required'
            ],
            'body' => [
                'required'
            ],
            'trigger_type' => [
                'nullable',
                'string'
            ],
            'reply_to' => [
                'nullable',
                'email'
            ],
            'code' => ['nullable', 'string', 'max:20'],
            'recipient_rule' => ['nullable', 'in:werkgever,opleider,chauffeur,deelnemers,intern'],
            'cc_rule' => ['nullable', 'in:werkgever'],
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'],
            'remove_attachment_ids' => ['nullable', 'array'],
            'remove_attachment_ids.*' => ['integer'],
            'is_active' => [
                'nullable',
                'boolean'
            ],
            'is_automatic' => [
                'nullable',
                'boolean'
            ]
        ];
    }
}
