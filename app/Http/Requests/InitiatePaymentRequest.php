<?php

namespace App\Http\Requests;

use App\Enums\HttpStatusCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class InitiatePaymentRequest extends FormRequest
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
            "recharge_number" => "required|array|min:1|max:3",
            "recharge_number.*.topup_number" => "required",
            "recharge_number.*.connection_type" => "required|in:prepaid,postpaid,PREPAID,POSTPAID",
            "recharge_number.*.amount" => "required|numeric|min:10|max:1000",
            "email" => "email"
        ];
        /*        return [
                    'amount' => 'required',
                    'topup_number' => 'required',
                    'connection_type' => 'required',
                    'email' => 'required'
                ];*/
    }

    public function messages()
    {
        return [
            'recharge_number.*.amount.required' => 'Recharge Amount is required',
            'recharge_number.*.amount.min' => 'Recharge Amount must be minimum 10 Tk.',
            'recharge_number.*.amount.max' => 'Recharge Amount must be less 1000 Tk.',
        ];
    }
}
