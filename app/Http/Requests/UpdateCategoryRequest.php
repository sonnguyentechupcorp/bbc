<?php

namespace App\Http\Requests;
use App\Http\Requests\AbstractRequest;

class UpdateCategoryRequest extends AbstractRequest
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
            'name' => ['string', 'unique:categories'],
            'slug' => ['nullable', 'string', 'unique:categories'],
        ];
    }
}
