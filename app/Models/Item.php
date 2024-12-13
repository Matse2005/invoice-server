<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'price',
        'amount',
        'btw'
    ];

    /**
     * Get the client the invoice belongs to
     **/
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
