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
            'id'                            => $this->id ?? null,
            'banglalink_info_en'            => $this->banglalink_info ?? null,
            'banglalink_info_bn'            => $this->banglalink_info_bn ?? null,
            'banner_image'                  => env('IMAGE_HOST_URL') . $this->banner_image ?? null,
            'content_image'                 => env('IMAGE_HOST_URL') . $this->content_image ?? null
            ];

    }
}
