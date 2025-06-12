<?php

declare(strict_types=1);

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;
}
