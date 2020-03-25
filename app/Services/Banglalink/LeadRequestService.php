<?php

namespace App\Services\Banglalink;

use App\Repositories\LeadRequestRepository;
use App\Services\ApiBaseService;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;

/**
 * Class FnfService
 * @package App\Services\Banglalink
 */
class LeadRequestService extends ApiBaseService
{
    use CrudTrait;

    const BUSINESS = 'business';
    const BUSINESS_SUB = 'business';
    const BUSINESS_REQUEST_TYPE = 'business';

    const POSTPAID_CAT = ['postpaid'];
    const POSTPAID_SUB = ['package'];
    const POSTPAID_REQUEST_TYPE = ['order_postpaid_sim'];

    /**
     * @var LeadRequestRepository
     */
    protected $leadRequestRepository;

    public function __construct(LeadRequestRepository $leadRequestRepository)
    {
        $this->leadRequestRepository = $leadRequestRepository;
        $this->setActionRepository($leadRequestRepository);
    }

    /**
     * @param $data
     * @return string
     */
    public function saveRequest($data)
    {
        $this->save($data);
        return $this->sendSuccessResponse([], 'Form submit successfully');
    }

}
