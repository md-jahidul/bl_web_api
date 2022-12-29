<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
            "title_en" => $this->title_en,
            "title_bn" => $this->title_en,
            "description_en" => $this->short_details_en,
            "description_bn" => $this->short_details_en,
            "blog_image" => $this->thumbnail_image,
            "alt_text_en" => $this->alt_text_en,
            "alt_text_bn" => $this->alt_text_bn,
            "publish_at" => $this->date,
        ];
    }
}
