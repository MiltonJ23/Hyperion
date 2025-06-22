<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Images extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'image'; // Explicit table name
    protected $primaryKey = 'image_id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'image_url',
    ];

    
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_images', 'fk2_image_id', 'fk1_event_id')
                    ->withTimestamps(); 
    }
}