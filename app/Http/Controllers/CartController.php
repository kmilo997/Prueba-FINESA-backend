<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\Models\Cart;
use App\Models\ItemOrder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CartController extends Controller
{
    public function list(Request $req)
    {
        $query = DB::table('orders as o');
        $query->join('customers as c','o.id_customer','=','c.id');
        $query->join('states as s','s.id','=','o.id_state');
        if(isset($req->search))
        {
            $search = strtolower($req->search);
            $query->where(DB::raw('LOWER(o.name)'),'like',"%$search%");
        }
        $query->select('o.id','c.name as name_customer','s.name as name_state')->orderBy('o.id','DESC');
        $orders = $query->get();

        $products = DB::table('products')->where('id_state',1)->select('id','name','value','quantity_available')->orderBy('id','DESC')->get();
        $customers = DB::table('customers')->orderBy('name','DESC')->get();

        return response()->json(["orders"=>$orders,"products"=>$products,"customers"=>$customers], 200);
    }

    public function getCart(Request $req,$id)
    {
        $cart_exists = Cart::where("id",$req->id)->first();
        if(!$cart_exists)
        {
            return response()->json("La orden no existe", 400);
        }
        $query = DB::table('orders');
        $query->where('id',$id);
        $order = $query->get();

        $queryProducts = DB::table('products as p');
        $queryProducts->join('items_order as i','i.id_product','=','p.id');
        $queryProducts->where('i.id_order',$id);
        $queryProducts->select('p.*','i.quantity');
        $products = $queryProducts->get();

        return response()->json(["order"=>$order,"products"=>$products], 200);
    }


    public function store(Request $req)
    {
        $cart_exists = Cart::where("id",$req->id)->first();
        if($cart_exists)
        {
            return response()->json("La orden ya fue registrada", 400);
        }
        $data_product = array(
            "id_customer" => $req->id_customer,
            "id_state" => $req->id_state
        );
        try {
            $cart = Cart::create($data_product);
        } catch (\Exception $e) {
            return response()->json(["Error" => $e->getMessage()] , 400);
        }

        try 
        {
            foreach ($req->products as $product) {
                ItemOrder::create(
                    array(
                        "id_order" => $cart->id,
                        "id_product" => $product["id"],
                        "quantity" => $product["quantity"]
                    )
                );
            }
        } catch (\Exception $e) {
            return response()->json(["Error" => $e->getMessage()] , 400);
        }

        return response()->json("Orden registrada exitosamente", 200);
    }

    public function put(Request $req,$id)
    {
        $product = Product::where("id",$id)->first();
        if(!$product)
        {
            return response()->json("El producto no existe", 400);
        }
        $product->url_image = $req->url_image;
        $product->value = $req->value;
        $product->name = $req->name;
        $product->id_state = $req->id_state;
        $product->sku = $req->sku;
        $product->date_change = Carbon::now();
        try {
            $product->save();
        } catch (\Exception $e) {
            return response()->json(["Error" => $e->getMessage()] , 400);
        } 
        return response()->json("El producto ha sido modificado", 200);
    }

    public function delete(Request $req,$id)
    {
        $cart_exists = Cart::where("id",$id)->first();
        if(!$cart_exists)
        {
            return response()->json("La orden no existe", 400);
        }

        $itemsCart = ItemOrder::where('id_order',$id)->delete();

        $cart_exists->delete();

        return response()->json("El Carrito ha sido eliminado", 200);
    }
}
