<?php

namespace App\Http\Requests;

use App\Models\Task;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreTaskRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('task_create');
    }

    public function rules()
    {
        return [
            'task_number' => [
                'string',
                'required',
                'unique:tasks',
            ],
            'user_id' => [
                'required',
                'integer',
            ],
            'claim_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
