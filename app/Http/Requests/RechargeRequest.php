<?php

namespace App\Http\Requests;

use App\Enums\HttpStatusCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class RechargeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'total_amount' => 'required',
            'topup_number'  => 'required'
        ];
    }

    /**
     * Validation error response
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     */
    protected function failedValidation($validator)
    {
        $errors = (new ValidationException($validator))->errors();

        $transformed_errors = [];

        foreach ($errors as $field => $message) {
            $transformed_errors = [
                'type'    => 'INVALID_PARAMETER',
                'message' => $message[0]
            ];
        }

        throw new HttpResponseException(response()->json([
            'status' => 'fail',
            'status_code' => HttpStatusCode::VALIDATION_ERROR,
            'message' => 'Validation Error',
            'errors'  => $transformed_errors
        ], HttpStatusCode::VALIDATION_ERROR));
    }
}
