<?php

namespace App\Http\Requests;

use App\Models\MailTemplate;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyMailTemplateRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('mail_template_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:mail_templates,id',
        ];
    }
}
