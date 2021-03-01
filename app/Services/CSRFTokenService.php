<?php

namespace App\Services;


class CSRFTokenService extends ApiBaseService
{
    public function getCSRFToken()
    {
        $data = [
            '_token' => csrf_token()
        ];
        return $this->sendSuccessResponse($data, 'Token successfully generated');
    }
}
