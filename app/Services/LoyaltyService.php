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

    public function getPriyojonStatus($mobile, $connectionType)
    {
        $subscriberId = $mobile;
//        $subscriberId = substr($mobile, 1);
        //$subscriberId = '1903303978'; //TODO: Remove from production
        $result = $this->blLoyaltyService->getPriyojonStatus($subscriberId, $connectionType);
        return $this->sendSuccessResponse($result, 'Loyalty data');
//        return $this->sendSuccessResponse($result['loyaltyPrograms'], 'Loyalty data');
    }

    public function getRedeemOptions($mobile)
    {
        $subscriberId = substr($mobile, 1);
        //$subscriberId = '1903303978'; //TODO: Remove from production

        $result = $this->blLoyaltyService->getRedeemOptions($subscriberId);
        return $this->sendSuccessResponse($result['data'], 'Loyalty data');
    }

    public function redeemOffer($mobile)
    {

    }

}
