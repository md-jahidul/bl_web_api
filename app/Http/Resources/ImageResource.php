<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
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
            'id'                        => $this->id ?? null,
            'slider_id'                 => $this->slider_id ?? null,
            'description'               => $this->description ?? null,
            'image_url'                 => env('IMAGE_HOST') . "/" . $this->image_url ?? null,
            'alt_text'                  => $this->alt_text ?? null,
            'redirect_url'              => $this->url ?? null,
            'sequence'                  => $this->sequence ?? null,
            'is_active'                 => $this->is_active
        ];
    }
}
