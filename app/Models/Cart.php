<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
     protected $table = 'orders';
     protected $primaryKey = 'id';
     public $timestamps = false;
     protected $fillable = [
        "id_customer",
        "id_state",
        "date_create",
        "date_change"
    ];
}
