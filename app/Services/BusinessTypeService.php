<?php

namespace App\Services;

use App\Http\Resources\BusinessTypeResource;
use App\Repositories\BusinessTypeRepository;
use Exception;

/**
 * Class BusinessTypeService
 * @package App\Services
 */
class BusinessTypeService extends ApiBaseService
{

    /**
     * @var BusinessTypeRepository
     */
    protected $businessTypeRepository;


    /**
     * BannerService constructor.
     * @param BusinessTypeRepository $bannerRepository
     */
    public function __construct(BusinessTypeRepository $businessTypeRepository)
    {
        $this->businessTypeRepository = $businessTypeRepository;
    }

    /**
     * Request for Banner info
     *
     * @return mixed|string
     */
    public function getBusinessTypeInfo()
    {
        try {
            $data = $this->businessTypeRepository->getBusinessTypeList();
            $formatted_data = BusinessTypeResource::collection($data);
            return $formatted_data;
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }
}
