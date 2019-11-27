<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PartnerOfferResource extends JsonResource
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
            "partner_id" => $this->partner_id ?? null,
            "validity_en" => $this->validity_en ?? null,
            "validity_bn" => $this->validity_bn ?? null,
            "offer_en" => $this->offer_en ,
            "offer_bn" => $this->offer_bn,
            "get_offer_msg_en" => $this->get_offer_msg_en ?? null,
            "get_offer_msg_bn" => $this->get_offer_msg_bn ?? null  ,
            "btn_text_en"  => $this->btn_text_en ?? null ,
            "btn_text_bn" => $this->btn_text_bn ?? null ,
            "campaign_img" => ($this->campaign_img != '') ? env("APP_URL") . "/" . $this->campaign_img : null,
            "is_campaign" => $this->is_campaign ?? null,
            "show_in_home" => $this->show_in_home ?? null,
            "is_active" => $this->is_active,
            "display_order" => $this->display_order,
            "campaign_order" => $this->campaign_order,
            "other_attributes" => $this->other_attributes ?? null,
            "offer_type_en" => $this->offer_type_en ?? null,
            "offer_type_bn" => $this->offer_type_bn ?? null,
            "company_name_en" => $this->company_name_en ?? null,
            "company_name_bn" =>  $this->company_name_bn,
            "company_logo" => $this->company_logo ?? null
            // created_at: "2019-11-26 13:35:38", Optional
            // updated_at: "2019-11-26 13:35:38", Optional
        ];
    }
}
