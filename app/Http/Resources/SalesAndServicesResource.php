<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesAndServicesResource extends JsonResource
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
            "retail_code" => $this->cc_code ?? null,
            "retailer_name" => $this->cc_name ?? null,
            "district" => $this->district ?? null,
            "thana" => $this->thana ?? null,
            "address" => $this->address ?? null,
            "longitude" => $this->longitude ?? null,
            "latitude" => $this->latitude ?? null,
            "opening_time" => $this->opening_time ?? null,
            "closing_time" => $this->closing_time ?? null,
            "holiday" => $this->holiday ?? null,
            "half_holiday" => $this->half_holiday ?? null,
            "half_holiday_opening_time" => $this->half_holiday_opening_time ?? null,
            "half_holiday_closing_time" => $this->half_holiday_closing_time ?? null,
            "additional_info" => $this->additional_info ?? null,
            "isShow" => false,

            // "image_url" => (!empty($this->image_url)) ? env("IMAGE_HOST_URL") . $this->image_url : null,
        ];
    }
}
