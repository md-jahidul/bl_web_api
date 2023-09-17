<?php

namespace App\Exceptions\Auth\Types;

use App\Exceptions\Auth\Interfaces\IExceptionResponse;
use Illuminate\Support\Facades\Log;

class TokenInvalidException implements IExceptionResponse
{
    public function response()
    {
//        Log::error('IDP Error: code - '.$this->getCode(). 'Error message: ' . $this->getMessage());

        $response = [
            'status' => 'FAIL',
            'status_code' => 401,
            'message' => "Token is invalid",
        ];
        return response()->json($response, 401);
    }
}
