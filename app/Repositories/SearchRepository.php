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
        return Product::whereRaw("MATCH(name_bn, name_en) AGAINST(? IN NATURAL LANGUAGE MODE)", $keyWord)->get();
    }
}
