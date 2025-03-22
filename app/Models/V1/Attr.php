<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attr extends Model
{
    /** @use HasFactory<\Database\Factories\AttrFactory> */
    use HasFactory;

    public function order()
    {
        return $this->belongsToMany(Order::class)->withTimestamps();
    }
}
