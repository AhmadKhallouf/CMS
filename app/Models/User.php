<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use Billable;
    use TwoFactorAuthenticatable;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];


    public function posts() : HasMany
    {
        return $this->hasMany(Post::class);
    }



/**
 * Get all messages sent by this user
 */
public function sentMessages(): HasMany
{
    return $this->hasMany(Message::class, 'sender_id');
}

/**
 * Get all messages received by this user
 */
public function receivedMessages(): HasMany
{
    return $this->hasMany(Message::class, 'receiver_id');
}

/**
 * Get all conversations (unique users the current user has chatted with)
 */
public function conversations()
{
    $userIds = Message::where('sender_id', $this->id)
        ->select('receiver_id as user_id')
        ->union(
            Message::where('receiver_id', $this->id)
                ->select('sender_id as user_id')
        )
        ->distinct()
        ->pluck('user_id')
        ->filter(function($id) {
            return $id != $this->id;
        });
    
    return User::whereIn('id', $userIds);
}

/**
 * Get the last message with a specific user
 */
public function lastMessageWith(User $user)
{
    return Message::where(function($query) use ($user) {
        $query->where('sender_id', $this->id)
              ->where('receiver_id', $user->id);
    })->orWhere(function($query) use ($user) {
        $query->where('sender_id', $user->id)
              ->where('receiver_id', $this->id);
    })->latest()->first();
}


public function addresses(): HasMany
{
    return $this->hasMany(Address::class);
}

public function orders(): HasMany
{
    return $this->hasMany(Order::class);
}

public function carts(): HasMany
{
    return $this->hasMany(Cart::class);
}


}
