<?php

namespace App\Http\Requests;
use App\Http\Requests\AbstractRequest;

class CategoryRequest extends AbstractRequest
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
            'name' => ['required', 'string', 'unique:categories'],
            'slug' => ['nullable', 'string', 'unique:categories'],
        ];
    }
}
