<?php

declare(strict_types=1);

namespace App\Filament\Resources\AttrResource\Pages;

use App\Filament\Resources\AttrResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateAttr extends CreateRecord
{
    protected static string $resource = AttrResource::class;
}
