<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'offer_category_id', 'active'];

    public function intAttr()
    {
        return $this->hasMany(OfferIntAttr::class);
    }

    public function strAttr()
    {
        return $this->hasMany(OfferStrAttr::class);
    }

    public function offerCategory()
    {
        return $this->belongsTo(OfferCategory::class);
    }
}
