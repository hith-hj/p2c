<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProducerResource\Pages;

use App\Filament\Resources\ProducerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListProducers extends ListRecords
{
    protected static string $resource = ProducerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
