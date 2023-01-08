<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PartnerOfferDetailsResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $data = isset($this['other_attributes']) ? json_decode($this['other_attributes'], true) : null;
        $phone = json_decode($this['phone']);
        $location = json_decode($this['location']);
        return [
            'card_title_en'         => $data['free_text_value_en'] ?? null,
            'card_title_bn'         => $data['free_text_value_bn'] ?? null,
            "partner_logo"          => $this['company_logo'] ?? null,
            "partner_name_en"       => $this['company_name_en'] ?? null,
            "partner_name_bn"       => $this['company_name_bn'] ?? null,
            "validity_en"           => $this['validity_en'] ?? null,
            "validity_bn"           => $this['validity_bn'] ?? null,
            "offer_unit"            => $this['offer_unit'] ?? null,
            "offer_value"           => $this['offer_value'] ?? null,
            "offer_scale"           => $this['offer_scale'] ?? null,
            'category_tag_en'       => $this['offer_type_en'] ?? null,
            'category_tag_bn'       => $this['offer_type_bn'] ?? null,
            'offer_details_en'      => $this['offer_details_en'] ?? null,
            'offer_details_bn'      => $this['offer_details_en'] ?? null,
            'eligible_customer_en'  => $this['eligible_customer_en'] ?? null,
            'eligible_customer_bn'  => $this['eligible_customer_bn'] ?? null,
            'phone_en'              => !empty($phone) ? $phone->en : "",
            'phone_bn'              => !empty($phone) ? $phone->bn : "",
            'location_en'           => !empty($location) ? $location->en : "",
            'location_bn'           => !empty($location) ? $location->bn : "",
            'area_en'               => $this['offer_type_en'] ?? null,
            'area_bn'               => $this['offer_type_bn'] ?? null,
            'offer_avail_en'        => $this['get_offer_msg_en'] ?? null,
            'offer_avail_bn'        => $this['get_offer_msg_bn'] ?? null,
            'banner_image_url'      => $this['banner_image_url'] ?? null,
            'company_website'       => $this['company_website'],
            'map_link'              => $this['map_link'] ?? null,
        ];
    }

}
