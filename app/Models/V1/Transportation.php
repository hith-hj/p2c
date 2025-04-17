<?php

declare(strict_types=1);

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transportation extends Model
{
    use HasFactory;

    protected $hidden = ['created_at','updated_at'];

    protected $guarded = [];

    public function carrier()
    {
        return $this->hasMany(Carrier::class);
    }
}
