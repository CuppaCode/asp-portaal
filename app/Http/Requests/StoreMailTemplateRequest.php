<?php

namespace App\Http\Requests;

use App\Models\MailTemplate;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreMailTemplateRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('mail_template_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'required'
            ],
            'body' => [
                'required'
            ]
        ];
    }
}
