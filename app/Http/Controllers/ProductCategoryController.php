<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Http\Resources\Offer\OfferResource;
use App\Http\Requests\ProductCategory\ToOffer;

class ProductCategoryController extends Controller
{
    public function attachProductCategoryToOffer(ToOffer $request,ProductCategory $productCategory)
    {
        $offer = Offer::findOrFail($request->offer_id);
        if($offer->offerCategory->name == 'buy_get')
        {
            $productCategory->offers()->detach();
            $productCategory->offers()->attach($offer->offerCategory->offers->pluck('id'));
            $offers = $offer->offerCategory->offers;
        }else{
            $productCategory->offers()->attach($request->offer_id);
            $offers = $productCategory->offers()->get();
        }
        
        return OfferResource::collection($offers);


    }

    public function getProductCategoryOffers(ProductCategory $productCategory)
    {
        if($productCategory->offers->isNotEmpty() && $productCategory->offers->first()->offerCategory->name == 'buy_get')
        {
            $offers = $productCategory->offers->first()->offerCategory->offers;
        }else{
            $offers = $productCategory->offers;
        }

        return OfferResource::collection($offers);
    }

    public function deattachProductCategoryFromOffer(ProductCategory $productCategory)
    {
        $productCategory->offers()->detach();
        return ['message' => 'record has been updated successfully'];
    }
}
