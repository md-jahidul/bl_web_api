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
