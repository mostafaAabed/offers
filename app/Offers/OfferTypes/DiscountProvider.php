<?php

namespace App\Offers\OfferTypes;

use App\Offers\Traits\OfferTrait;
use App\Offers\OfferTypes\OfferProvider;

class DiscountProvider implements OfferProvider {

    use OfferTrait;
    
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
            $offerDiscount = $product->productCategory->offers->where('active', true)->first() ? 
                optional($product->productCategory->offers->where('active', true)->first()->intAttr->where('name', 'discount')->first())->value : 0;
            $discountType = $product->productCategory->offers->where('active', true)->first() ? 
                optional($product->productCategory->offers->where('active', true)->first()->strAttr->where('name', 'discount_type')->first())->value : 'percentage';
            $product->discount = $this->discountValue($product->quantity, $product->price, $discountType, $offerDiscount);
            $this->discount->push($product);
        }
    }

}