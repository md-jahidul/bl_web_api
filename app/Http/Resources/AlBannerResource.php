<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AlBannerResource extends JsonResource
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
            'id' => $this->id ?? null,
            'section_id' => $this->section_id ?? null,
            'section_type' => $this->section_type ?? null,
            'title_en' => $this->title_en ?? null,
            'title_bn' => $this->title_bn ?? null,
            'desc_en' => $this->desc_en ?? null,
            'desc_bn' => $this->desc_bn ?? null,
            'alt_text_en' => $this->alt_text_en ?? null,
            'alt_text_bn' => $this->alt_text_bn ?? null,
            'image' => (!empty($this->image)) ? config("filesystems.image_host_url") . $this->image : null,
            'image_name_en' => $this->image_name_en ?? null,
            'image_name_bn' => $this->image_name_bn ?? null,
            'other_attributes' => $this->other_attributes ?? null,
        ];
    }
}
