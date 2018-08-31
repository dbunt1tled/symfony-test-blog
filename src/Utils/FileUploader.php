<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 05.08.18
 * Time: 15:27
 */

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{

    public function upload(UploadedFile $file,string $nameForFile = '', $targetDirectory = '')
    {
        if(empty($nameForFile)){
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        }else{
            $originalName = (string) $nameForFile;
        }
        $fileName = $this->slug($originalName).'.'.$file->getClientOriginalExtension();
        $file->move($targetDirectory, $fileName);
        return $fileName;
    }

    public function slug($slug) {
        $name = $slug;
        $slug = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [:Nonspacing Mark:] Remove; [:Punctuation:] Remove; Lower();',
            $slug
        );

        if (false == $slug) {
            throw new \RuntimeException('Unable to sluggize: ' . $name);
        }

        $slug = preg_replace('/\W/', ' ', $slug); //remove remaining nonword characters
        $slug = preg_replace('/[-\s]+/', '-', $slug);
        return $slug;
    }
}