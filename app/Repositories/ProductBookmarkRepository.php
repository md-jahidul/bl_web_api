<?php
namespace App\Repositories;

use App\Models\AlProductBookmark;
use App\Models\PartnerOffer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductBookmarkRepository extends BaseRepository
{
    /**
     * @var string
     */
    public $modelName = AlProductBookmark::class;


    /**
     * @param $customerMobile
     * @param $request
     */
    public function saveProduct($customerMobile, $request)
    {
        if ($request->operation_type == "save"){
            $this->model->updateOrcreate([
                'mobile' => $customerMobile,
                'product_code' => $request->product_code,
            ]);
        }else if ($request->operation_type == "delete"){
           $product = $this->model->where('product_code', $request->product_code);
           $product->delete();
        }
    }
}
