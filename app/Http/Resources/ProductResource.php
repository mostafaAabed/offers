<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'price' => $this->price,
            'category' => new ProductCategoryResource($this->productCategory),
        ];

        if($this->quantity)
        {
            $data['quantity'] = $this->quantity;
            $data['total'] = $this->price * $this->quantity - $this->discount;
        }

        if($this->discount)
        {
            $data['discount'] = $this->discount;
        }

        return $data;
    }
}
