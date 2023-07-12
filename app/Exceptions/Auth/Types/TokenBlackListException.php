<?php

namespace App\Exceptions\Auth\Types;

use App\Exceptions\Auth\Interfaces\IExceptionResponse;

class TokenBlackListException implements IExceptionResponse
{
    public function response()
    {
        $response = [
            'status' => 'FAIL',
            'status_code' => 401,
            'message' => "The Token has been blacklisted",
        ];
        return response()->json($response, 403);
    }
}
