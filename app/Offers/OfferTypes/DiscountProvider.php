<?php

namespace App\Offers\OfferTypes;

class DiscountProvider {
    
    protected $products;

    protected $discount;

    protected $offers;

    public function __construct()
    {
        $this->products = collect([]);
        $this->discount = collect([]);
    }

    public function forProducts($products)
    {
        $this->products = $products;
        return $this;
    }

    public function getOffers()
    {
        $this->calculateDiscount($this->products);
        return $this->discount;
    }

    private function calculateDiscount($products)
    {
        foreach($products as $product)
        {
            $offerDiscount = $product->productCategory->offers->where('active', true)->first() ? optional($product->productCategory->offers->where('active', true)->first()->intAttr->where('name', 'discount')->first())->value : 0;
            $product->discount = $product->price * $product->quantity * $offerDiscount / 100;
            $this->discount->push($product);
        }
    }

}