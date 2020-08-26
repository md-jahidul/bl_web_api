<?php

namespace App\Services;

use App\Http\Resources\AboutPriyojonResource;
use App\Repositories\AboutPageRepository;
use App\Repositories\AboutPriyojonRepository;
use App\Traits\CrudTrait;
use Illuminate\Database\QueryException;


class FileFormatService extends ApiBaseService
{

//    /**
//     * AboutPageService constructor.
//     * @param AboutPageRepository $aboutPageRepository
//     */
//    public function __construct(AboutPageRepository $aboutPageRepository)
//    {
//        $this->aboutPageRepository = $aboutPageRepository;
//    }

    /**
     * @param $slug
     * @return mixed
     */
    public function getUrl()
    {
        try {
            $data = [
              'file_url' => asset('test-file/END_June_4G_Coverage_2020.kmz')
            ];
            return $this->sendSuccessResponse($data, 'File Url');
        } catch (QueryException $exception) {
            return response()->error("Something Wrong", $exception);
        }
    }
}
