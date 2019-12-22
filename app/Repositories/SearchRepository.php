<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/22/19
 * Time: 11:11 AM
 */

namespace App\Repositories;


use App\Models\Menu;
use App\Models\PartnerOffer;
use App\Models\Product;

class SearchRepository
{
    public function getSearchResult($keyWord)
    {
        return ['product_offers' => $this->getProductResult($keyWord), 'partner_offers' => $this->getPartnerOfferResult($keyWord),
            'menus' => $this->getMenuResult($keyWord)];
    }

    public function getProductResult($keyWord)
    {
        return Product::selectRaw('products.*, product_details.details_en, product_details.details_bn, 
        product_details.offer_details_bn, product_details.offer_details_en')
            ->whereRaw("MATCH(products.name_bn, products.name_en) AGAINST(? IN NATURAL LANGUAGE MODE) 
            OR MATCH(product_details.details_en, product_details.details_bn, 
        product_details.offer_details_bn, product_details.offer_details_en) AGAINST(? IN NATURAL LANGUAGE MODE)", [$keyWord, $keyWord])
            ->leftJoin('product_details', 'products.id', '=', 'product_details.product_id')->get();
    }

    public function getMenuResult($keyWord)
    {
        return Menu::whereRaw("MATCH(en_label_text, en_label_text) AGAINST(? IN NATURAL LANGUAGE MODE)", $keyWord)->get();
    }

    public function getPartnerOfferResult($keyWord)
    {
        return PartnerOffer::selectRaw('partner_offers.*, partner_offer_details.details_en, partner_offer_details.details_bn, 
        partner_offer_details.offer_details_bn, partner_offer_details.offer_details_en')
            ->whereRaw("MATCH(partner_offer_details.details_en, partner_offer_details.details_bn, 
        partner_offer_details.offer_details_bn, partner_offer_details.offer_details_en) AGAINST(? IN NATURAL LANGUAGE MODE)", $keyWord)
            ->join('partner_offer_details', 'partner_offers.id', '=', 'partner_offer_details.partner_offer_id')->get();
    }
}
