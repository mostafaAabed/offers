<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OfferCategory;
use App\Http\Resources\OfferCategoryResource;

class OfferCategoryController extends Controller
{
    public function list()
    {
        $offers = OfferCategory::get();
        return OfferCategoryResource::collection($offer);
    }
}
