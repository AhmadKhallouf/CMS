<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProdcutVariation extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'product_id',
        'title',
        'type',
        'order',
        'SKU',
        'price',
        'parent_id',
    ];

    public static function boot()
    {
         parent::boot();
         static::creating(function ($variation) {
            $variation->order = ProdcutVariation::where('product_id',$variation->product_id)->max('order') + 1;
         });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProdcutVariation::class,'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ProdcutVariation::class,'parent_id');
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class,'variant_id');
    }
    
}
