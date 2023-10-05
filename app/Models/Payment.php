<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['order_id','payment_intend','amount', 'customer_email','payment_at','intiated_at','status'];

    protected $appends = ['payment_status'];
    
    protected $hidden = ['payment_intend'];

    public function order(){
        return $this->belongsTo(Order::class);
    }
    public function getPaymentStatusAttribute() {
       return $this->status == 1 ? "Completed" : "Failed";
    }
}   
