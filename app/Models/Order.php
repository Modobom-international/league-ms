<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    use HasFactory;
    protected $table = 'orders';

    protected $fillable = ['product_id', 'seller_id', 'buyer_id', 'status', 'confirmed_at'];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function seller() {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer() {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
