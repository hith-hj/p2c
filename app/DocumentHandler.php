<?php

declare(strict_types=1);

namespace App;

use App\Models\V1\Document;

trait DocumentHandler
{
    public function upload(
        $file,
        $belongTo_id,
        $belongTo_type,
        $doc_type = 'document'
    ): Document {
        if (! isset($belongTo_id, $belongTo_type)) {
            throw new \Exception('Image upload missing information ', 1);
        }

        $path = sprintf('uploads/%s/%s/%s', $doc_type, class_basename($belongTo_type), $belongTo_id);
        $fileName = time().'_'.$file->hashName();
        $filePath = $file->storeAs($path, $fileName, 'public');

        return Document::create([
            'belongTo_id' => $belongTo_id,
            'belongTo_type' => $belongTo_type,
            'url' => $filePath,
            'doc_type' => $doc_type,
        ]);
    }

    public function multible($input, $belongTo_id, $belongTo_type, $doc_type = 'document'): bool
    {
        if (gettype($input) === 'string') {
            $input = request($input);
        }

        foreach ($input as $key => $file) {
            if ($key === 'profile') {
                $doc_type = $key;
            }

            $this->upload($file, $belongTo_id, $belongTo_type, $doc_type);
        }

        return true;
    }
}
