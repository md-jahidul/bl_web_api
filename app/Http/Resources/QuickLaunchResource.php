<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuickLaunchResource extends JsonResource
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
            "id" =>  $this->id ?? null,
            "title_en" => $this->title_en ?? null,
            "title_bn" => $this->title_bn ?? null,
            'image_url_en' => $this->image_url_en ?? null,
            'image_url_bn' => $this->image_url_bn ?? null,
            "alt_text" => $this->alt_text ?? null,
            "alt_text_bn" => $this->alt_text_bn ?? null,
            "link" => $this->link ?? null,
            "link_bn" => $this->link_bn ?? null,
            "is_external_link" => $this->is_external_link ?? null,
            "slug" => $this->slug ?? null,
            "status"  => $this->status ?? null ,
            "display_order" => $this->display_order ?? null ,
        ];
    }
}
