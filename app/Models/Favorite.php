<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $table = 'brands';

    protected $fillable = [
        'user_id',
        'product_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Mỗi Favorite thuộc về một sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
