<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements JWTSubject // Ensures JWT interface is implemented
{
    use HasFactory, Notifiable, HasUuids;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'email',
        'password',
        'sex',
        'age',
        'phone',
        'address',
        'description',
        'profile_image_url',
        'email_verified_at',
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'age' => 'integer',
        ];
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'fk_user_id', 'user_id');
    }

    public function cards(): BelongsToMany
    {
        return $this->belongsToMany(
            Card::class,
            'user_card',
            'fk1_user_id',
            'fk2_bank_card_id'
        );
    }

    public function bookedEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'book', 'fk1_user_id', 'fk2_event_id')
                    ->withPivot('booking_id', 'status', 'price_at_booking', 'created_at')
                    ->withTimestamps();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'fk_user_id', 'user_id');
    }

    public function carts(): HasMany 
{
    return $this->hasMany(Cart::class, 'user_id', 'user_id');
}


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getRouteKeyName()
    {
        return 'user_id';
    }
} 