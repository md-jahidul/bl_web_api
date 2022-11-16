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
            'image_url'           => $this->image_url ?? null,
            'mobile_view_img'     => ($this->mobile_view_img) ? $this->mobile_view_img : null,
            'alt_text'            => $this->alt_text ?? null,
            'other_attributes'    => $this->other_attributes ?? null,
        ];
    }
}
