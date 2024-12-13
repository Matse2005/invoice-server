<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address_one',
        'address_two',
        'address_three',
        'btw'
    ];

    /**
     * Get the items for the invoice.
     **/
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
