<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginWithOtpRequest extends FormRequest
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
            'otp_token'     => 'required|string',
            'grant_type'    => 'required',
            'client_id'     => 'required',
            'client_secret' => 'required',
            'otp'           => 'required',
            'username'      => 'required',
            'provider'      => 'required'
        ];
    }
}
