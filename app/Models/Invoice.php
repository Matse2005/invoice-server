<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'number',
        'date',
        'port',
        'btw'
    ];

    /**
     * Get the items for the invoice.
     **/
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Get the client the invoice belongs to
     **/
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
