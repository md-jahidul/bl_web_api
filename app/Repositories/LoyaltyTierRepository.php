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

    public function offerByTier($showInHome)
    {
        $data = $this->model->where('status', 1);

        if ($showInHome) {
            $data = $data->whereHas('partnerOffers', function ($query) {
                $query->where('show_in_home', 1);
            });
        } else {
            $data = $data->whereHas('partnerOffers');
        }

        return $data->select(
                'id',
                'title_en',
                'title_bn',
                'slug'
            )
            ->with(['partnerOffers' => function ($q) use($showInHome) {
                $q->where('is_active', 1);
                if ($showInHome){
                    $q->where('show_in_home', 1);
                }
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
