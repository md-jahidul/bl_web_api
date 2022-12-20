<?php

namespace App\Http\Resources;
use App\Traits\BindAttributeTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class ShortcodeResource extends JsonResource
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
            "component_title" => $this->component_title ??  null,
            "component_typer" => $this->component_type ??  null,
            "title_en" => $this->title_en ?? null,
            "title_bn" => $this->title_bn ?? null,
        ];

        $data = $this->bindAttribute($this,$arr);

        return $data;
    }
}
