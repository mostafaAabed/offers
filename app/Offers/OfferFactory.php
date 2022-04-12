<?php

namespace App\Offers;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\OfferCategory;

Class OfferFactory {

    /**
     * This class is responsible for 
     * backward compatiblity with db files
     * 
     */

    protected $products;
    protected $offers;
    protected $productsAfterOffer;
    protected $productsOutsideOffer;

    

    /**
     * constructor
     * @param Array $products with keys id, quantity 
     */
    public function __construct()
    {
        $this->productsAfterOffer = collect([]);
        $this->initOffers();
    }

    public function forProducts($products)
    {
        $productsCollection = collect($products);
        $products = Product::whereIn('id', $productsCollection->pluck('id'))
            ->with('productCategory.offers.offerCategory')->get();
        $products->each(function($product, $i) use ($productsCollection){
            $product->quantity = $productsCollection[$i]['quantity'];
            $this->classifyProductToOffer($product);
        });

        $this->products = $products;
        return $this;
    }

    private function initOffers()
    {
        $offers = OfferCategory::get();
        foreach($offers as $offer)
        {
            $this->offers[$offer->name] = collect([]);
        }
    }

    protected function classifyProductToOffer($product)
    {
        $offer = $product->productCategory->offers->isNotEmpty() ? 
        $product->productCategory->offers->first()->offerCategory->name : NULL;

        if($offer){
            $products = $this->offers[$offer];
            $products []= $product;
            $this->offers[$offer] = $products;
        }else{
            $this->productsOutsideOffer[] = $product;
        }
    }

    public function getOffers()
    {
        $offers = array_keys($this->offers);
        $finalOffers = [];
        foreach($offers as $offer)
        {
            $class = '\App\Offers\OfferTypes\\'.ucfirst(Str::camel($offer))."Provider";
            $provider = new $class();
            $this->productsAfterOffer = $this->productsAfterOffer->merge($provider->forProducts($this->offers[$offer])->getOffers());

        }
        $products = $this->products;
        $total = 0;
        $discount = 0;
        foreach($products as $product)
        {
            $productAfterOffer = $this->productsAfterOffer->where('id', $product->id)->first();
            if($productAfterOffer)
            {
                $discount += $productAfterOffer->discount;
                $product->discount = $productAfterOffer->discount;
            }
            $total += $product->price * $product->quantity;
        }
        $total -= $discount;
        
        return [
            'products' => $products,
            'total' => $total,
            'discount' => $discount,
        ];
    }
}

