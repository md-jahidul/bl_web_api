<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\LoyaltyTier;

class LoyaltyTierRepository extends BaseRepository
{
    public $modelName = LoyaltyTier::class;

    public function offerByTier()
    {
        return $this->model->where('status', 1)
            ->select(
                'id',
                'title_en',
                'title_bn',
                'slug'
            )
            ->with(['partnerOffers' => function ($q){
                $q->select(
                    'partner_id',
                    'partner_category_id',
                    'loyalty_tier_id',
                    'card_img',
                    'validity_en',
                    'validity_bn',
                    'offer_unit',
                    'offer_value',
                    'offer_scale'
                )
                ->with([ 'partner' => function ($q){
                    $q->select('id', 'company_name_en', 'company_name_bn');
                }]);
            }])
            ->get();
    }
}
