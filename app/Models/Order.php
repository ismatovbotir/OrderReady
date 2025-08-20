<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderStatus;
use App\Models\OrderItem;

class Order extends Model
{
    use HasFactory;
    public $guarded=[];

    public function statuses(){
        return $this->hasMany(OrderStatus::class);
    }
    public function items(){
        return $this->hasMany(OrderItem::class);
    }
}
