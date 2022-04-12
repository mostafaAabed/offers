<?php

namespace App\Http\Resources\Offer;

use App\Http\Resources\OfferCategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'active' => (int)$this->active,
            'offerCategory' => new OfferCategoryResource($this->offerCategory),
        ];

        foreach($this->intAttr as $attr)
        {
            $data[$attr->name] = $attr->value;
        }

        return $data;
    }
}
