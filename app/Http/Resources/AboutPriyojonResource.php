<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AboutPriyojonResource extends JsonResource
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
            "details_en" => $this->details_en ?? null,
            "details_bn" => $this->details_bn ?? null,
            "left_side_img" => ($this->left_side_img != '') ? config('filesystems.image_host_url') . $this->left_side_img : null,
            "right_side_ing" =>($this->right_side_ing != '') ? config('filesystems.image_host_url') . $this->right_side_ing : null,
            "other_attributes" => $this->other_attributes,
        ];
    }
}
