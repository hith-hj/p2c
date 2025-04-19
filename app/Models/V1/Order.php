<?php

declare(strict_types=1);

namespace App\Models\V1;

use App\Enums\UserRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function producer()
    {
        return $this->belongsTo(User::class, 'producer_id');
    }

    public function carrier()
    {
        return $this->belongsTo(User::class, 'carrier_id');
    }
    
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function user($role)
    {
        return match ($role) {
            UserRoles::Producer->value => $this->belongsTo(User::class, 'producer_id'),
            UserRoles::Carrier->value => $this->belongsTo(User::class, 'carrier_id'),
            default => throw new \Exception('Error Order User is not defiend', 1),
        };
    }

    public function attrs()
    {
        return $this->belongsToMany(Attr::class)->withTimestamps();
    }

    public function items()
    {
        return $this->belongsToMany(Item::class)->withTimestamps();
    }

    public function transportation(){
        return $this->belongsTo(Transportation::class);
    }
}
