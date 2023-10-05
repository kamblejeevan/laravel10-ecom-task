<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id','quantity'];
    
    protected $appends = ['user_name', 'product_name','product_price'];
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function getUserNameAttribute() {
        $user = $this->user->first_name." ".$this->user->last_name ?? null;
        return $user;
    }

    public function getProductNameAttribute() {
        $product = $this->product->name ?? null;
        return $product;
    }

    public function getProductPriceAttribute() {
        $product = $this->product->price ?? null;
        return $product;
    }
}
