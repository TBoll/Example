<?php

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

header('Content-Type: application/json; charset=UTF-8');

/**
 * This endpoint provides the server time
 */
Route::get('api/server-time', function() {
    return response()->json(['serverTime' => Carbon::now()->toIso8601String()]);
});

/**
 * This endpoint provides a filter for the term 'minima'
 * 
 */
Route::get('/api/posts-containing-minima', function() {
    $response = HTTP::get('https://jsonplaceholder.typecode.com/posts');

    $posts = collect($response->json());

    $filterPosts = $posts->filter(function ($post) {
        return stripos($post['body'], 'minima') !== false;
    })->values();

    return $filterPosts->toArray();
});

/**
 * This endpoint provides all the products without 'stock_availiable', 'created_at' and 'updated_at'
 */
Route::get('/api/products', function () {
    $products = Product::all(['id', 'product_id', 'product_name']);

    return response()->json($products);
})

/**
 * This endpoint provides a response where an order can be fulfilled or not
 */
Route::post('/api/order-fulfillment', function () {
    $productId = $request->input('product_id');
    $quantity = $request->input('quantity');

    $products = Product::where('product_id', $productId)->first();

    if($product && $product->stock_available >= $quantity) {

        return response()->json(['fulfilled' => true, 'message' => 'Order can be fulfilled.']);
    }

    return response()->json(['fulfilled' => false, 'message' => 'Insufficent stock.']);
})



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
