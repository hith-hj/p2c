<?php

declare(strict_types=1);

namespace App\Filament\Resources\TransportationResource\Pages;

use App\Filament\Resources\TransportationResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateTransportation extends CreateRecord
{
    protected static string $resource = TransportationResource::class;
}
