<?php

namespace App\Http\Requests;


class RegisterRequest extends AbstractRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'name' => ['required', 'string',' min:1', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'regex:/(\w)@gmail\.com/i', 'unique:users'],
            'password' => ['required' , 'string', 'confirmed'],
            'birth_date' => ['nullable', 'date_format:Y-m-d'],
            'gender' => ['nullable', 'in:0,1']
        ];
    }
}
