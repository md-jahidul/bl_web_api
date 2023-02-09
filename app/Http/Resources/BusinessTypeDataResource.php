<?php

namespace App\Http\Resources;

use App\Traits\BindAttributeTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessTypeDataResource extends JsonResource
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
        $arr =  [
            'id' => $this->id,
            'title_en' => $this->title_en,
            'title_bn' => $this->title_bn,
            'image_url' => $this->image_url,
            'mobile_view_img' => $this->mobile_view_img,
            'alt_text_en' => $this->alt_text_en,
            'alt_text_bn' => $this->alt_text_bn,
        ];
        $data = $this->bindAttribute($this,$arr);
        return $data;
    }
}
