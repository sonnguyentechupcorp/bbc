<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AbstractRequest extends FormRequest
{
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if (Request::wantsJson() || Request::is('api/*')) {
            throw new ValidationException(
                $validator,
                new JsonResponse([
                    'status' => false,
                    'code' => 0,
                    'locale' => app()->getLocale(),
                    'message' => __('The given data was invalid'),
                    'errors' => $validator->getMessageBag()->toArray()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
