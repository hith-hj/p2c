<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProducerResource\Pages;

use App\Filament\Resources\ProducerResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateProducer extends CreateRecord
{
    protected static string $resource = ProducerResource::class;
}
