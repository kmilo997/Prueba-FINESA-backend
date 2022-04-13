<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function list(Request $req)
    {
        $query = DB::table('customers');
        if(isset($req->search))
        {
            $search = strtolower($req->search);
            $query->where(DB::raw('LOWER(name)'),'like',"%$search%");
            $query->orWhere(DB::raw('LOWER(email)'),'like',"%$search%");
            $query->orWhere(DB::raw('LOWER(cc)'),'like',"%$search%");
        }
        $query->orderBy('id','DESC');
        $customers = $query->get();
        return response()->json(["customers"=>$customers], 200);
    }

    public function store(Request $req)
    {
        $customer_exists = Customer::where("cc",$req->cc)->orWhere("email",$req->email)->first();
        if($customer_exists)
        {
            return response()->json("El cliente ya esta registrado", 400);
        }
        $data_customer = array(
            "cc" => $req->cc,
            "email" => $req->email,
            "name" => $req->name
        );
        try {
            Customer::create($data_customer);
        } catch (\Exception $e) {
            return response()->json(["Error" => $e->getMessage()] , 400);
        }

        return response()->json("Cliente creado exitosamente", 200);
    }

    public function put(Request $req,$id)
    {
        $customer = Customer::where("id",$id)->first();
        if(!$customer)
        {
            return response()->json("El cliente no existe", 400);
        }
        $customer->cc = $req->cc;
        $customer->name = $req->name;
        $customer->email = $req->email;
        $customer->date_change = Carbon::now();
        try {
            $customer->save();
        } catch (\Exception $e) {
            return response()->json(["Error" => $e->getMessage()] , 400);
        } 
        return response()->json("El cliente ha sido modificado", 200);
    }

    public function delete(Request $req,$id)
    {
        $customer = Customer::where("id",$id)->first();
        if(!$customer)
        {
            return response()->json("El cliente no existe", 400);
        }
        $customer->delete();
        return response()->json("El cliente ha sido eliminado", 200);
    }
}
