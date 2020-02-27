<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadRequest extends FormRequest
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
              'name' => 'required',
              'company_name' => 'required'
//              'mobile' => 'required',
//              'email' => 'required|email|unique:lead_requests',
//              'district' => 'required',
//              'thana'=> 'required',
//              'address' => 'required',
//              'quantity' => 'required',
//              'package' => 'required',
        ];
    }
}
