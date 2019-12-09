<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/9/19
 * Time: 11:54 AM
 */

namespace App\Services\Banglalink;


use App\Services\ApiBaseService;

class BanglalinkLoyaltyService extends BaseService
{
    protected $statusEndPoint = '/loyalty/loyalty/priyojon-status';

    protected $apiBaseService;

    /**
     * BanglalinkLoyaltyService constructor.
     * @param ApiBaseService $apiBaseService
     */
    public function __construct(ApiBaseService $apiBaseService)
    {
        $this->apiBaseService = $apiBaseService;
    }

    public function getPriyojonStatus ($subscriberId)
    {
        $url = $this->statusEndPoint . '?customerId=' . $subscriberId;
        $result = $this->get($url);
        if ($result['status_code'] == 200) {
            return json_decode($result['response'], true);
        }
        throw new \RuntimeException("Internal service error", $result['status_code']);
    }
}
