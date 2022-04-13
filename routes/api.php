<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'customers'], function (){
    Route::get('','App\Http\Controllers\CustomerController@list');
    Route::post('/','App\Http\Controllers\CustomerController@store');
    Route::put('/{id}','App\Http\Controllers\CustomerController@put');
    Route::delete('/{id}','App\Http\Controllers\CustomerController@delete');
});

Route::group(['prefix' => 'products'], function (){
    Route::get('','App\Http\Controllers\ProductController@list');
    Route::post('/','App\Http\Controllers\ProductController@store');
    Route::put('/{id}','App\Http\Controllers\ProductController@put');
    Route::delete('/{id}','App\Http\Controllers\ProductController@delete');
});

Route::group(['prefix' => 'cart'], function (){
    Route::get('','App\Http\Controllers\CartController@list');
    Route::get('/{id}','App\Http\Controllers\CartController@getCart');
    Route::post('/','App\Http\Controllers\CartController@store');
    Route::put('/{id}','App\Http\Controllers\CartController@put');
    Route::delete('/{id}','App\Http\Controllers\CartController@delete');
});

Route::group(['prefix' => 'reports'], function (){
    Route::get('','App\Http\Controllers\ReportController@list');
});