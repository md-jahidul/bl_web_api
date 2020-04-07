<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\ProductBookmarkService;
use Illuminate\Http\Request;

class ProductBookmarkController extends Controller {

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

    public function getBookmarkAppService(Request $request) {
        return $this->productBookmarkService->appServiceProducts($request);
    }

    public function getBookmarkBusiness(Request $request) {
        return $this->productBookmarkService->businessProducts($request);
    }

    public function getBookmarkOffers(Request $request) {
        return $this->productBookmarkService->offerProducts($request);
    }

}
