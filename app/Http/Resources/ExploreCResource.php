<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExploreCResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            // 'id' => $this->id ?? null,
            'title_en' => $this->title_en ?? null,
            'title_bn' => $this->title_bn ?? null,
            'short_desc_en' => $this->short_desc_en ?? null,
            'short_desc_bn' => $this->short_desc_bn ?? null,
            'button_lable_en' => $this->button_lable_en ?? null,
            'button_lable_bn' => $this->button_lable_bn ?? null,
            'slug_en' => $this->slug_en ?? null,
            'slug_bn' => $this->slug_bn ?? null,
            // 'button_url_en' => route('explore-c-details', ['explore_c_slug' => $this->slug_en]) ?? null,
            // 'button_url_bn' => route('explore-c-details', ['explore_c_slug' => $this->slug_bn]) ?? null,
            'image' => (!empty($this->image)) ? $this->image : null,
            'image_mobile' => (!empty($this->image_mobile)) ? $this->image_mobile : null,
            'img_alt_en' => $this->img_alt_en ?? null,
            'img_alt_bn' => $this->img_alt_bn ?? null,
            'img_name_en' => $this->img_name_en ?? null,
            'img_name_bn' => $this->img_name_bn ?? null,
            // 'start_date' => $this->start_date ?? null,
            // 'end_date' => $this->end_date ?? null,
            // 'display_order' => $this->display_order ?? null,
            // 'status' => $this->status ?? null,
        ];
    }
}
