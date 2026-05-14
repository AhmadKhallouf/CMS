<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'address',
        'address2',
        'city',
        'post_code'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function formattedAddress()
    {
        return sprintf(
            '%s, %s, %s, %s',
            $this->address,
            $this->address2,
            $this->city,
            $this->post_code,
        );
    }
}
