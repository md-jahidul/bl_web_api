<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/5/19
 * Time: 3:35 PM
 */

namespace App\Services\Banglalink;


use App\Enums\HttpStatusCode;
use App\Services\ApiBaseService;

class BanglalinkProductService extends BaseService
{
    protected $endpoint = "/customer-information/customer-information/{customerId}/available-products";

    protected $apiBaseService;

    /**
     * BanglalinkProductService constructor.
     * @param ApiBaseService $apiBaseService
     */
    public function __construct(ApiBaseService $apiBaseService)
    {
        $this->apiBaseService = $apiBaseService;
    }


    public function getCustomerProducts($customerId)
    {
        $endpoint = $this->endpoint . "?channel=" . env("PURCHASE_CHANNEL_NAME", 'website');
        $url = str_replace("{customerId}", $customerId, $endpoint);
        $result = $this->get($url);

        if ($result['status_code'] == 200) {
            return json_decode($result['response'], true);
        }
        throw new \RuntimeException("Internal service error", $result['status_code']);
    }
}
