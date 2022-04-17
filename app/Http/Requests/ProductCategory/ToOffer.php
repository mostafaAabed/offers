<?php

namespace App\Http\Requests\ProductCategory;

use App\Models\Offer;
use App\Models\OfferIntAttr;
use App\Models\OfferCategory;
use Illuminate\Foundation\Http\FormRequest;

class ToOffer extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'offer_id' => ['required', 'nullable', 'exists:offers,id,active,1'],
        ];

        if($this->productCategory->offers->isNotEmpty()){
            $valid = collect([]);
            if($this->productCategory->offers->first()->offerCategory->name == 'buy_get'){
                $valid = OfferCategory::where('name', 'buy_get')->first()->offers->pluck('id');
            }
            elseif($this->productCategory->offers->first()->offerCategory->name == 'bundle'){
                $buy = OfferIntAttr::whereIn('offer_id', $this->productCategory->offers->pluck('id'))->where('name', 'buy')->pluck('value');
                $valid = Offer::whereHas('offerCategory', function($query){
                    $query->where('name', 'bundle');
                })
                ->whereDoesntHave('intAttr', function($query) use ($buy){
                    $query->where('name', 'buy')->whereIn('value', $buy);
                })->pluck('id');
            }

            $rules['offer_id'][]= 'in:'.$valid->join(',');
        }

        return $rules;
    }
}
