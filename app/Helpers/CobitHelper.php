<?php
namespace App\Helpers;

class CobitHelper
{
    public static function Media($filename, $path, $file)
    {
        $mime = array(
            'filename' => $filename,
            'path' => $path . $filename,
            // 'url'=>url('/'.$path.$filename),
            'size' => $file->getSize(),
            'type' => $file->getClientMimeType(),
            'originalname' => $file->getClientOriginalName(),
            'ext' => $file->extension()
        );
        return $mime;
    }
}
