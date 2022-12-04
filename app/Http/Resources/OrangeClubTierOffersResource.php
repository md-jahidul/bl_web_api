<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrangeClubTierOffersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'card_img' => $this->card_img,
            'validity_en' => $this->validity_en,
            'validity_bn' => $this->validity_bn,
            'offer_unit' => $this->offer_unit,
            'offer_value' => $this->offer_value,
            'offer_scale' => $this->offer_scale,
            'partner_name_en' => $this->partner->company_name_en ?? null,
            'partner_name_bn' => $this->partner->company_name_bn ?? null,
        ];
    }
}
