<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyPlanProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @return array
     */
    public function toArray($request)
    {
        if ($this->data_volume_unit === 'gb') {
            $dataVolume = $this->data_volume * 1024;
        } else {
            $dataVolume = $this->data_volume;
        }

        return [
            'sim_type' => $this->sim_type,
            'content_type' => $this->content_type,
            'product_code' => $this->product_code,
            'renew_product_code' => $this->renew_product_code,
            'recharge_product_code' => $this->recharge_product_code,
            'sms' => $this->sms_volume,
            'minutes' => $this->minute_volume,
            'internet' => $dataVolume,
            'validity' => $this->validity,
            'tag' => $this->tag,
            'display_sd_vat_tax_en' => $this->display_sd_vat_tax_en,
            'display_sd_vat_tax_bn' => $this->display_sd_vat_tax_bn,
            'points' => (int) $this->points,
            'market_price' => $this->market_price,
            'price' => $this->discount_price,
            'savings_amount' => $this->savings_amount,
            'discount_percentage' => $this->discount_percentage
        ];
    }
}
