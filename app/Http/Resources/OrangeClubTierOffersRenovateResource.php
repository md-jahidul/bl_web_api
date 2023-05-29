<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrangeClubTierOffersRenovateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $other_attributes = json_decode($this->other_attributes,true);

        return [
            'card_title_en' => $other_attributes['free_text_value_en'] ?? null,
            'card_title_bn' => $other_attributes['free_text_value_bn'] ?? null,
            'card_img' => $this->card_img,
            'validity_en' => $this->validity_en,
            'validity_bn' => $this->validity_bn,
            'btn_text_en' => $this->btn_text_en,
            'btn_text_bn' => $this->btn_text_bn,
            'partner_logo' => $this->company_logo ?? null,
            'partner_name_en' => $this->company_name_en ?? null,
            'partner_name_bn' => $this->company_name_bn ?? null,
            'category_tag_en' => $this->offer_type_en ?? null,
            'category_tag_bn' => $this->offer_type_bn ?? null,
            'url_slug_en' => $this->url_slug ?? null,
            'url_slug_bn' => $this->url_slug_bn ?? null,
            'schema_markup' => $this->schema_markup ?? null,
            'page_header_en' => $this->page_header ?? null,
            'page_header_bn' => $this->page_header_bn ?? null,
        ];
    }
}