<?php

namespace App\Services;

use App\Http\Resources\AboutPriyojonResource;
use App\Repositories\AboutPageRepository;
use App\Repositories\AboutPriyojonRepository;
use App\Traits\CrudTrait;
use Illuminate\Database\QueryException;


class AboutPageService
{
    use CrudTrait;

    /**
     * @var $aboutPageRepository
     */
    protected $aboutPageRepository;

    /**
     * AboutPageService constructor.
     * @param AboutPageRepository $aboutPageRepository
     */
    public function __construct(AboutPageRepository $aboutPageRepository)
    {
        $this->aboutPageRepository = $aboutPageRepository;
        $this->setActionRepository($aboutPageRepository);
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function aboutDetails($slug)
    {
        try {
            $data = $this->aboutPageRepository->findDetail($slug);
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
