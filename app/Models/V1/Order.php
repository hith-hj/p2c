<?php

declare(strict_types=1);

namespace App\Models\V1;

use App\Traits\CodesHandler;
use App\Traits\FeeCalculator;
use App\Traits\OrderDteCalculator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use CodesHandler;
    use FeeCalculator;
    use HasFactory;
    use OrderDteCalculator;

    protected $guarded = [];

    public function producer(): BelongsTo
    {
        return $this->belongsTo(Producer::class);
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
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

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class, 'belongTo_id')
            ->where('belongTo_type', get_class($this));
    }
}
