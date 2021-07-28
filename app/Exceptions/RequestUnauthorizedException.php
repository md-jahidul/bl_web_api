<?php

namespace App\Exceptions;

use Exception;

class RequestUnauthorizedException extends Exception
{
    private $errorObj;
    private const ERROR_HINT = 'Please send request from a valid platform';
    private const ERROR_MESSAGE = 'You are not AUTHORIZED';

    private function initErrorObj()
    {
        $this->errorObj = new \stdClass();
        $this->errorObj->message = self::ERROR_MESSAGE;
        $this->errorObj->hint = self::ERROR_HINT;
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
