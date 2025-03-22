<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'notifiable_id');
    }

    public function viewed()
    {
        return $this->update(['status' => 1]);
    }
}
