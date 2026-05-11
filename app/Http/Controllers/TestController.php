<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class TestController extends Controller
{
  

    public function getPage(){
        $product = Product::first();
        $finalVariantId = 0;
         return view('livewire.product',compact('product','finalVariantId'));
    }

    public function formTest(Request $request){
        $n = $request->all();
         
       if(data_get($n, 'status') !== null){
        return response()->json([
            'status' => true,
        ],200);
       }else{
        return response()->json([
            'status' => false,
        ],200); 
       }
    }

   
}
