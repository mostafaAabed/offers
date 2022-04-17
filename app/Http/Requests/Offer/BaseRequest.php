<?php

namespace App\Http\Requests\Offer;

use App\Rules\Offer\BuyRule;
use App\Models\OfferIntAttr;
use App\Models\OfferCategory;
use App\Rules\Offer\UniqueOfferAttrRule;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
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
            'name' => ['required', 'unique:offers,name,'.optional($this->offer)->id],
        ];

        $offerCategory = OfferCategory::find($this->offer_category_id);
        
        if($offerCategory)
        {
            if($offerCategory->name == 'buy_get'){
                $rules ['buy'] = ['required', 'integer', 'min:1', new UniqueOfferAttrRule($offerCategory, optional($this->offer)->id)];
                $rules ['get'] = ['required', 'integer', 'min:0'];
                $rules ['discount'] = ['nullable', 'integer', 'min:1', 'max:99'];
                $rules ['buy_discount'] = ['nullable', 'required_if:get,0', 'integer', 'min:1'];
                $rules ['discount_type'] = ['nullable', 'required_with:discount,buy_discount', 'string', 'in:percentage,fixed'];
            }elseif($offerCategory->name == 'discount'){
                $rules['discount'] = ['required', 'integer', 'min:1', 'max:99', new UniqueOfferAttrRule($offerCategory, optional($this->offer)->id)];
                $rules ['discount_type'] = ['required', 'string', 'in:percentage,fixed'];
            }elseif($offerCategory->name == 'bundle'){
                $rules['buy'] = ['required', 'integer', 'min:1'];
                $rules ['price'] = ['required', 'integer', 'min:1'];
            }
        }

        if(!$this->offer)
        {
            $rules['offer_category_id'] = ['required', 'exists:offer_categories,id'];
        }else{
            $rules['active'] = ['required', 'boolean'];
            if($this->offer->offerCategory->name == 'bundle')
            {
                $productCategories = $this->offer->productCategories->pluck('id');
                $buy = OfferIntAttr::where('name', 'buy')->whereHas('offer', function($query) use ($productCategories){
                    $query->whereHas('offerCategory', function($query){
                        $query->where('name', 'bundle');
                    })
                    ->whereHas('productCategories', function($query) use ($productCategories){
                        $query->whereIn('product_category_id', $productCategories);
                    })
                    ->where('offers.id', '!=', $this->offer->id);
                })->pluck('value');

                $rules['buy'][]= 'not_in:'.$buy->join(',');
            }
        }

        return $rules;
    }
}
