<?php

namespace App\Offers\OfferTypes;

use App\Models\Offer;
use App\Offers\Traits\OfferTrait;
use Illuminate\Support\Facades\DB;

class BundleProvider implements OfferProvider {

    use OfferTrait;

    protected $products;
    
    protected $offers;

    protected $discount;


    public function __construct()
    {
        $this->products = collect([]);
        $this->discount = collect([]);
        
        $this->offers = Offer::where('active', true)->whereHas('offerCategory', function($query){
            $query->where('name', 'bundle');
        })->get()->sortByDesc(function($offer){
            return $offer->intAttr->where('name', 'buy')->first()->value;
        });
    }

    public function forProducts($products)
    {
        $products->sortByDesc('price')->each(function($product, $i){
            $this->classifyProductToOffer($product);
        });
        
        return $this;
    }

    public function getOffers()
    {
        $this->calculateDiscount();
        return $this->discount;
    }

    private function calculateDiscount()
    {
        foreach($this->offers as $offer)
        {
            $buy = $offer->intAttr->where('name', 'buy')->first()->value;
            $price = $offer->intAttr->where('name', 'price')->first()->value;

            if(!$offer->products || $offer->products->sum('quantity') < $buy)
            {
                continue;
            }

            $priceInOffer = $price / $buy;

            $this->removeBuy($buy, $offer->products, $priceInOffer);

            $this->calculateDiscount();
        }
    }

    private function removeBuy($buy, &$products, $priceInOffer)
    {
        foreach($products as $key => $product)
        {
            if($buy < $product->quantity)
            {
                $product->quantity = $product->quantity - $buy;
                $discountValue = $this->discountValue($buy, $product->price, 'fixed', $product->price - $priceInOffer);
                $this->updateDiscount($product, round($discountValue));
                break;
            }else{
                $buy = $buy - $product->quantity;
                $discountValue = $this->discountValue($product->quantity, $product->price, 'fixed', $product->price - $priceInOffer);
                $this->updateDiscount($product, round($discountValue));
                $product->quantity = 0;
                unset($products[$key]);
            }
        }
    }

    private function updateDiscount($product, $discount)
    {
        if($this->discount->where('id', $product->id)->count()){   
            $this->discount = $this->discount->map(function($item) use ($product, $discount){
                if($item->id == $product->id){
                    $newItem = $item;
                    $newItem->discount += $discount;
                    return $newItem;
                }else{
                    return $item;
                }
            });
        }else{
            $product->discount = $discount;
            $this->discount->push($product);
        }
    }

    protected function classifyProductToOffer($product)
    {
        foreach($product->productCategory->offers as $offer){
            if($offer->active){
                $i = $this->offers->search(function($item, $key) use ($offer){
                    return $item->id == $offer->id;
                });
                if($i !== false){
                    $products = $this->offers[$i]->products ?: collect([]);
                    $products []= $product;
                    $this->offers[$i]->products = $products;
                }
            }
        }
    }

}
