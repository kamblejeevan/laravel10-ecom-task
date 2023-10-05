<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $fillable = ['user_id', 'product_id'];
    protected $appends = ['user_name', 'product_name'];
    
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
}
