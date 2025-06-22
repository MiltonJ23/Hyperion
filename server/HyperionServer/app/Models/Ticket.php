<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ticket'; 
    protected $primaryKey = 'ticket_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'fk_user_id',
        'fk_event_id',
        'ticket_type',
    ];

    /**
     * Get the user that owns the ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fk_user_id', 'user_id');
    }

    /**
     * Get the event for which this ticket is.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'fk_event_id', 'event_id');
    }
}