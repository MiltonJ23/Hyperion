<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cart';
    protected $primaryKey = 'cart_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'cart_state',
        'cart_total_price',
    ];

    protected function casts(): array
    {
        return [
            'cart_total_price' => 'decimal:2',
        ];
    }

    /**
     * Define the relationship that a Cart belongs to a User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * The events that belong to the cart.
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'cart_event', 'fk1_cart_id', 'fk2_event_id')
                    ->withTimestamps();
    }
}
