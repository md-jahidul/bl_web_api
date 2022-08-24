<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ManagementResource extends JsonResource
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
            'id'                            => $this->id ?? null,
            'name_en'                       => $this->name ?? null,
            'name_bn'                       => $this->name_bn ?? null,
            'designation_en'                => $this->designation ?? null,
            'designation_bn'                => $this->designation_bn ?? null,
            'profile_img_url_en'            => $this->profile_img_url_en ?? null,
            'profile_img_url_bn'            => $this->profile_img_url_bn ?? null,
            'profile_img_alt_text'          => $this->profile_img_alt_text ?? null,
            'profile_img_alt_text_bn'       => $this->profile_img_alt_text_bn ?? null,
            'banner_img_url_en'             => $this->banner_img_url_en ?? null,
            'banner_img_url_bn'             => $this->banner_img_url_bn ?? null,
            'banner_img_alt_text'           => $this->banner_img_alt_text ?? null,
            'banner_img_alt_text_bn'        => $this->banner_img_alt_text_bn ?? null,
            'personal_details_en'           => $this->personal_details ?? null,
            'personal_details_bn'           => $this->personal_details_bn ?? null,
            'twitter_link'                  => $this->twitter_link ?? null,
            'linkedin_link'                 => $this->linkedin_link ?? null,
            'facebook_link'                 => $this->facebook_link ?? null,
            'others_link'                   => $this->others_link ?? null,
            'display_order'                   => $this->display_order ?? null

        ];

    }
}
