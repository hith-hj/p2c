<?php

namespace App\Filament\Resources\ProducerResource\Pages;

use App\Filament\Resources\ProducerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProducer extends ViewRecord
{
    protected static string $resource = ProducerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
