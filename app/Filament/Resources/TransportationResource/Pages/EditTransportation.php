<?php

declare(strict_types=1);

namespace App\Filament\Resources\TransportationResource\Pages;

use App\Filament\Resources\TransportationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditTransportation extends EditRecord
{
    protected static string $resource = TransportationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
