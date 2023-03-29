<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrangeClubTierResource extends JsonResource
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
            'title_en' => $this->title_en,
            'title_bn' => $this->title_bn,
            'slug' => $this->slug,
            'offers' => OrangeClubTierOffersResource::collection($this->partnerOffers)
        ];
    }
}
