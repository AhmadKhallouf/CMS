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
            $this->cart = $cart;
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
           $item = CartItem::where('cart_id', $this->ensureCartExists()->id)
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->where('cart_id', $this->ensureCartExists()->id)
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
            $item->cart_id = $this->ensureCartExists()->id;
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
            return $this->getCart()?->items()->count() ?? 0;
        }


        public function remove()
        {

        }


        public function update()
        {

        }


        public function getCart(): ?Cart
        {
            if ($this->cart) {
                return $this->cart;
            }

            $cartId = $this->session->get(config('cart.session.cart_key'));

            if (! $cartId) {
                return null;
            }

            return $this->cart = Cart::where('cart_id', $cartId)->first();
        }

        public function ensureCartExists(): Cart
        {
            if ($cart = $this->getCart()) {
                if (Auth::check()) {
                    $this->associateWithUser();
                }

                return $cart;
            }

            $user = Auth::user();

            if ($user && $this->restoreForUser($user)) {
                return $this->cart;
            }

            $this->create($user);

            return $this->cart;
        }


        public function associateWithUser(): void
        {
            if (! Auth::check()) {
                return;
            }

            $cart = $this->getCart();

            if (! $cart) {
                return;
            }

            if ((int) $cart->user_id === (int) Auth::id()) {
                return;
            }

            $cart->user_id = Auth::id();
            $cart->save();
            $this->cart = $cart;
        }

        public function restoreForUser(User $user): bool
        {
            $storedCart = Cart::query()
                ->where('user_id', $user->id)
                ->whereHas('items')
                ->latest('updated_at')
                ->first();

            if (! $storedCart) {
                return false;
            }

            $this->session->put(config('cart.session.cart_key'), $storedCart->cart_id);
            $this->cart = $storedCart;

            return true;
        }

        public function syncForAuthenticatedUser(User $user): void
        {
            $sessionCart = $this->getCart();

            if ($sessionCart && $sessionCart->items()->exists()) {
                if ((int) $sessionCart->user_id !== (int) $user->id) {
                    $sessionCart->user_id = $user->id;
                    $sessionCart->save();
                    $this->cart = $sessionCart;
                }

                return;
            }

            if ($this->restoreForUser($user)) {
                return;
            }

            if (! $sessionCart) {
                $this->create($user);
            }
        }


        public function getSubtotal()
        {
            $subtotal = 0;

            $cart = $this->getCart();

            if (! $cart) {
                return 0;
            }

            $cartItem = $cart->items;

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

        public function clear(): void
        {
            if ($cart = $this->getCart()) {
                $cart->items()->delete();
            }

            $this->session->forget(config('cart.session.cart_key'));
            $this->cart = null;
        }
}

