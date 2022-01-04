<?php

namespace App\Http\Requests;
use App\Http\Requests\AbstractRequest;

class UserRequest extends AbstractRequest
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
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'email' =>['required', 'string', 'unique:users,email'],
            'password' => ["required" , "string"],
            'birth_date' => ['nullable', 'date_format:Y-m-d'],
            'gender' => ['nullable', 'in:0,1'],
            'avatar' => ['nullable', 'image']
        ];
    }
}
