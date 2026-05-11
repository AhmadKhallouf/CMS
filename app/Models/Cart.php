<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'user_id',
    ];


    public static function creating($callback)
    {
        parent::boot();

        static::creating(function (Cart $cart){
            $cart->cart_id = Str::uuid();
            $cart->save();
        });
    }

    // public function add($cartID, $quantity ,$productID, $variant = null ){

    //     $item = $this->items()->where('cart_id',$cartID)
    //                           ->where('product_id',$productID)
    //                           ->where('variant',$variant)
    //                           ->first();
    //     if($item){

    //         $item->quantity += $quantity;
    //         $item->save();

    //     }else{

    //         $this->items()->create([
    //             'product_id' => $productID,
    //             'quantity' => $quantity,
    //             'variant' => $variant,
    //         ]);
    //     }
    // }

    public function items(): HasMany
    {
        return $this->hasMany(Cartitem::class);
    }
}
