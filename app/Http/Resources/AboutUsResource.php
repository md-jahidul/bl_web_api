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
            'slug'                    => $this->slug ?? null,
            'title_en'                => $this->title ?? null,
            'title_bn'                => $this->title_bn ?? null,
            'banglalink_info_en'      => $this->banglalink_info ?? null,
            'banglalink_info_bn'      => $this->banglalink_info_bn ?? null,
            'details_en'              => $this->details_en ?? null,
            'details_bn'              => $this->details_bn ?? null,
            'schema_markup'           => $this->schema_markup ?? null,
            'page_header'             => $this->page_header ?? null,
            'page_header_bn'             => $this->page_header_bn ?? null,
            'url_slug'                => $this->url_slug ?? null,
            'url_slug_bn'             => $this->url_slug_bn ?? null,
            'banner_image_web_en'     => $this->banner_image_web_en ?? null,
            'banner_image_web_bn'     => $this->banner_image_web_bn ?? null,
            'banner_image_mobile_en'  => $this->banner_image_mobile_en ?? null,
            'banner_image_mobile_bn'  => $this->banner_image_mobile_bn ?? null,
            'alt_text'                => $this->alt_text ?? null,
            'alt_text_bn'             => $this->alt_text_bn ?? null,
            'image_url_en'            => $this->image_url_en ?? null,
            'image_url_bn'            => $this->image_url_bn ?? null,
            'content_img_alt_text'    => $this->content_img_alt_text ?? null,
            'content_img_alt_text_bn' => $this->content_img_alt_text_bn ?? null,
        ];

    }
}
