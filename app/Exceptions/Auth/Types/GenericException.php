<?php

namespace App\Exceptions\Auth\Types;

use App\Exceptions\Auth\Interfaces\IExceptionResponse;

class GenericException implements IExceptionResponse
{
    public function response()
    {
        return response()->json([
            'response' => 'Erro não mapeado'
        ], 400);
    }
}
