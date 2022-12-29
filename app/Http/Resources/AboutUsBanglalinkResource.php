<?php

namespace App\Http\Resources;

use App\Traits\BindAttributeTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutUsBanglalinkResource extends JsonResource
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
            "id" => $this->id,
            "title" => $this->title,
            "title_bn" => $this->title_bn,
            "banglalink_info" => $this->banglalink_info,
            "banglalink_info_bn" => $this->banglalink_info_bn,
            "details_en" => $this->details_en,
            "details_bn" => $this->details_bn,
            "slug" => $this->slug,
            "schema_markup" => $this->schema_markup,
            "page_header" => $this->page_header,
            "page_header_bn" => $this->page_header_bn,
            "url_slug" => $this->url_slug,
            "url_slug_bn" => $this->url_slug_bn,
            "alt_text" => $this->alt_text,
            "alt_text_bn" => $this->alt_text_bn,
            "banner_name" => $this->banner_name,
            "banner_name_bn" => $this->banner_name_bn,
            "banner_image_mobile" => $this->banner_image_mobile,
            "banner_image" => $this->banner_image,
            "content_image" => $this->content_image,
            "content_img_name" => $this->content_img_name,
            "content_img_name_bn" => $this->content_img_name_bn,
            "content_img_alt_text" => $this->content_img_alt_text,
            "content_img_alt_text_bn" => $this->content_img_alt_text_bn,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];

        $data = $this->bindAttribute($this,$arr);
        return $data;

    }
}
