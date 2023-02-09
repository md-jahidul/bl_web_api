<?php

namespace App\Http\Resources;

use App\Models\AlSliderImage;
use App\Traits\BindAttributeTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class SubFooterResource extends JsonResource
{
    use BindAttributeTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $arr = [
            "title_en" => $this->title_en ?? null,
            "title_bn" => $this->title_bn ?? null,
        ];

        $data = $this->bindAttribute($this,$arr);

        $data['data']=SliderImageResource::collection(AlSliderImage::where('slider_id',12)->get());

        return $data;
    }
}
