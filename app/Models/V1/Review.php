<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function belongTo():MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'belongTo_id', 'belongTo_type');
    }
}
