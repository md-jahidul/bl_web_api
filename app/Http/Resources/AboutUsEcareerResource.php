<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AboutUsEcareerResource extends JsonResource
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
            'id'                  => $this->id ?? null,
            'title_en'            => "Career" ?? null,
            'title_bn'            => "ক্যারিয়ার" ?? null,
            'description_en'      => $this->description_en ?? null,
            'description_bn'      => $this->title_en ?? null,
            'is_active'           => $this->is_active ?? null,
            'portal_items'        =>  AboutUsEcareerItemResource::collection($this->aboutUsEcareerItems),
        ];

    }
}
