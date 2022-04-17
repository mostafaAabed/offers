<?php

namespace App\Offers\OfferTypes;

use App\Models\Offer;
use App\Offers\Traits\OfferTrait;
use Illuminate\Support\Facades\DB;

class BuyGetProvider implements OfferProvider {

    use OfferTrait;

    protected $products;

    protected $discount;

    protected $offers;


    public function __construct()
    {
        $this->products = collect([]);
        $this->discount = collect([]);
        $this->offers = Offer::whereHas('offerCategory', function($query){
            $query->where('name', 'buy_get');
        })->where('active', true)->with(['intAttr', 'strAttr'])->get();
        $this->offers = $this->offers->sortByDesc(function($offer){
            return $offer->intAttr->where('name', 'buy')->first()->value + 
                $offer->intAttr->where('name', 'get')->first()->value;
        });
    }

    public function forProducts($products)
    {
        $this->products = $products->sortByDesc('price');
        return $this;
    }

    public function getOffers()
    {
        $this->calculateDiscount($this->products);
        return $this->discount;
    }

    private function calculateDiscount(&$products)
    {
        foreach($this->offers as $offer)
        {
            $buy = $offer->intAttr->where('name', 'buy')->first()->value;
            $get = $offer->intAttr->where('name', 'get')->first()->value;
            $discount = optional($offer->intAttr->where('name', 'discount')->first())->value;
            $buyDiscount = optional($offer->intAttr->where('name', 'buy_discount')->first())->value;
            $discountType = optional($offer->strAttr->where('name', 'discount_type')->first())->value;

            if($products->sum('quantity') < $buy + $get)
            {
                continue;
            }

            $this->removeBuy($buy, $products, $discountType, $buyDiscount);
            $this->removeGet($get, $products, $discountType, $discount);

            $this->calculateDiscount($products);
        }
    }

    private function removeBuy($buy, &$products, $discountType, $discount = 0)
    {
        foreach($products as $key => $product)
        {
            if($buy < $product->quantity)
            {
                $product->quantity = $product->quantity - $buy;
                if($discount){
                    $discountValue = $this->discountValue($buy, $product->price, $discountType, $discount);
                    $this->updateDiscount($product, $discountValue);
                }
                break;
            }else{
                $buy = $buy - $product->quantity;
                if($discount){
                    $discountValue = $this->discountValue($product->quantity, $product->price, $discountType, $discount);
                    $this->updateDiscount($product, $discountValue);
                }
                unset($products[$key]);
            }
        }
    }

    private function removeGet($get, &$products, $discountType, $offerDiscount)
    {
        foreach($products->sortBy('price') as $key => $product)
        {
            if($get < $product->quantity)
            {             
                $product->quantity = $product->quantity - $get;
                if($offerDiscount){
                    $discountValue = $this->discountValue($get, $product->price, $discountType, $offerDiscount);
                    $this->updateDiscount($product, $discountValue);
                }
                break;
            }else{
                $get = $get - $product->quantity;
                if($offerDiscount){
                    $discountValue = $this->discountValue($product->quantity, $product->price, $discountType, $offerDiscount);
                    $this->updateDiscount($product, $discountValue);
                }
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

}
