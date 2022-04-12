<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    public function offers()
    {
        return $this->belongsToMany(Offer::Class, 'product_category_offer');
    }
}
