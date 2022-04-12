<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferIntAttr extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'value'];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
