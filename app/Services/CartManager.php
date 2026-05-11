<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Cartitem;
use App\Models\User;
use App\Services\Contract\CartManager as CartInterface;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartManager  implements CartInterface
{

        protected $cart;
        protected $session;

        public function __construct(SessionManager $sessionManager)
        {
            $this->session = $sessionManager->driver();
            //$this->cart = $this->getOrCreateCart();
        }


        public function exists()
        {
            return $this->session->has(config('cart.session.cart_key')) && $this->getCart();
        }


        public function create(?User $user = null)
        {

            $cart = Cart::make([
        'cart_id' => Str::uuid() // Set the cart_id here
    ]);

            if($user){
                $cart->user()->associate($user); 
            }

            $cart->save();

            $this->session->put(config('cart.session.cart_key'), $cart->cart_id);
        }


        // protected function getOrCreateCart()
        // {

        //     $cartId = $this->session->get('cart_id');

        //     if($cartId){

        //         $cart = Cart::where('cart_id' , $cartId)->first();

        //         if(Auth::check() && is_null($cart->user_id)){

        //                 $cart->user_id = Auth::id();
        //                 $cart->save();

        //          }else{

        //                 $cart = Cart::creat([
        //                     'cart_id' => Str::uuid(),
        //                     'user_id' => Auth::id() ?? null,

        //                 ]);

        //                 $this->session->put('cart_id' , $cart->cart_id);
        //                 $this->session->save();
        //          }

        //     }

        //     return $cart;
        // }

        

        public function add($productId, $variantId = null, $quantity = 1)
        {
           $item = CartItem::where('cart_id', $this->getCart()->id)
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->where('cart_id', $this->getCart()->id)
            ->first();


        if ($item) {
            $item->quantity += $quantity;
            $item->save();


            if ($item->product) {
                $item->product->stock->decrementStock($quantity);
            }

            return;
        }

            $item = Cartitem::make();

            $item->product_id = $productId;
            $item->cart_id = $this->getCart()->id;
            $item->quantity = $quantity;

            if($variantId){
               $item->variant_id = $variantId;
            }

            if($item->product){
                $item->product->stock->decrementStock($quantity);
            }

            $item->save();
        }


        public function getItemsCount(): int
        {
            return $this->getCart()->items()->count();
        }


        public function remove()
        {

        }


        public function update()
        {

        }


        public function getCart()
        {

            if($this->cart){
                return  $this->cart;
            }

            return $this->cart = Cart::where('cart_id', $this->session->get(config('cart.session.cart_key')))->first();
        }


        public function associateWithUser()
        {

            $this->cart->user_id = Auth::id();

            $this->cart->save();
        }


        public function getSubtotal()
        {
            $subtotal = 0;

            $cartItem = $this->getCart()->items;

            foreach($cartItem as $item){

                if($item->variant){

                    $price = $item->variant->price;

                } else {

                    $price = $item->product->price;
                } 

                $subtotal += $price * $item->quantity; 
            }

            return $subtotal;
        }
}

