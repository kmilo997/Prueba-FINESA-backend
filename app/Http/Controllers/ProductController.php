<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\States;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function list(Request $req)
    {
        $query = DB::table('products as p');
        $query->join('states as s','p.id_state','=','s.id');
        if(isset($req->search))
        {
            $search = strtolower($req->search);
            $query->where(DB::raw('LOWER(name)'),'like',"%$search%");
            $query->orWhere(DB::raw('LOWER(value)'),'like',"%$search%");
            $query->orWhere(DB::raw('LOWER(url_image)'),'like',"%$search%");
            $query->orWhere(DB::raw('LOWER(s.name)'),'like',"%$search%");
            $query->orWhere(DB::raw('LOWER(p.sku)'),'like',"%$search%");
        }
        $query->select('p.sku','p.quantity_available','p.name','s.name as name_state','p.id_state','p.id','p.value','p.url_image')->orderBy('p.id','DESC');
        $products = $query->get();

        $states = DB::table('states')->orderBy('id','DESC')->get();

        return response()->json(["products"=>$products,"states"=>$states], 200);
    }

    public function store(Request $req)
    {
        $product_exists = Product::where("sku",$req->sku)->first();
        if($product_exists)
        {
            return response()->json("El producto ya esta registrado", 400);
        }
        $data_product = array(
            "url_image" => $req->url_image,
            "value" => $req->value,
            "name" => $req->name,
            "id_state" => $req->id_state,
            "sku" => $req->sku,
            "quantity_available" => $req->quantity_available,
        );
        try {
            Product::create($data_product);
        } catch (\Exception $e) {
            return response()->json(["Error" => $e->getMessage()] , 400);
        }

        return response()->json("Producto creado exitosamente", 200);
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
        $product->quantity_available = $req->quantity_available;
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
        $producto = Product::where("id",$id)->first();
        if(!$producto)
        {
            return response()->json("El producto no existe", 400);
        }
        $producto->delete();
        return response()->json("El producto ha sido eliminado", 200);
    }
}
