<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\PartnerOfferResource;
use App\Http\Resources\QuickLaunchResource;
use App\Http\Resources\SliderImageResource;
use App\Models\BusinessOthers;
use App\Models\QuickLaunch;
use App\Models\QuickLaunchItem;
use App\Models\AlSlider;
use App\Models\AlSliderComponentType;
use App\Models\AlSliderImage;
use App\Models\ShortCode;
use App\Models\MetaTag;
use App\Repositories\DynamicUrlRedirectionRepository;
use App\Services\HomeService;
use App\Services\ProductService;
use App\Services\QuickLaunchService;
use App\Services\SalesAndServicesService;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use App\Repositories\BusinessTypeRepository;
use DB;
use Validator;
use App\Services\EcareerService;
use Illuminate\Http\Request;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class HomePageController extends Controller
{

    /**
     * @var ProductService
     */
    private $homeService;
    private $productService;
    private $quickLaunchService;
    private $ecarrerService;
    private $salesAndServicesService;
    /**
     * @var DynamicUrlRedirectionRepository
     */
    private $dynamicUrlRedirectionRepository;

    /**
     * HomePageController constructor.
     * @param HomeService $homeService
     * @param ProductService $productService
     * @param QuickLaunchService $quickLaunchService
     * @param EcareerService $ecarrerService
     * @param SalesAndServicesService $salesAndServicesService
     * @param DynamicUrlRedirectionRepository $dynamicUrlRedirectionRepository
     */
    public function __construct(
        HomeService $homeService,
        ProductService $productService,
        QuickLaunchService $quickLaunchService,
        EcareerService $ecarrerService,
        SalesAndServicesService $salesAndServicesService,
        DynamicUrlRedirectionRepository $dynamicUrlRedirectionRepository
    ) {
        $this->homeService = $homeService;
        $this->productService = $productService;
        $this->quickLaunchService = $quickLaunchService;
        $this->ecarrerService = $ecarrerService;
        $this->salesAndServicesService = $salesAndServicesService;
        $this->dynamicUrlRedirectionRepository = $dynamicUrlRedirectionRepository;
    }

    public function getHomePageData(Request $request)
    {
        // return $request->header('authorization');
        return $this->homeService->getComponents($request);
    }

    /**
     * Frontend dynamic route for seo tab
     * @return [type] [description]
     */
    public function frontendDynamicRoute()
    {
        $data = [];

        try {
            $parent_code = "ECareer";
            $parent_url = "/e-career";
            # eCarrer frontend route fro programs
            $ecarrer_data['code'] = $parent_code;
            $ecarrer_data['url'] = $parent_url;

            # eCarrer children data
            # programs routes
            $programs_slug = $this->ecarrerService->getProgramsAllTabTitle('life_at_bl_topbanner', 'programs', true);

            $extra_slug_data = [$programs_slug];

            $programs_child_data = $this->ecarrerService->getProgramsAllTabTitle('programs_top_tab_title');

            $programs_child_data_results = $this->formatDynamicRoute($programs_child_data, $parent_code, $parent_url,
                $extra_slug_data);

            # life at banglalink all top banner slug
            $top_banner_slug = $this->ecarrerService->getProgramsAllTabTitle('life_at_bl_topbanner');

            $top_banner_data_results = $this->formatDynamicRoute($top_banner_slug, $parent_code, $parent_url, null);

            if (!empty($top_banner_data_results)) {

                if (!empty($programs_child_data_results)) {
                    $child_data = array_merge($top_banner_data_results, $programs_child_data_results);
                } else {
                    $child_data = $top_banner_data_results;
                }
            } else {
                $child_data = null;
            }

            $ecarrer_data['children'] = $child_data;
            $data[] = $ecarrer_data;

            /**
             * Fetching dynamic url redirection data
             */
            $dynamicUrlRedirections = $this->dynamicUrlRedirectionRepository->getRedirections();
            $data['dynamic_redirections'] = $dynamicUrlRedirections->toArray();

            return response()->success($data, "Data Success");
        } catch (\Exception $e) {
            return response()->error('Route not found.', $e->getMessage());
        } catch (FatalThrowableError $e) {
            return response()->error('Internal server error.', $e->getMessage());
        }
    }

    /**
     * [formatDynamicRoute description]
     * @param $data
     * @param $parent_code
     * @param $parent_url
     * @param null $extra_slug_data
     * @return array|null [type]                  [description]
     */
    private function formatDynamicRoute($data, $parent_code, $parent_url, $extra_slug_data = null)
    {

        try {
            $results = null;
            if (is_array($data)) {

                if (!empty($extra_slug_data) && is_array($extra_slug_data)) {
                    $additional_url_slug = implode('/', $extra_slug_data);
                } else {
                    $additional_url_slug = null;
                }

                foreach ($data as $value) {

                    $sub_data = [];

                    $sub_data['code'] = $parent_code;

                    if (!empty($additional_url_slug)) {
                        $sub_data['url'] = $parent_url . '/' . $additional_url_slug . '/' . $value['url_slug'];
                    } else {
                        $sub_data['url'] = $parent_url . '/' . $value['url_slug'];
                    }
                    $sub_data['slug'] = $value['slug'];

                    $results[] = $sub_data;
                }
            }

            return $results;
        } catch (\Exception $e) {
            return response()->error('Internal server error.', $e->getMessage());
        } catch (FatalThrowableError $e) {
            return response()->error('Internal server error.', $e->getMessage());
        }
    }

}
