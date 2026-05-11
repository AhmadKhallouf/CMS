<?php

namespace App\Models;

use Filament\Actions\Concerns\HasForm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class Post extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use HasForm;
    use InteractsWithMedia;
    

    protected $fillable = [
        'title',
        'content',
        'meta_description',
        'is_featured',
        'slug',
        'is_published',
        'user_id',
    ];

    public function users() : BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function excerpt():string
    {
        return Str::words(tiptap_converter()->asText($this->content), 40, '...');
    }

    // public function getImageUrl()
    // {
    //     $mediaUrl = $this->getFirstMediaUrl();

    //     if ($mediaUrl) {
    //         $baseUrl = config('app.url');
            
    //         // Check if the base URL exists in the media URL before replacing it
    //         if (strpos($mediaUrl, $baseUrl) !== false) {
    //             $urlWithoutBase = str_replace($baseUrl, '', $mediaUrl);
    //             return $urlWithoutBase;
    //         }

    //         return $mediaUrl; // Return the original URL if base URL is not found
    //     }

    //     return null; // Or any default URL if the media URL is not available
    // }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class); 
    }
}
