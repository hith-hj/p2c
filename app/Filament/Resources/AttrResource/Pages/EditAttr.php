<?php

namespace App\Filament\Resources\AttrResource\Pages;

use App\Filament\Resources\AttrResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttr extends EditRecord
{
    protected static string $resource = AttrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
