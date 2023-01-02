<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\PartnerCategory;

class PartnerOfferCategoryRepository extends BaseRepository
{
    public $modelName = PartnerCategory::class;

    public function loyaltyCatOffers()
    {
        return $this->model->where('status', 1)
            ->whereHas('partnerOffers')
            ->select(
                'id',
                'name_en',
                'name_bn',
                'url_slug_en',
                'url_slug_bn',
                'page_header',
                'page_header_bn',
                'schema_markup'
            )
            ->with(['partnerOffers' => function ($q) {
                $q->where('is_active', 1);
                $q->select(
                    'id',
                    'partner_id',
                    'partner_category_id',
                    'loyalty_tier_id',
                    'card_img',
                    'validity_en',
                    'validity_bn',
                    'btn_text_en',
                    'btn_text_bn',
                    'url_slug',
                    'url_slug_bn',
                    'page_header',
                    'page_header_bn',
                    'schema_markup',
                    'other_attributes'
                )
                ->with([ 'partner' => function ($q){
                    $q->select('id', 'company_name_en', 'company_name_bn');
                }]);
            }])
            ->get();
    }
}
