<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->image)) {
                $product->image = 'images/1.jpg';
            }
        });
    }

    public function getImageAttribute($value)
    {
        return $value ?: 'images/1.jpg';
    }
}
