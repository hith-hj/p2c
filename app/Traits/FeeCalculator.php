<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\FeeTypes;
use App\Models\V1\Fee;
use Illuminate\Support\Carbon;

trait FeeCalculator
{
    public function createFee(object $badge, $type = FeeTypes::normal->value): ?Fee
    {
        $fee = $this->fee($this->cost);
        $delay_fee = $this->delayFee($fee);
        $due_date = $this->dueDate();
        if (! $this->feeExists($badge) && method_exists($badge, 'fees')) {
            $record = $badge->fees()->create([
                'belongTo_type' => $badge::class,
                'subject_id' => $this->id,
                'subject_type' => $this::class,
                'type' => $type,
                'amount' => $fee,
                'delay_fee' => $delay_fee,
                'due_date' => $due_date,
                'status' => 0,
            ]);

            return $record;
        }

        return null;
    }

    private function fee(int $cost): int
    {
        $percent = (int) config('app.fee_amount', 20);

        return (int) round($cost * ($percent / 100));
    }

    private function delayFee(int $fee): int
    {
        $percent = (int) config('app.delay_fee', 20);

        return (int) round($fee * ($percent / 100));
    }

    private function dueDate(): Carbon
    {
        return now()->endOfMonth();
    }

    private function feeExists(object $badge): bool
    {
        return $badge->fees()->where([
            ['subject_id', $this->id],
            ['subject_type', $this::class],
        ])->exists();
    }
}
