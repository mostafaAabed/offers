<?php

namespace App\Offers\OfferTypes;

use App\Models\Offer;
use Illuminate\Support\Facades\DB;

class BuyGetProvider implements OfferProvider {

    protected $products;

    protected $discount;

    protected $offers;

    public function __construct()
    {
        $this->products = collect([]);
        $this->discount = collect([]);
        $this->offers = Offer::whereHas('offerCategory', function($query){
            $query->where('name', 'buy_get');
        })->where('active', true)->with('intAttr')->get();
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

            if($products->sum('quantity') < $buy + $get)
            {
                continue;
            }

            $this->removeBuy($buy, $products);
            $this->removeGet($get, $products, $discount);

            $this->calculateDiscount($products);
        }
    }

    private function removeBuy($buy, &$products)
    {
        foreach($products as $key => $product)
        {
            if($buy < $product->quantity)
            {
                $product->quantity = $product->quantity - $buy;
                break;
            }else{
                $buy = $buy - $product->quantity;
                unset($products[$key]);
            }
        }
    }

    private function removeGet($get, &$products, $offerDiscount)
    {
        foreach($products->sortBy('price') as $key => $product)
        {
            if($get < $product->quantity)
            {
                $discount = $offerDiscount ? $product->price * $get * $offerDiscount / 100 : $product->price * $get;
                $product->quantity = $product->quantity - $get;
                
                $this->updateDiscount($product, $discount);
                break;
            }else{
                $get = $get - $product->quantity;
                $discount = $offerDiscount ? $product->price * $product->quantity * $offerDiscount / 100 : $product->price * $product->quantity;
                $this->updateDiscount($product, $discount);
                unset($products[$key]);
            }
        }
        // for($i = $products->count() - 1; $i >= 0; $i-- )
        // {

        //     if(!isset($products[$i]))
        //     {
        //         continue;
        //     }

        //     if($get < $products[$i]->quantity)
        //     {
        //         $discount = $products[$i]->price * $get;
        //         $products[$i]->quantity = $products[$i]->quantity - $get;
                
        //         $this->updateDiscount($products[$i], $discount);
        //         break;
        //     }else{
        //         $get = $get - $products[$i]->quantity;
        //         $discount = $products[$i]->price * $products[$i]->quantity;
        //         $this->updateDiscount($products[$i], $discount);
        //         unset($products[$i]);
        //     }
        // }
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
