<?php

namespace App\Http\Controllers\API\V1;

use App\Models\AppServiceProduct;
use App\Services\AppAndService;
use App\Services\ReferralCodeService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use App\Services\AppServiceDetailsService;

class AppServiceDetailsController extends Controller
{

   /** Details category list
   * app_banner_fixed_section
   */

   /**** Compoent Types ****
    * text_with_image_right
    * text_with_image_bottom
    * title_text_editor
    * multiple_image_banner
    * slider_text_with_image_right
    */

    /**
     * @var $appServiceCategoryRepository
     */
    private $appServiceDetailsService;
    /**
     * @var $appServiceTabRepository
     */
    private $appServiceProduct;
    /**
     * @var $AppAndService
     */
    protected $info = [];
    /**
     * @var ReferralCodeService
     */
    private $referralCodeService;

    /**
     *
     * @param AppServiceDetailsService $appServiceDetailsService
     * @param ReferralCodeService $referralCodeService
     * @param AppAndService $appServiceProduct
     */

    public function __construct(
        AppServiceDetailsService $appServiceDetailsService,
        ReferralCodeService $referralCodeService,
        AppAndService $appServiceProduct
    ) {
        $this->appServiceDetailsService = $appServiceDetailsService;
        $this->referralCodeService = $referralCodeService;
        $this->appServiceProduct = $appServiceProduct;
    }


    /**
     * Get details component with
     * @param  [type] $product_id [description]
     * @return [type]             [description]
     */
    public function appServiceDetailsComponent($slug)
    {
        return $this->appServiceDetailsService->getDetails($slug);
    }

    public function getReferralCode($mobileNo, $appId)
    {
        return $this->referralCodeService->referralCodeGenerator($mobileNo, $appId);
    }

    public function shareReferralCode(Request $request)
    {
        return $this->referralCodeService->shareReferralCount($request->all());
    }




//    /**
//     * Display a listing of the resource.
//     *
//     * @param $tab_type
//     * @param $product_id
//     * @return Factory|View
//     */
//
//    public function productDetails($tab_type, $product_id)
//    {
//        $this->info['tab_type'] = $tab_type;
//        $this->info['product_id'] = $product_id;
//        $this->info["section_list"] = $this->appServiceDetailsService->sectionList($product_id);
//        $this->info["products"] = $this->appServiceProduct->appServiceRelatedProduct($tab_type, $product_id);
//        $this->info["productDetail"] = $this->appServiceProduct->detailsProduct($product_id);
//        $this->info["fixedSectionData"] = $this->info["section_list"]['fixed_section'];
//        return view('admin.app-service.details.section.index', $this->info);
//    }
//
//    /**
//     * Store a newly created resource in storage.
//     *
//     * @param Request $request
//     * @return RedirectResponse|Redirector
//     */
//    public function store(Request $request, $tab_type, $product_id)
//    {
//        $response = $this->appServiceDetailsService->storeAppServiceProductDetails($request->all(), $tab_type, $product_id);
//        Session::flash('message', $response->getContent());
//        return redirect(url("app-service/details/$tab_type/$product_id"));
//    }
//
//    /**
//     * Display the specified resource.
//     *
//     * @param Request $request
//     * @param $tab_type
//     * @param $product_id
//     * @return Request
//     */
//    public function fixedSectionUpdate(Request $request, $tab_type, $product_id)
//    {
//        $response = $this->appServiceDetailsService->fixedSectionUpdate($request->all(), $tab_type, $product_id);
//        Session::flash('message', $response->getContent());
//        return redirect(url("app-service/details/$tab_type/$product_id"));
//    }
//
//    /**
//     * Show the form for editing the specified resource.
//     *
//     * @param int $id
//     * @return Factory|View
//     */
//    public function edit($tab_type, $product_id, $id)
//    {
//        $section = $this->appServiceDetailsService->findOne($id);
//        return view('admin.app-service.details.section.edit', compact('tab_type', 'product_id', 'section'));
//    }
//
//    /**
//     * Update the specified resource in storage.
//     *
//     * @param Request $request
//     * @param int $id
//     * @return RedirectResponse|Redirector
//     */
//    public function update(Request $request, $tab_type, $product_id, $id)
//    {
//        $response = $this->appServiceDetailsService->updateAppServiceDetailsSection($request->all(), $id);
//        Session::flash('message', $response->getContent());
//        return redirect(route('app_service.details.list', [$tab_type, $product_id]));
//    }
//
//    public function tabWiseCategory($tabId)
//    {
//        return $this->appServiceCategoryRepository
//            ->findByProperties(['app_service_tab_id' => $tabId], ['id', 'title_en', 'alias']);
//    }
//
//    /**
//     * Remove the specified resource from storage.
//     *
//     * @param int $id
//     * @return Response
//     */
//    public function destroy($id)
//    {
//        //
//    }
}
