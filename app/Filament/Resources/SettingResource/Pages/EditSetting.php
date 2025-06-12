<?php

declare(strict_types=1);

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\V1\Setting;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\File;

final class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $file = $this->getNewConfig($data);
        if (File::exists(config_path('settings.php'))) {
            File::delete(config_path('settings.php'));
        }
        File::put(config_path('settings.php'), $file);

        return $data;
    }

    protected function getNewConfig(array $data)
    {
        $new = config('settings');
        $new[$data['key']]['value'] = $data['value'];
        $export = "<?php\n\ndeclare(strict_types=1);\n\nreturn ".var_export($new, true).';';

        return $export;
    }

    protected function build()
    {
        $set = [];
        foreach (Setting::all() as $s) {
            $set[$s->key] = ['value' => $s->value, 'description' => $s->description];
        }

        $file = "<?php \ndeclare(strict_types=1);\n return \n ".var_export($set, true).';';
        File::put(config_path('settings.php'), $file);
    }
}
