<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cartitem extends Model
{
    use HasFactory;

    protected $table = "cart_items";
    protected $fillable = [
        'cart_id',
        'product_id',
        'variant',
        'quantity',
    ];



    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class); 
    }


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }


    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProdcutVariation::class);
    }


    
}
