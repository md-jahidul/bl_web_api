<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/9/19
 * Time: 11:54 AM
 */

namespace App\Services\Banglalink;


use App\Exceptions\BLApiHubException;
use App\Services\ApiBaseService;

class BanglalinkLoyaltyService extends BaseService
{
//    protected $statusEndPoint = '/loyalty-old-sys/loyalty-old-sys/priyojon-status';
    protected $statusEndPoint = '/loyalty/loyalty/priyojon-status';
    protected $lmsProfileEndPoint = '/loyalty2/loyalty2/get-member-profile';
    protected $redeemOptionEndPoint = '/loyalty/loyalty/get-priyojon-redeem-options';
    protected $redeemPurchaseEndPoint = '/loyalty/loyalty/purchase-priyojon-redeem';

    protected $apiBaseService;

    /**
     * BanglalinkLoyaltyService constructor.
     * @param ApiBaseService $apiBaseService
     */
    public function __construct(ApiBaseService $apiBaseService)
    {
        $this->apiBaseService = $apiBaseService;
    }

    public function getLmsMemberProfile($msisdn)
    {
        $body = array(
            'channel' => env('LMS_CHANNEL', 'MYBLAPP'),
            'msisdn' => $msisdn,
            'transactionID' => uniqid()
        );
        $url = $this->lmsProfileEndPoint;
        return $this->post($url, $body);
    }

    public function getPriyojonStatus($subscriberId)
    {
        $customerId =  substr($subscriberId, 3);
        $url = $this->statusEndPoint . '?customerId=' . $customerId;
        $result = $this->get($url);

        if ($result['status_code'] == 200) {
            $data = json_decode($result['response'], true);
            if ($data['message'] == 'OK') {
                return $this->apiBaseService->sendSuccessResponse($data, 'Loyalty info', '', '', 200);
            }
           return $this->apiBaseService->sendErrorResponse('This user is not eligible loyalty', '', 500);
        }
        return $this->apiBaseService->sendErrorResponse('Internal service error', '', 500);
    }

    public function getRedeemOptions($subscriberId)
    {
        $url = $this->redeemOptionEndPoint . '?msisdn=' . $subscriberId;
        $result = $this->get($url);
        if ($result['status_code'] == 200) {
            $data = json_decode($result['response'], true);
            if ($data['message'] == 'OK') {
                return $data;
            }
            throw new BLApiHubException($data['message'], 500);

        }
        throw new BLApiHubException("Internal service error", $result['status_code']);
    }

    public function redeemOfferPurchase($msisdn, $offerId)
    {
        $purchaseUrl = $this->redeemPurchaseEndPoint;
        $body = ["msisdn" => $msisdn, "offerId" => $offerId, "channelID" => 25, "salesChannelID" => 24];
        $result = $this->post($purchaseUrl, $body);

        if ($result['status_code'] == 200) {
            $response = json_decode($result['response'], true);
            return $response['data'];
        }
        throw new BLApiHubException("Internal service error", $result['status_code']);
    }
}
