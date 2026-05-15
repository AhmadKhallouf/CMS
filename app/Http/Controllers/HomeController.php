<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Services\CartManager;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request. 
     */
    public function __invoke(Request $request)
    {
        $cart = app(CartManager::class);
        return view('home',[
            'posts' => Post::where('is_featured',true)->latest()->take(6)->get(),
            'products' => Product::latest()->take(6)->get(),
            'cart' => $cart,
        ]);
    }
}
