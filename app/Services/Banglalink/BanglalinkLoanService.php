<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/8/19
 * Time: 12:24 PM
 */

namespace App\Services\Banglalink;


use App\Services\ApiBaseService;

class BanglalinkLoanService extends BaseService
{
    protected $endpoint = '/customer-information/customer-information/{customerId}/available-loan-products';

    protected $apiBaseService;

    /**
     * BanglalinkProductService constructor.
     * @param ApiBaseService $apiBaseService
     */
    public function __construct(ApiBaseService $apiBaseService)
    {
        $this->apiBaseService = $apiBaseService;
    }


    public function getCustomerLoanProducts($customerId)
    {
        $url = str_replace("{customerId}", $customerId, $this->endpoint);

        $result = $this->get($url);

        if ($result['status_code'] == 200) {
            return json_decode($result['response'], true);
        }
        throw new \RuntimeException("Internal service error", $result['status_code']);
    }
}
