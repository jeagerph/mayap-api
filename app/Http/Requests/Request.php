<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class Request extends FormRequest
{
    protected function failedValidation(Validator $validator) 
	{
		throw new HttpResponseException(
            response()->json([
                'errors' => $validator->errors()
            ], 422)
        );
    }
    
    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json([
                'errors' => [
                    'message' => 'You are not authorized to perform this action.'
                ]
            ], 403)
        );
    }
}
