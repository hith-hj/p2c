<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\V1\Image;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

trait ImagesHandler
{
    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'belongTo_id')
            ->withAttributes(['belongTo_type' => $this::class]);
    }

    public function uploadImage($file, $doc_type = 'document'): Image
    {
        $path = sprintf('uploads/%s/%s/%s', $doc_type, class_basename($this::class), $this->id);
        $fileName = time() . '_' . $file->hashName();
        $filePath = $file->storeAs($path, $fileName, 'public');
        $this->syncImagesToPublic();
        return $this->images()->create([
            'url' => $filePath,
            'type' => $doc_type,
        ]);
    }

    public function multibleImage($input, $doc_type = 'document'): bool
    {
        if (gettype($input) === 'string') {
            $input = request($input);
        }

        foreach ($input as $key => $file) {
            if ($key === 'profile') {
                $doc_type = $key;
            }

            $this->uploadImage($file, $doc_type);
        }

        return true;
    }

    private function syncImagesToPublic()
    {
        try {
            Log::info('Syncing Images');
            $command = "rsync -a --delete /home/bookus/repositories/p2c/storage/app/public/uploads/ /home/bookus/public_html/p2c.4bookus.com/uploads";
            $result = shell_exec($command);
            Log::info('images synced', ['result' => $result]);
        } catch (\Exception $e) {
            Log::info('images syncing error', ['error' => $e->getMessage()]);
        }
    }
}
