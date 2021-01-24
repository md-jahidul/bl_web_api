<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SliderImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        return $this->sliderImages;

        return [
            'id'                  => $this->id ?? null,
            'title_en'            => $this->title_en ?? null,
            'title_bn'            => $this->title_bn ?? null,
            'banner_image_web_en'     => $this->banner_image_web_en ?? null,
            'banner_image_web_bn'     => $this->banner_image_web_bn ?? null,
            'banner_image_mobile_en'  => $this->banner_image_mobile_en ?? null,
            'banner_image_mobile_bn'  => $this->banner_image_mobile_bn ?? null,
            'alt_text'            => $this->alt_text ?? null,
            'alt_text_bn'            => $this->alt_text_bn ?? null,
            'other_attributes'    => $this->other_attributes ?? null,
        ];
    }
}
