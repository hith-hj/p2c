<?php

declare(strict_types=1);

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;

class CarrierDetails extends Model
{
    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];
}
