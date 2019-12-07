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
            "left_side_img" => ($this->left_side_img != '') ? env("IMAGE_HOST_URL") . "/" . $this->left_side_img : null,
            "right_side_ing" =>($this->right_side_ing != '') ? env("IMAGE_HOST_URL") . "/" . $this->right_side_ing : null,
            "other_attributes" => $this->other_attributes,
            // created_at: "2019-11-26 13:35:38", Optional
            // updated_at: "2019-11-26 13:35:38", Optional
        ];
    }
}
