<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Offers\OfferFactory;
use App\Http\Requests\CartRequest;
use App\Http\Requests\GetOffersRequest;
use App\Http\Resources\Cart\CartResource;

class CartController extends Controller
{
    public function __construct(OfferFactory $offerFactory)
    {
        $this->offerFactory = $offerFactory;
    }

    public function getOffers(CartRequest $request)
    {
        $products = $request->products;
        $cart = $this->offerFactory->forProducts($products)->getOffers();
        return new CartResource($cart['products'], $cart['total'], $cart['discount']);
        
    }
}
