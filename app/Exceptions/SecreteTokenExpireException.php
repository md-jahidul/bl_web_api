<?php

namespace App\Exceptions;

use App\Enums\ApiErrorCode;
use App\Enums\ApiErrorType;
use Exception;

class SecreteTokenExpireException extends Exception
{
    private $errorObj;
    private const ERROR_TYPE = ApiErrorType::TOKEN_NOT_FOUND_ERROR;
    private const ERROR_CODE = ApiErrorCode::TOKEN_INVALID_ERROR;
    private const ERROR_MESSAGE = 'The access token expired';

    private function initErrorObj()
    {
        $this->errorObj = new \stdClass();
        $this->errorObj->message = self::ERROR_MESSAGE;
        $this->errorObj->type = self::ERROR_TYPE;
        $this->errorObj->code = self::ERROR_CODE;
    }

    /**
     * TokenNotFoundException constructor.
     */
    public function __construct()
    {
        $this->initErrorObj();
    }

    public function render()
    {
        return response()->json([
            'status' => 'FAIL',
            'status_code' => 401,
            'error' => $this->errorObj
        ], 401);
    }
}
