<?php

namespace App\Exceptions\Auth;

use App\Exceptions\Auth\Interfaces\IExceptionResponse;

class AuthExceptionService
{
    CONST GENERIC_EXCEPTION = 'App\Exceptions\Auth\Types\GenericException';

    CONST EXCEPTIONS = [
        'Tymon\JWTAuth\Exceptions\TokenExpiredException' => 'App\Exceptions\Auth\Types\TokenExpiredException',
        'Tymon\JWTAuth\Exceptions\TokenInvalidException' => 'App\Exceptions\Auth\Types\TokenInvalidException',
        'Tymon\JWTAuth\Exceptions\TokenBlacklistedException' => 'App\Exceptions\Auth\Types\TokenBlackListException',
        'Tymon\JWTAuth\Exceptions\JWTException' => 'App\Exceptions\Auth\Types\JWTException'
    ];

    protected $exception;

    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

    public function getType() : IExceptionResponse
    {
        $exception_class = get_class($this->exception);

        if( array_key_exists( $exception_class , $this::EXCEPTIONS) ){
            return $this->getInstance( $this::EXCEPTIONS[$exception_class] );
        }

        return $this->getInstance( $this::GENERIC_EXCEPTION );
    }

    private function getInstance(string $class) : IExceptionResponse
    {
        return new $class();
    }
}
