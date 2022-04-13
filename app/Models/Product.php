<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     protected $table = 'products';
     protected $primaryKey = 'id';
     public $timestamps = false;
     protected $fillable = [
        "cc",
        "sku",
        "name",
        "value",
        "quantity_available",
        "id_state",
        "date_create",
        "date_change"
    ];
}
