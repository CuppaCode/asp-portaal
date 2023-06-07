<?php

namespace App\Http\Requests;

use App\Models\Note;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateNoteRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('note_edit');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
            ],
            'description' => [
                'required',
            ],
            'claims.*' => [
                'integer',
            ],
            'claims' => [
                'required',
                'array',
            ],
            'user_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
