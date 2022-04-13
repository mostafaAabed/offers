<?php

namespace App\Http\Requests\Offer;

use App\Rules\Offer\BuyRule;
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
                $rules ['get'] = ['required', 'integer', 'min:1'];
                $rules ['discount'] = ['nullable', 'integer', 'min:1', 'max:99'];
            }elseif($offerCategory->name == 'discount'){
                $rules['discount'] = ['required', 'integer', 'min:1', 'max:99', new UniqueOfferAttrRule($offerCategory, optional($this->offer)->id)];
            }
        }

        if(!$this->offer)
        {
            $rules['offer_category_id'] = ['required', 'exists:offer_categories,id'];
        }else{
            $rules['active'] = ['required', 'boolean'];
        }

        return $rules;
    }
}
