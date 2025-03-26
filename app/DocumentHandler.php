<?php

namespace App;

use App\Models\V1\Document;

trait DocumentHandler
{
    public function upload($file, $documented_id, $documented_type, $doc_type = 'document')
    {
        if (! isset($documented_id,$documented_type)) {
            throw new \Exception("Image upload missing information ", 1);
            ;
        }
        $path = "uploads/$doc_type/$documented_type/$documented_id";
        $fileName = time().'_'.$file->hashName();
        $filePath = $file->storeAs($path, $fileName, 'public');

        return Document::create([
            'documented_id' => $documented_id,
            'documented_type' => $documented_type,
            'url' => $filePath,
            'doc_type' => $doc_type
        ]);
    }

    public function multible($input, $documented_id, $documented_type, $doc_type = 'document')
    {
        if (gettype($input) === 'string') {
            $input = request($input);
        }
        foreach ($input as $key=> $file) {
            if($key === 'profile'){
                $doc_type = $key;
            }
            $this->upload($file, $documented_id, $documented_type, $doc_type);
        }

        return true;
    }
}
