<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\FeeTypes;
use App\Models\V1\Fee;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

trait FeesHandler
{
    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class, 'belongTo_id')
            ->withAttributes(['belongTo_type' => $this::class]);
    }

    public function createFee(object $subject, $type = FeeTypes::normal->value): ?Fee
    {
        Truthy(
            ! method_exists($subject, 'getFeeSource'),
            sprintf('%s missing getFeeSource()', class_basename($subject))
        );
        $amount = $this->amount($subject->getFeeSource());
        $delay_fee = $this->delayFee($amount);
        $due_date = $this->dueDate();
        $record = $this->fees()->create([
            'subject_id' => $subject->id,
            'subject_type' => $subject::class,
            'type' => $type,
            'amount' => $amount,
            'delay_fee' => $delay_fee,
            'due_date' => $due_date,
            'status' => 0,
        ]);

        return $record;
    }

    private function amount(int $source): int
    {
        $percent = config('settings.fee_percent.value', 20);

        return (int) round($source * ($percent / 100));
    }

    private function delayFee(int $fee): int
    {
        $percent = config('settings.delay_fee.value', 30);

        return (int) round($fee * ($percent / 100));
    }

    private function dueDate(): Carbon
    {
        return now()->endOfMonth();
    }

    private function feeExists(object $object): bool
    {
        Truthy(! method_exists($object, 'getFeeSource'), 'missing fees() method');

        return $object->fees()->where([
            ['subject_id', $this->id],
            ['subject_type', $this::class],
        ])->exists();
    }
}
