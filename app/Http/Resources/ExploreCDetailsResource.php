<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExploreCDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            // 'id' => $this->id ?? null,
            'section_details_id' => $this->section_details_id ?? null,
            'page_type' => $this->page_type ?? null,
            'component_type' => $this->component_type ?? null,
            'title_en' => $this->title_en ?? null,
            'title_bn' => $this->title_bn ?? null,
            'editor_en' => $this->editor_en ?? null,
            'editor_bn' => $this->editor_bn ?? null,
            'description_en' => $this->description_en ?? null,
            'description_bn' => $this->description_bn ?? null,
            'extra_title_bn' => $this->extra_title_bn ?? null,
            'extra_title_en' => $this->extra_title_en ?? null,
            'image' => (!empty($this->image)) ? $this->image : null,
            'alt_text' => $this->img_alt_en ?? null,
            'alt_text' => $this->img_alt_bn ?? null,
            'multiple_attributes' => $this->multiple_attributes ?? null,
            // 'other_attributes' => $this->other_attributes ?? null,
            // 'component_order' => $this->component_order ?? null,
            // 'status' => $this->status ?? null,
            // 'details_url' => route('explore-c-details', ['explore_c_id' => $this->id]),
        ];
    }
}
