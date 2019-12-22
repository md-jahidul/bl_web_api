<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/22/19
 * Time: 11:11 AM
 */

namespace App\Repositories;


use App\Models\Product;

class SearchRepository
{
    public function getSearchResult($keyWord)
    {
        return Product::selectRaw('products.*, product_details.details_en, product_details.details_bn, 
        product_details.offer_details_bn, product_details.offer_details_en')
            ->whereRaw("MATCH(products.name_bn, products.name_en) AGAINST(? IN NATURAL LANGUAGE MODE)", $keyWord)
            ->leftJoin('product_details', 'products.id', '=', 'product_details.product_id')->get();
    }
}
