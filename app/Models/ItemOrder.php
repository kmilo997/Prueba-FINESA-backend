<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemOrder extends Model
{
     protected $table = 'items_order';
     protected $primaryKey = 'id';
     public $timestamps = false;
     protected $fillable = [
        "id_order",
        "id_product",
        "quantity",
    ];
}
