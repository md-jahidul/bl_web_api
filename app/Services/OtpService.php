<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\OtpConfigResource;
use App\Repositories\AppVersionRepository;
use App\Repositories\OtpConfigRepository;

class OtpService extends ApiBaseService
{

    /**
     * @var AppVersionRepository
     */
    protected $otpConfigRepository;


    /**
     * BanglalinkOtpService constructor.
     * @param OtpConfigRepository $otpConfigRepository
     */
    public function __construct(OtpConfigRepository $otpConfigRepository)
    {
        $this->otpConfigRepository = $otpConfigRepository;
    }


    /**
     * Version Info
     * @return mixed
     */
    public function getOtpConfigInfo()
    {
        try {
            $data = $this->otpConfigRepository->findAll();

            if (count($data) == 0) {
                $data = collect([
                    (object) [
                        'token_length_number'  => 6,
                        'validation_time'      => config('apiconfig.opt_token_expiry')
                    ]
                ]);
            }

            $formatted_data = OtpConfigResource::collection($data);

            return $this->sendSuccessResponse($formatted_data, 'OTP Config', [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }

}
