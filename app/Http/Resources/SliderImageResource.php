<?php

namespace App\Http\Resources;

use App\Traits\BindAttributeTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderImageResource extends JsonResource
{
    use BindAttributeTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        return $this->sliderImages;
        $arr =  [
            'id'                  => $this->id ?? null,
            'title_en'            => $this->title_en ?? null,
            'title_bn'            => $this->title_bn ?? null,
            'image_url'           => $this->image_url ?? null,
            'mobile_view_img'     => ($this->mobile_view_img) ? $this->mobile_view_img : null,
            'alt_text'            => $this->alt_text ?? null,
            'alt_text_bn'            => $this->alt_text_bn ?? null,
            'description_bn'            => $this->description_bn ?? null,
            'description_en'            => $this->description_en ?? null,
            'display_order'            => $this->display_order ?? null,
            'is_active'            => $this->is_active ?? null,
            //'other_attributes'    => $this->other_attributes ?? null,
        ];

        $data = $this->bindAttribute($this,$arr);
        return $data;
    }
}
