<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpsellPurchaseInvocationRequest extends FormRequest
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
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'msisdn'            => 'required',
            'product_code'      => 'required',
            'pay_with_balance'  => 'required',   
            'product_details.name' => 'required',
            'product_details.price' => 'required',
            'product_details.currency' => 'required',
            'product_details.data_amount' => 'required',
            'product_details.data_unit' => 'required',
            'product_details.time_amount' => 'required',
            'product_details.time_unit' => 'required',
        ];
    }
}
