<?php

namespace App\Services;

use Session;

class CSRFTokenService extends ApiBaseService
{
    public function getCSRFToken($request)
    {
        $generated_token = csrf_token();
        Session::put($generated_token, $generated_token);
        $data = [
            '_token' => $generated_token
        ];
        return $this->sendSuccessResponse($data, 'Token successfully generated');
    }
}
