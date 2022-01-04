<?php

namespace App\Http\Requests;

class UpdateUserRequest extends AbstractRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['string', 'min:1', 'max:255'],
            'avatar' => ['nullable', 'image'],
            'birth_date' => ['nullable', 'date_format:Y-m-d'],
            'gender' => ['nullable', 'in:0,1']
        ];
    }
}
