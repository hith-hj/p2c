<?php

declare(strict_types=1);

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $guarded = [];

    public function subject()
    {
        return $this->hasOne($this->belongTo_type)->where('id', $this->belongTo_id);
    }
}
