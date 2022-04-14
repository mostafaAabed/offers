<?php

namespace App\Offers\Traits;

trait OfferTrait 
{
    public function discountValue($quantity, $price, $discountType = 'percentage', $discount = 0)
    {
        $total = $price * $quantity;
        switch ($discountType){
            case 'percentage' :
                $discountValue = $total * $discount / 100;
                break;
            case 'fixed' :
                $discountValue = $quantity * $discount > $total ? $total : $quantity * $discount;
                break;
            default :
            $discountValue = 0;
        }
        return $discountValue;
    }
}