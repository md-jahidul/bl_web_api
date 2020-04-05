<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\ProductBookmarkService;

class ProductBookmarkController extends Controller
{
    /**
     * @var ProductBookmarkService
     */
    private $productBookmarkService;

    /**
     * ProductBookmarkController constructor.
     * @param ProductBookmarkService $productBookmarkService
     */
    public function __construct(
        ProductBookmarkService $productBookmarkService
    ) {
        $this->productBookmarkService = $productBookmarkService;
    }

    public function getBookmarkAppService()
    {
        return $this->productBookmarkService->appServiceProducts();
    }

}
