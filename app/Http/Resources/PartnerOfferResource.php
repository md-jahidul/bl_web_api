<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PartnerOfferResource extends JsonResource
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
            'id'                 => $this->id ?? null,
            'title'              => $this->title ?? null,
            'description'        => $this->description ?? null,
            'short_code'         => $this->short_code ?? null,
            'slider_images'      => ImageResource::collection($this->sliderImages),
        ];
    }
}
