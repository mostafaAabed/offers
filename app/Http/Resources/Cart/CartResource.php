<?php

namespace App\Http\Resources\Cart;

use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function __construct($products, $total, $discount)
    {
        parent::__construct($products);
        $this->total = $total;
        $this->discount = $discount;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'products' => $this->map(function($item){
                return new ProductResource($item);
            }),
            'total' => $this->total,
            'discount' => $this->discount,
        ];
    }
}
