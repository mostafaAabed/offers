<?php

namespace App\Rules\Offer;

use App\Models\OfferIntAttr;
use Illuminate\Contracts\Validation\Rule;

class UniqueOfferAttrRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($offerCategory, $offerId = null)
    {
        $this->offerId = $offerId;
        $this->offerCategory = $offerCategory;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return OfferIntAttr::whereHas('offer.offerCategory', function($query) use ($attribute){
            return $query->where('name', $this->offerCategory->name);
        })
        ->when($this->offerId, function($query) use ($attribute){
            $query->where('offer_id', '!=', $this->offerId);
        })
        ->where([['name', $attribute],['value', $value]])->get()->isEmpty();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'already exists.';
    }
}
