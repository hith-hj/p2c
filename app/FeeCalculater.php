<?php

declare(strict_types=1);

namespace App;

use App\Models\V1\Fee;
use Illuminate\Support\Carbon;

trait FeeCalculater
{
    public function createFee(object $badge): ?Fee
    {
        $fee = $this->fee($this->cost);
        $delay_fee = $this->delayFee($fee);
        $due_date = $this->dueDate();
        if (! $this->feeExists($badge) && method_exists($badge, 'fees')) {
            $record = $badge->fees()->create([
                'belongTo_type' => get_class($badge),
                'subject_id' => $this->id,
                'subject_type' => $this::class,
                'amount' => $fee,
                'delay_fee' => $delay_fee,
                'due_date' => $due_date,
                'status' => 0,
            ]);

            return $record;
        }

        return null;
    }

    public function pay(int $id): bool
    {
        $fee = $this->fees()->find($id);

        return $fee->delete();
    }

    private function fee(int $cost): float
    {
        $percent = (float) config('app.fee_amount', 20);

        return $cost * ($percent / 100);
    }

    private function delayFee(float $fee): float
    {
        $percent = config('app.delay_fee', 20);

        return $fee * ($percent / 100);
    }

    private function dueDate(): Carbon
    {
        return now()->endOfMonth();
    }

    private function feeExists(object $badge): bool
    {
        return $badge->fees()->where([
            ['subject_id', $this->id],
            ['subject_type', get_class($this)],
        ])->exists();
    }
}
