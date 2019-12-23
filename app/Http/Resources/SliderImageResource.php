<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SliderImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [];
            $data["id"] =  $this->id ?? null;
            $data["slider_id"]  = $this->slider_id ?? null;
            $data["title_en"] = $this->title_en ?? null;
            $data["title_bn"] = $this->title_bn ?? null;
            $data["start_date"] = $this->start_date ?? null;
            $data["end_date"] = $this->end_date ?? null;
            $data["image_url"] =  'ddddd' . $this->image_url;
            $data["alt_text"] = $this->alt_text ?? null;
            $data["display_order"] = $this->display_order ?? null;
            $data["is_active"]  = $this->is_active ?? null;
            dd($this->other_attributes);
//            foreach ($this->other_attributes as $key => $value) {
//                $data[$key]  = $value;
//            }

        return  $data;
    }
}
