<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Event extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'event';

    protected $primaryKey = 'event_id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';


    protected $fillable = [
        'fk_user_id',
        'event_name',
        'event_desc',
        'event_date',
        'event_time',
        'event_venue',
        'event_location',
        'event_status',
        'event_price',
    ];


    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'event_price' => 'decimal:2',
        ];
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fk_user_id', 'user_id');
    }




    public function tickets()
        {
    return $this->hasMany(Ticket::class, 'fk_event_id', 'event_id');
        }


    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Images::class, 'event_images', 'fk1_event_id', 'fk2_image_id')
                    ->withTimestamps();
    }


    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(Cart::class, 'art_vent', 'FK2_Event_Id', 'FK1_Cart_Id')
                    ->withTimestamps();
    }

    public function bookedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'book', 'fk2_event_id', 'fk1_user_id')
                    ->using(Book::class)
                    ->withPivot('booking_id', 'status', 'price_at_booking', 'created_at')
                    ->withTimestamps();
    }







}
