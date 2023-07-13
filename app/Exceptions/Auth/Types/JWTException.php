<?php

namespace App\Exceptions\Auth\Types;

use App\Exceptions\Auth\Interfaces\IExceptionResponse;

class JWTException implements IExceptionResponse
{
    public function response()
    {
        $response = [
            'status' => 'FAIL',
            'status_code' => 401,
            'message' => "Uninformed Token",
        ];
        return response()->json($response, 400);
    }
}
