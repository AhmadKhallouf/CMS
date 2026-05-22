<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrdersShowController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return view('orders.index', [
            'orders' => $request->user()->orders()->with('products')->get(), 
        ]);
    }
}
