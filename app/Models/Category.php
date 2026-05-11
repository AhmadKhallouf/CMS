<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'body',
        'parent_id',
        'bg_color',
        'text_color',
        'meta_description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'parent_id','id');
    }

     public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class); 
    }
}
