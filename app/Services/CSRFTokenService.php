<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\AboutUsEcareerResource;
use App\Http\Resources\AboutUsResource;
use App\Http\Resources\ManagementResource;
use App\Http\Resources\SliderImageResource;
use App\Repositories\AboutUsEcareerRepository;
use App\Repositories\AboutUsRepository;
use App\Repositories\EcareerPortalRepository;
use App\Repositories\ManagementRepository;
use App\Repositories\SliderImageRepository;
use App\Repositories\SliderRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

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
