<?php

declare(strict_types=1);

namespace App\Models\V1;

use App\Observers\OrderObserver;
use App\Traits\CodesHandler;
use App\Traits\OrderDteCalculator;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([OrderObserver::class])]
final class Order extends Model
{
    use CodesHandler;
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

    public function getFeeSource()
    {
        return $this->cost;
    }
}
