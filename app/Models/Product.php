<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name', 'description', 'images', 'price', 'category', 'brand', 'slug', 'discount_price', 'status', 'condition', 'location',  'user_id', 'start_date', 'expires_at'
    ];

    public function categories()
    {
        return $this->belongsTo(CategoryProduct::class,'category' ,'id' );
    }

    public function brands()
    {
        return $this->belongsTo(Brand::class, 'brand' ,'id' );
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function mainImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id' ,'id' );
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

}
