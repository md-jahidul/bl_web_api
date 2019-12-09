<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/9/19
 * Time: 12:12 PM
 */

namespace App\Services;


use App\Enums\HttpStatusCode;
use App\Services\Banglalink\BanglalinkLoyaltyService;

class LoyaltyService extends ApiBaseService
{

    /**
     * @var BanglalinkLoyaltyService
     */
    protected $blLoyaltyService;

    /**
     * LoyaltyService constructor.
     * @param BanglalinkLoyaltyService $blLoyaltyService
     */
    public function __construct(BanglalinkLoyaltyService $blLoyaltyService)
    {
        $this->blLoyaltyService = $blLoyaltyService;
    }

    public function getPriyojonStatus($mobile)
    {
        //TODO:Prepare subscriber id from mobile number
        $subscriberId = '1903303978';

        $result = $this->blLoyaltyService->getPriyojonStatus($subscriberId);
        if ($result['responseMessage'] == 'SUCCESS') {
            return $this->sendSuccessResponse($result['loyaltyPrograms'], 'Loyalty data');
        } else {
            $this->sendErrorResponse($result['responseMessage'], [], HttpStatusCode::VALIDATION_ERROR);
        }
    }


}
