<?php

namespace App;

use App\Models\V1\Document;

trait DocumentHandler
{
    public function upload($file, $documented_id, $documented_type)
    {
        if (! isset($documented_id,$documented_type)) {
            return false;
        }
        $path = 'uploads/docs/'.$documented_type.'/'.$documented_id;
        $fileName = time().'_'.$file->hashName();
        $filePath = $file->storeAs($path, $fileName, 'public');

        return Document::create([
            'documented_id' => $documented_id,
            'documented_type' => $documented_type,
            'url' => $filePath,
        ]);
    }

    public function multible($input, $documented_id, $documented_type)
    {
        if (gettype($input) === 'string') {
            $input = request($input);
        }
        foreach ($input as $file) {
            $this->upload($file, $documented_id, $documented_type);
        }

        return true;
    }
}
