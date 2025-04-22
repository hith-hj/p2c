<?php

declare(strict_types=1);

namespace App\Models\V1;

use App\CodesManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use CodesManager;
    use HasFactory;

    protected $guarded = [];

    public function producer(): BelongsTo
    {
        return $this->belongsTo(Producer::class, 'producer_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class, 'carrier_id');
    }
    

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function attrs(): BelongsToMany
    {
        return $this->belongsToMany(Attr::class)->withTimestamps();
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)->withTimestamps();
    }

    public function transportation(): BelongsTo
    {
        return $this->belongsTo(Transportation::class);
    }
}
