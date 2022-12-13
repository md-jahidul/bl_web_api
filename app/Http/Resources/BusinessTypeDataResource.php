<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessTypeDataResource extends JsonResource
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
            'title_en' => $this->title_en,
            'title_bn' => $this->title_bn,
            'description_en' => $this->description_en,
            'description_bn' => $this->description_bn,
            'image_en' => $this->image_en,
            'image_bn' => $this->image_bn,
            'alt_text_en' => $this->alt_text_en,
            'alt_text_bn' => $this->alt_text_bn,
            'label_btn_en' => $this->label_btn_en,
            'label_btn_bn' => $this->label_btn_bn,
            'url_en' => $this->url_en,
            'url_bn' => $this->url_bn,
        ];
    }
}
