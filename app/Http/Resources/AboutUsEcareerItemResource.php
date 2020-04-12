<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AboutUsEcareerItemResource extends JsonResource
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
            'id'                  => $this->id ?? null,
            'title_en'            => $this->title_en ?? null,
            'title_bn'            => $this->title_bn ?? null,
            'description_en'      => $this->description_en ?? null,
            'description_bn'      => $this->description_bn ?? null,
            'is_active'           => $this->is_active ?? null,
            'link'           => $this->alt_links ?? null,
            'image'               => env('IMAGE_HOST_URL') . "/" . $this->image ?? null,

        ];

    }
}
