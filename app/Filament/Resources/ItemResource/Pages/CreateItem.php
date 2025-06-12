<?php

declare(strict_types=1);

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Resources\ItemResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateItem extends CreateRecord
{
    protected static string $resource = ItemResource::class;
}
