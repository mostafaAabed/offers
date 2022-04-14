<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use App\Models\OfferCategory;
use App\Http\Resources\Offer\OfferResource;
use App\Http\Requests\Offer\CreateRequest;
use App\Http\Requests\Offer\UpdateRequest;
use App\Http\Requests\Offer\BaseRequest;

class OfferController extends Controller
{
    public function create(BaseRequest $request)
    {
        $offerCategory = OfferCategory::findOrFail($request->offer_category_id);
        $offer = Offer::create($request->validated());
        $offer->active = true;
        $int = $request->only(config("offers.attrs.{$offerCategory->name}.int"));
        $str = $request->only(config("offers.attrs.{$offerCategory->name}.str"));
        foreach($int as $name => $value){
            $offer->intAttr()->create([
                'name' => $name,
                'value' => $value,
            ]);
        }
        foreach($str as $name => $value){
            $offer->strAttr()->create([
                'name' => $name,
                'value' => $value,
            ]);
        }
        return new OfferResource($offer);
    }

    public function update(BaseRequest $request, Offer $offer)
    {
        $offerCategory = $offer->offerCategory;
        $offer->update($request->validated());
        $int = $request->only(config("offers.attrs.{$offerCategory->name}.int"));
        $str = $request->only(config("offers.attrs.{$offerCategory->name}.str"));
        $offer->intAttr()->delete();
        $offer->strAttr()->delete();
        foreach($int as $name => $value){
            $offer->intAttr()->create([
                'name' => $name,
                'value' => $value,
            ]);
        }
        foreach($str as $name => $value){
            $offer->strAttr()->create([
                'name' => $name,
                'value' => $value,
            ]);
        }
        return new OfferResource($offer);
    }

    public function show(Offer $offer)
    {
        return new OfferResource($offer);
    }

    public function list()
    {
        $offers = Offer::when(request('offerCategoryId'), function($query){
            $query->where('offer_category_id', request('offerCategoryId'));
        })->get();
        return OfferResource::collection($offers);
    }


}
