<?php


namespace App\Services;


use App\Http\Resources\AppServiceResource;
use App\Repositories\AppServiceBookmarkRepository;
use App\Repositories\AppServiceCategoryRepository;
use App\Repositories\AppServiceProductRepository;
use App\Repositories\AppServiceTabRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\JsonResponse;

class AppAndService extends ApiBaseService
{
    use CrudTrait;

    const SAVE = "save";
    const DELETE = "delete";

    /**
     * @var $aboutPageRepository
     */
    protected $appServiceTabRepository;
    protected $appServiceCategoryRepository;
    protected $appServiceProductRepository;

    /**
     * @var CustomerService
     */
    protected $customerService;

    /**
     * @var AppServiceBookmarkRepository
     */
    protected $appServiceBookmarkRepository;

    /**
     * AboutPageService constructor.
     * @param CustomerService $customerService
     * @param AppServiceTabRepository $appServiceTabRepository
     * @param AppServiceCategoryRepository $appServiceCategoryRepository
     * @param AppServiceProductRepository $appServiceProductRepository
     * @param AppServiceBookmarkRepository $appServiceBookmarkRepository
     */
    public function __construct(
        CustomerService $customerService,
        AppServiceTabRepository $appServiceTabRepository,
        AppServiceCategoryRepository $appServiceCategoryRepository,
        AppServiceProductRepository $appServiceProductRepository,
        AppServiceBookmarkRepository $appServiceBookmarkRepository
    ) {
        $this->customerService = $customerService;
        $this->appServiceTabRepository = $appServiceTabRepository;
        $this->appServiceCategoryRepository = $appServiceCategoryRepository;
        $this->appServiceProductRepository = $appServiceProductRepository;
        $this->appServiceBookmarkRepository = $appServiceBookmarkRepository;
    }

    /**
     * @return JsonResponse
     */
    public function appServiceData()
    {
        $data = $this->appServiceTabRepository->appServiceCollection();
        return $this->sendSuccessResponse($data,'Internet packs list', config('filesystems.image_host_url'));
    }


    public function packageList($provider)
    {
        $data = $this->appServiceProductRepository->findByProperties(['provider_url' => $provider], ['id', 'name_en', 'name_bn', 'provider_url', 'validity_unit', 'price_tk']);
        return $this->sendSuccessResponse($data,'VAS package list');
    }

    public function customerAppServiceBookmark($request)
    {
        $customerInfo = $this->customerService->getCustomerDetails($request);

        $operationType = $request->operation_type;
        $productId = $request->product_id;

        if ($operationType == self::SAVE) {
            $this->appServiceBookmarkRepository->save([
                'mobile' => $customerInfo->phone,
                'product_id' => (int)$productId,
            ]);
            return $this->sendSuccessResponse([], 'Bookmark saved successfully!');
        } else if ($operationType == self::DELETE) {
            $bookmarkProducts = $this->appServiceBookmarkRepository->findByProperties(['mobile' => $customerInfo->phone]);
            foreach ($bookmarkProducts as $bookmarkProduct) {
                if ($bookmarkProduct->product_id == $productId) {
                    $bookmarkProduct->delete();
                    return $this->sendSuccessResponse([], 'Bookmark removed successfully!');
                }
            }
        }
        return $this->sendErrorResponse('Invalid operation');
    }

    public function like($productId)
    {
        $product = $this->appServiceProductRepository->findOneByProperties(['id' => $productId]);
        $product['like'] = $product['like']+ 1;
        $product->update();
        return $this->sendSuccessResponse([], 'Product liked successfully!');
    }
}
