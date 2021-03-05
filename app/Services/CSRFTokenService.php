<?php

namespace App\Services;

class CSRFTokenService extends ApiBaseService
{
    public function getCSRFToken($request)
    {
//        dd(csrf_token());
//        dd($request->session()->token());

        $data = [
            '_token' => csrf_token()
        ];
        return $this->sendSuccessResponse($data, 'Token successfully generated');
    }
}
