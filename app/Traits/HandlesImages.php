<?php

namespace App\Traits;


trait HandlesImages
{
    public function uploadImage(Request $request, $fieldName, $directory)
    {
        if ($request->hasFile($fieldName)) {
            $file = $request->file($fieldName);
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path($directory), $filename);
            return $directory . '/' . $filename;
        }

        return null;
    }

    // audio file
    public function uploadAudio(Request $request, $fieldName, $directory)
    {
        if ($request->hasFile($fieldName)) {
            $file = $request->file($fieldName);
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path($directory), $filename);
            return $directory . '/' . $filename;
        }

        return null;
    }
}
