<?php

declare(strict_types=1);

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function producer()
    {
        return $this->belongsTo(Producer::class);
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'belongTo_id')
            ->where('belongTo_type', static::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'branch_id');
    }
}
