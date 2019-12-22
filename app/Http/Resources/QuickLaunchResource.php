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
            "image_url" => (!empty($this->image_url)) ? env("IMAGE_HOST_URL") . $this->image_url : null,
            "alt_text" => $this->alt_text ?? null,
            "link" => $this->link ?? null,
            "status"  => $this->status ?? null ,
            "display_order" => $this->display_order ?? null ,
        ];
    }
}
