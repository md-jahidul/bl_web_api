<?php

namespace App\Services;

use App\Repositories\AlCsrfTokenRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Carbon\Carbon;
use Session;

class CSRFTokenService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var AlCsrfTokenRepository
     */
    private $alCsrfTokenRepository;

    /**
     * AlCsrfTokenRepository constructor.
     * @param AlCsrfTokenRepository $alCsrfTokenRepository
     */
    public function __construct(
        AlCsrfTokenRepository $alCsrfTokenRepository
    ) {
        $this->alCsrfTokenRepository = $alCsrfTokenRepository;
        $this->setActionRepository($alCsrfTokenRepository);
    }

    public function getCSRFToken($request)
    {
        $token_expiry = config('session.lifetime');
        $bdTimeZone = Carbon::now('Asia/Dhaka');
        $currentTime = $bdTimeZone->toDateTimeString();
        $expires_time = $bdTimeZone->addMinutes($token_expiry);

        $this->alCsrfTokenRepository->deleteExpiredToken($currentTime, $expires_time);

        $token = csrf_token();
        $existToken = $this->alCsrfTokenRepository->findOneBy(['token' => $token]);

        $strLn = str_split($token, 20);
        $partOneRev = array_reverse(str_split($strLn[0]));
        $partOne = implode($partOneRev);
        $partTwoRev = array_reverse(str_split($strLn[1]));
        $partTwo = implode($partTwoRev);
        $arrayReverse = $partTwo.$partOne;

        $convBase64 = str_replace('=', '', base64_encode($arrayReverse));


        if (!$existToken) {
            $data['token'] = $token;
            $data['secret_key'] = $convBase64;
            $data['expires_at'] = $expires_time;
            $this->save($data);
        }
        $data = [
            '_token' => $token
        ];
        return $this->sendSuccessResponse($data, 'Token successfully generated');
    }
}
