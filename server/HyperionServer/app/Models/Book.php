<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;


/**
 * This class represents the pivot model for the 'book' table.
 * It MUST extend Pivot because it's used in a many-to-many relationship
 * with extra attributes.
 */
class Book extends Pivot
{

    protected $table = 'book';

    public $incrementing = true;
    
    protected $primaryKey = 'booking_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fk1_user_id',
        'fk2_event_id',
        'price_at_booking',
        'status'
    ];
}