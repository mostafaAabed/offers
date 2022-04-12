<?php

namespace App\Offers\OfferTypes;

interface OfferProvider {

    public function forProducts($products);

    public function getOffers();
}