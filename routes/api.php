<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CartController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OfferCategoryController;
use App\Http\Controllers\ProductCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('cart', [CartController::class, 'getOffers']);

// offer
Route::get('offer-category', [OfferCategoryController::class, 'list']);
Route::post('offer', [OfferController::class, 'create']);
Route::post('offer/{offer}', [OfferController::class, 'update']);
Route::get('offer/{offer}', [OfferController::class, 'show']);
Route::get('offer', [OfferController::class, 'list']);

// offer category
Route::post('product-category/{productCategory}/to-offer', [ProductCategoryController::class, 'attachProductCategoryToOffer']);
Route::post('product-category/{productCategory}/remove-offers', [ProductCategoryController::class, 'deattachProductCategoryFromOffer']);
Route::get('product-category/{productCategory}/offers', [ProductCategoryController::class, 'getProductCategoryOffers']);
