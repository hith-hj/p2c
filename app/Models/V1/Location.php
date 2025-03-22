<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function holder()
    {
        return $this->belongsTo($this->locatable_type)
            ->where('id', $this->locatable_id);
    }
}
