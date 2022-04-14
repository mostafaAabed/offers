<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\Product;
use App\Models\OfferCategory;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $freePieceOfferCategory = OfferCategory::where('name', 'buy_get')->firstOrFail();
        $discountOfferCategory = OfferCategory::where('name', 'discount')->firstOrFail();

        $freePieceOffers = [
            [
                'name'=>'1_0',
                'buy' => '1',
                'get' => '0',
                'buy_discount' => '20',
                'discount_type' => 'percentage',
            ],
            [
                'name'=>'2_0',
                'buy' => '2',
                'get' => '0',
                'buy_discount' => '60',
                'discount_type' => 'fixed',
            ],
            [
                'name'=>'3_1',
                'buy' => '3',
                'get' => '1',
                'discount' => '60',
                'discount_type' => 'fixed',
            ],
            [
                'name'=>'4_2',
                'buy' => '4',
                'get' => '2',
            ],
            [
                'name'=>'5_3',
                'buy' => '5',
                'get' => '3',
            ],
            [
                'name'=>'6_4',
                'buy' => '6',
                'get' => '4',
            ],
        ];

        $discountOffers = [
            [
                'name'=>'30',
                'discount' => '30',
                'discount_type' => 'percentage',
            ],
            [
                'name'=>'60',
                'discount' => '60',
                'discount_type' => 'percentage',
            ],
            [
                'name'=>'40',
                'discount' => '40',
                'discount_type' => 'fixed',
            ],
        ];



        foreach($freePieceOffers as $item){
            $offer = Offer::firstOrCreate([
                'name' => $item['name'],
                'offer_category_id' => $freePieceOfferCategory->id,
            ]);

            $offer->intAttr()->firstOrCreate(['name' => 'buy'], [
                'name' => 'buy',
                'value' => $item['buy'],
            ]);

            $offer->intAttr()->firstOrCreate(['name' => 'get'], [
                'name' => 'get',
                'value' => $item['get'],
            ]);
            
            if(isset($item['discount'])){
                $offer->intAttr()->firstOrCreate(['name' => 'discount'], [
                    'name' => 'discount',
                    'value' => $item['discount'],
                ]);
            }

            if(isset($item['buy_discount'])){
                $offer->intAttr()->firstOrCreate(['name' => 'buy_discount'], [
                    'name' => 'buy_discount',
                    'value' => $item['buy_discount'],
                ]);
            }

            if(isset($item['discount_type'])){
                $offer->strAttr()->firstOrCreate(['name' => 'discount_type'], [
                    'name' => 'discount_type',
                    'value' => $item['discount_type'],
                ]);
            }
        }

        foreach($discountOffers as $item){
            $offer = Offer::firstOrCreate([
                'name' => $item['name'],
                'offer_category_id' => $discountOfferCategory->id,
            ]);

            $offer->intAttr()->firstOrCreate(['name' => 'discount'], [
                'name' => 'discount',
                'value' => $item['discount'],
            ]);

            $offer->strAttr()->firstOrCreate(['name' => 'discount_type'], [
                'name' => 'discount_type',
                'value' => $item['discount_type'],
            ]);
        }

        \App\Models\ProductCategory::factory(6)->create();
        \App\Models\Product::factory(100)->create();

    }

}
