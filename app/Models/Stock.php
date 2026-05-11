<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'variant_id',
        'quantity',
    ];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProdcutVariation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function decrementStock(int $quantity): Bool
    {
        if($this->quantity < $quantity){
                return false; // Not enough quantity in stock
            }
        
        $this->decrement('quantity',$quantity);

        return true;
    }

    public function increamentStock(int $quantity): void
    {
        $this->increment('quantity',$quantity);
    }

}
