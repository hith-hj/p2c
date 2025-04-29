<?php

declare(strict_types=1);

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $guarded = [];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function holder()
    {
        return $this->morphTo(__FUNCTION__, 'belongTo_type', 'belongTo_id');
    }

    public function subject()
    {
        return $this->morphTo(__FUNCTION__, 'subject_type', 'subject_id');
    }
}
