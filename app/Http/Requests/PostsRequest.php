<?php

namespace App\Http\Requests;
use App\Http\Requests\AbstractRequest;

class PostsRequest extends AbstractRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'title' => ['required', 'string', 'unique:posts'],
            'body' => ['nullable', 'string'],
            'author_id' => ['required', 'numeric', 'min:1'],
        ];
    }
}
