<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\DB;
class ReportController extends Controller
{
    public function list(Request $req)
    {
        $query = DB::table('items_order as i');
        $query->join('orders as o','o.id','=','i.id_order');
        $query->join('customers as c','c.id','=','o.id_customer');
        $query->join('products as p','p.id','=','i.id_product');
        if(isset($req->dateStart) && isset($req->dateEnd))
        {
            $query->whereBetween('o.date_create', [$req->dateStart, $req->dateEnd]);
        }
        $query->select('o.id','c.name as name_customer','p.name as name_product','p.value as value_product','i.quantity as quantity','o.date_create',DB::raw('i.quantity * p.value as total'))->orderBy('o.date_create','DESC');
        $items_order = $query->get();


        return response()->json(["items_order"=>$items_order], 200);
    }

}
