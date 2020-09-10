<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PartnerOfferResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        if (!empty($this->phone)) {
            $phone = json_decode($this->phone);
        }
        if (!empty($this->location)) {
            $location = json_decode($this->location);
        }

        $offerScaleEn = array('Upto' => "Up To", 'Minimum' => 'Minimum', 'Fixed' => 'Fixed');
        $offerScaleBn = array('Upto' => "সর্বোচ্চ", 'Minimum' => 'নূন্যতম', 'Fixed' => 'ফিক্সড');

        return [
            "id" => $this->id ?? null,
            "partner_id" => $this->partner_id ?? null,
            "product_code" => $this->product_code ?? null,
            "area_en" => $this->area_en ?? null,
            "area_bn" => $this->area_bn ?? null,
            "phone_en" => $phone->en ?? null,
            "phone_bn" => $phone->bn ?? null,
            "location_en" => $location->en ?? null,
            "location_bn" => $location->bn ?? null,
            "map_link" => $this->map_link ?? null,
            "validity_en" => $this->validity_en ?? null,
            "validity_bn" => $this->validity_bn ?? null,
            "offer_unit" => $this->offer_unit ?? null,
            "offer_value" => $this->offer_value ?? null,
            "offer_scale_en" => $offerScaleEn[$this->offer_scale] ?? null,
            "offer_scale_bn" => $offerScaleBn[$this->offer_scale] ?? null,
            "get_offer_msg_en" => $this->get_offer_msg_en ?? null,
            "get_offer_msg_bn" => $this->get_offer_msg_bn ?? null,
            "btn_text_en" => $this->btn_text_en ?? null,
            "btn_text_bn" => $this->btn_text_bn ?? null,
            "campaign_img" => (!empty($this->campaign_img)) ? config("filesystems.image_host_url") . $this->campaign_img : null,
            "is_campaign" => $this->is_campaign ?? null,
            "show_in_home" => $this->show_in_home ?? null,
            "like" => $this->like,
            "is_active" => $this->is_active,
            "display_order" => $this->display_order,
            "campaign_order" => $this->campaign_order,
            "other_attributes" => $this->other_attributes ?? null,
            "offer_type_en" => $this->offer_type_en ?? null,
            "offer_type_bn" => $this->offer_type_bn ?? null,
            "company_name_en" => $this->company_name_en ?? null,
            "company_name_bn" => $this->company_name_bn,
            "company_logo" => (!empty($this->company_logo)) ? config("filesystems.image_host_url") . $this->company_logo : null
        ];
    }

}
