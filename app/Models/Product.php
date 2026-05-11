<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'published_at',
        'variants',
        'price',
        'meta_description',
        'SKU',
        'user_id',
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

     public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class); 
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProdcutVariation::class);
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }
}
