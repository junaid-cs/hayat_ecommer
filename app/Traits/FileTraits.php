<?php

namespace App\Traits;

trait FileTraits
{
    // multiple file upload
    public function uploadMultipleFiles($files, $path)
    {
        $filenames = [];
        foreach ($files as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($path), $filename);
            $filenames[] = $filename;
        }
        return $filenames;
    }
    // single image upload function
    public function uploadSingleImage($file, $path)
    {
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($path), $filename);
        return $filename;
    }

    // delete file function
    public function deleteFile($filePath)
    {
        if (file_exists(public_path($filePath))) {
            unlink(public_path($filePath));
            return true;
        }
        return false;
    }
}
