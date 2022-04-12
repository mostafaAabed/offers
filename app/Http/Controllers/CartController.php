<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Offers\OfferFactory;
use App\Http\Requests\GetOffersRequest;
use App\Http\Resources\Cart\CartResource;

class CartController extends Controller
{
    public function __construct(OfferFactory $offerFactory)
    {
        $this->offerFactory = $offerFactory;
    }

    public function getOffers(Request $request)
    {
        $products = [
            // [
            //     'id' => 1,
            //     'quantity' => 2,
            // ],
            // [
            //     'id' => 2,
            //     'quantity' => 10,
            // ],
            // [
            //     'id' => 3,
            //     'quantity' => 3,
            // ],
            [
                'id' => 5,
                'quantity' => 2,
            ],
            [
                'id' => 6,
                'quantity' => 1,
            ],
            [
                'id' => 7,
                'quantity' => 1,
            ],
        ];
        $cart = $this->offerFactory->forProducts($products)->getOffers();
        return new CartResource($cart['products'], $cart['total'], $cart['discount']);
        
    }
}
