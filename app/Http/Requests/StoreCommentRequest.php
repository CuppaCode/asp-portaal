<?php

namespace App\Http\Requests;

use App\Models\Comment;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCommentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('comment_create');
    }

    public function rules()
    {
        return [
            'commentable_id' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'commentable_type' => [
                'string',
                'required',
            ],
        ];
    }
}
