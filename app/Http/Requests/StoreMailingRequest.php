<?php

namespace App\Http\Requests;

use App\Models\Mailing;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreMailingRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('mailing_create');
    }

    public function rules()
    {
        return [
            'subject' => [
                'required',
                'string',
            ],
            'body' => [
                'required',
            ],
            'recipients' => [
                'required',
                'array',
            ],
            'recipients.*' => [
                'email',
            ],
            'cc' => [
                'nullable',
                'array',
            ],
            'cc.*' => [
                'email',
            ],
            'bcc' => [
                'nullable',
                'array',
            ],
            'bcc.*' => [
                'email',
            ],
            'reply_to' => [
                'nullable',
                'email',
            ],
            'status' => [
                'nullable',
                'string',
            ],
            'claims' => [
                'array',
            ],
        ];
    }
}
