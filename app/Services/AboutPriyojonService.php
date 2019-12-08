<?php

namespace App\Services;

use App\Http\Resources\AboutPriyojonResource;
use App\Repositories\AboutPriyojonRepository;
use App\Traits\CrudTrait;
use Illuminate\Database\QueryException;


class AboutPriyojonService
{
    use CrudTrait;

    /**
     * @var $partnerOfferDetailRepository
     */
    protected $aboutPriyojonRepository;

    /**
     * AboutPriyojonService constructor.
     * @param AboutPriyojonRepository $aboutPriyojonRepository
     */
    public function __construct(AboutPriyojonRepository $aboutPriyojonRepository)
    {
        $this->aboutPriyojonRepository = $aboutPriyojonRepository;
        $this->setActionRepository($aboutPriyojonRepository);
    }

    /**
     * @return mixed
     */
    public function aboutDetails()
    {
        try {
            $data = $this->aboutPriyojonRepository->findDetail('about_priyojon');
            if (!empty($data)) {
                $aboutDetails = AboutPriyojonResource::collection($data);
                return response()->success($aboutDetails, 'Data Found!');
            }
            return response()->error("Data Not Found!");
        } catch (QueryException $exception) {
            return response()->error("Something Wrong", $exception);
        }
    }
}
