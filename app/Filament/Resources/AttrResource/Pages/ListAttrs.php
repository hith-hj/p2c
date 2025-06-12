<?php

declare(strict_types=1);

namespace App\Filament\Resources\AttrResource\Pages;

use App\Filament\Resources\AttrResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListAttrs extends ListRecords
{
    protected static string $resource = AttrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
