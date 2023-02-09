<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OclaResource extends JsonResource
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
            'id'                => $this->id ?? null,
            'quater'            => $this->quater ?? null,
            'quater_bn'         => $this->quater_bn ?? null,
            'year'              => $this->year ?? null,
            'year_bn'           => $this->year_bn ?? null,
            'image'             => config('filesystems.image_host_url').$this->image ?? null,
            'image_bn'          => config('filesystems.image_host_url').$this->image_bn ?? null,
            'image_alt'         => $this->image_alt ?? null,
            'image_alt_bn'      => $this->image_alt_bn ?? null,
        ];
    }
}
