<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AboutUsResource extends JsonResource
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
            'id'                      => $this->id ?? null,
            'slug'                      => $this->slug ?? null,
            'title_en'                => $this->title ?? null,
            'title_bn'                => $this->title_bn ?? null,
            'banglalink_info_en'      => $this->banglalink_info ?? null,
            'banglalink_info_bn'      => $this->banglalink_info_bn ?? null,
            'details_en'              => $this->details_en ?? null,
            'details_bn'              => $this->details_bn ?? null,
            'banner_image'            => env('IMAGE_HOST_URL') . $this->banner_image ?? null,
            'banner_image_mobile'     => env('IMAGE_HOST_URL') . $this->banner_image_mobile ?? null,
            'alt_text'                => $this->alt_text ?? null,
            'schema_markup'           => $this->schema_markup ?? null,
            'page_header'             => $this->page_header ?? null,
            'url_slug'                => $this->url_slug ?? null,
            'content_image'           => env('IMAGE_HOST_URL') . $this->content_image ?? null
        ];

    }
}
