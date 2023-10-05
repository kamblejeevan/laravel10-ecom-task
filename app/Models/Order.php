<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['order_date','amount','user_id','status'];
    
    protected $appends = ['user_name','order_status'];
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }

    public function payment(){
        return $this->belongsTo(Payment::class);
    }

    public function getUserNameAttribute() {
        $user = $this->user->first_name." ".$this->user->last_name ?? null;
        return $user;
    }

    public function getOrderStatusAttribute() {
        return $this->status == 1 ? "Completed" : ($this->status == 2 ? "Failed" : "Pending");
    }

    public static function boot() {

        parent::boot();

        static::deleting(function ($model){

            $model->orderItems()->delete();
        });
    }
}
