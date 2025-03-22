<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $guarded = [];

    public function producer()
    {
        return $this->belongsTo(User::class, 'producer_id');
    }

    public function carrier()
    {
        return $this->belongsTo(User::class, 'carrier_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class)->withTimestamps();
    }
}
