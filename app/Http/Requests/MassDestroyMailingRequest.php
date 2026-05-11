<?php

namespace App\Http\Requests;

use App\Models\Mailing;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyMailingRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('mailing_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:mailings,id',
        ];
    }
}
