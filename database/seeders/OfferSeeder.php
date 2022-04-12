<?php

namespace Database\Seeders;

use App\Models\OfferCategory;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $offerCategories = config('offers.categories');

        foreach($offerCategories as $item){
            OfferCategory::firstOrCreate([
                'name' => $item,
            ]);
        }
    }
}
