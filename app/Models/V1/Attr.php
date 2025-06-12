<?php

declare(strict_types=1);

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Attr extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    public function order(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)->withTimestamps();
    }
}
