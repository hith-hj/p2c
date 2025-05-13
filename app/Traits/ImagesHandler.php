<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\V1\Image;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        $fileName = time().'_'.$file->hashName();
        $filePath = $file->storeAs($path, $fileName, 'public');

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

            $this->uploadFile($file, $doc_type);
        }

        return true;
    }
}
