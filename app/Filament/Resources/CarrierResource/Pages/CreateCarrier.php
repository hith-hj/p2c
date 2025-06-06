<?php

declare(strict_types=1);

namespace App\Filament\Resources\CarrierResource\Pages;

use App\Filament\Resources\CarrierResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateCarrier extends CreateRecord
{
    protected static string $resource = CarrierResource::class;
}
