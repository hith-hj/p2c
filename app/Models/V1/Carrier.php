<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    /** @use HasFactory<\Database\Factories\CarrierFactory> */
    use HasFactory;

    protected $guarded = [];

    public function transportation()
    {
        return $this->belongsTo(Transportation::class)->select([
            'name',
            'capacity',
            'cost_per_km',
            'cost_per_kg',
            'category',
        ]);
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'locatable_id')
            ->where('locatable_type', get_class($this));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasOne(CarrierDetails::class)->select([
            'plate_number',
            'brand',
            'model',
            'color',
            'year',
        ]);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'documented_id')
            ->where('documented_type', class_basename($this))
            ->select(['url']);
    }
}
