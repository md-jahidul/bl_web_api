<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Services\AboutUsService;
use App\Services\FileFormatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class FileFormatController extends Controller
{

    /**
     * @var FileFormatService
     */
    private $fileFormatService;


    /**
     * AboutUsController constructor.
     * @param FileFormatService $fileFormatService
     */
    public function __construct(FileFormatService $fileFormatService)
    {
        $this->fileFormatService = $fileFormatService;
    }

    /**
     * @return JsonResponse
     */
    public function getFileUrl()
    {
        return $this->fileFormatService->getUrl();
    }

}
