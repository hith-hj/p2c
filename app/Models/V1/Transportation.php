<?php

declare(strict_types=1);

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transportation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function carriers(): HasMany
    {
        return $this->hasMany(Carrier::class);
    }
}
