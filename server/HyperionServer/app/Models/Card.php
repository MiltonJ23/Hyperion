<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'card';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'bank_card_id';

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

    /**
     * Indicates if the model should be timestamped.
     * Your migration doesn't include $table->timestamps();
     * Set this to false if you don't have created_at and updated_at columns.
     *
     * @var bool
     */
    public $timestamps = false; // Set to false as your migration doesn't include them.
                               // If you add $table->timestamps() to your migration, set this to true.

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bank_sequence',
        'bank_provider',
        'date_peremption',
        'cvv',
        'bank_card_number',
        'bank_card_type',
        'bank_card_holder_name',
        'bank_card_issuer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * (CVV is sensitive data, good to hide it by default if serialized)
     * @var array<int, string>
     */
    protected $hidden = [
        'cvv',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_peremption' => 'date',
        ];
    }

    /**
     * Define the many-to-many relationship with User.
     */
    public function users(): BelongsToMany
{
    return $this->belongsToMany(
        User::class,
        'user_card',
        'fk2_bank_card_id',
        'fk1_user_id'
    );
}
}
