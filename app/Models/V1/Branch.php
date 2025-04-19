<?php

declare(strict_types=1);

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $guarded = [];

    // protected $with=['location:locatable_id,long,lat'];

    public function producer()
    {
        return $this->belongsTo(Producer::class);
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'locatable_id')
            ->where('locatable_type', static::class);
    }

    public function orders(){
        return $this->hasMany(Order::class,'branch_id');
    }
}
