<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyOfferCatRenovateResource extends JsonResource
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
            'id' => $this->id,
            'name_en' => $this->name_en,
            'name_bn' => $this->name_bn,
            'url_slug_en' => $this->url_slug_en,
            'url_slug_bn' => $this->url_slug_bn,
            'page_header' => $this->page_header,
            'page_header_bn' => $this->page_header_bn,
            'schema_markup' => $this->schema_markup,
            //'offers' => OrangeClubTierOffersRenovateResource::collection($all),
            //'count' =>$this->partner_offers_count
        ];
    }
}
