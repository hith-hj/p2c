<?php

declare(strict_types=1);

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\V1\Setting;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;

final class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('sync')
                ->color(Color::Amber)
                ->action(function () {
                    foreach (config('settings') as $key => $setting) {
                        if (! isset($setting['value'])) {
                            continue;
                        }
                        if (! Setting::where('key', $key)->exists()) {
                            Setting::create([
                                'key' => $key,
                                'value' => $setting['value'],
                                'description' => $setting['description'],
                            ]);
                        }
                    }
                })->hidden(true),
        ];
    }
}
