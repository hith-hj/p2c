<?php

declare(strict_types=1);

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    protected $guarded = [];

    public function transportation()
    {
        return $this->belongsTo(Transportation::class);
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'belongTo_id')
            ->where('belongTo_type', static::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasOne(CarrierDetails::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'belongTo_id')
            ->where('belongTo_type', class_basename($this))
            ->select(['url', 'doc_type']);
    }

    public function profileImage()
    {
        return $this->hasOne(Document::class, 'belongTo_id')
            ->where([['belongTo_type', class_basename($this)], ['doc_type', 'profile']])
            ->select(['url', 'doc_type']);
    }

    public function validate(bool $state)
    {
        return $this->update(['is_valid' => $state]);

    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'carrier_id');
    }
}
