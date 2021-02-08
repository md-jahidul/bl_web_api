<?php


namespace App\Services;

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
     * @var ImageFileViewerService
     */
    protected $imageFileViewerService;

    /**
     * AboutPageService constructor.
     * @param CustomerService $customerService
     * @param AppServiceTabRepository $appServiceTabRepository
     * @param AppServiceCategoryRepository $appServiceCategoryRepository
     * @param AppServiceProductRepository $appServiceProductRepository
     * @param AppServiceBookmarkRepository $appServiceBookmarkRepository
     * @param ImageFileViewerService $imageFileViewerService
     */
    public function __construct(
        CustomerService $customerService,
        AppServiceTabRepository $appServiceTabRepository,
        AppServiceCategoryRepository $appServiceCategoryRepository,
        AppServiceProductRepository $appServiceProductRepository,
        AppServiceBookmarkRepository $appServiceBookmarkRepository,
        ImageFileViewerService $imageFileViewerService
    ) {
        $this->customerService = $customerService;
        $this->appServiceTabRepository = $appServiceTabRepository;
        $this->appServiceCategoryRepository = $appServiceCategoryRepository;
        $this->appServiceProductRepository = $appServiceProductRepository;
        $this->appServiceBookmarkRepository = $appServiceBookmarkRepository;
        $this->imageFileViewerService = $imageFileViewerService;
    }

    /**
     * @return JsonResponse
     */
    public function appServiceData()
    {
        $tabs = $this->appServiceTabRepository->appServiceCollection();

        $data = [];
        $keyData = config('filesystems.moduleType.AppServiceTab');
        $i = 0;

        foreach ($tabs as $tab) {
            $imgData = $this->imageFileViewerService->prepareImageData($tab, $keyData);

            $categories = $this->getCategoriesByTab($tab->id);

            unset($tab->banner_image_url, $tab->banner_image_mobile);

            $data[$i] = array_merge($tab->toArray(), $imgData);;
            $data[$i]['categories'] = $categories;

            $i++;
        }

        return $this->sendSuccessResponse($data,'Internet packs list', config('filesystems.image_host_url'));
    }

    public function getCategoriesByTab($tabId)
    {
        $catList = [];
        $categories = $this->appServiceCategoryRepository->getCategoriesByTab($tabId);

        foreach ($categories as $category) {
            $products = $this->getProductsByCategory($category->id);
            $category->products = $products;
            $catList[] = $category;
        }

        return $catList;
    }

    public  function getProductsByCategory($catId)
    {
        $productList = [];
        $products = $this->appServiceProductRepository->getProductsByCategory($catId);
        $keyData = config('filesystems.moduleType.AppServiceProduct');

        foreach ($products as $product) {
            $imgData = $this->imageFileViewerService->prepareImageData($product, $keyData);
            unset($product->product_img_url, $product->product_img_en, $product->product_img_bn);
            $product = array_merge($product->toArray(), $imgData);

            $productList[] = (object) $product;
        }

        return $productList;
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
