<?php

namespace App\Helpers;

class GetPathHelper
{
    public static function getPublicUploadPath()
    {
        return 'uploads/';
    }

    public static function getUploadPath($user_type)
    {
        return 'uploads/'.$user_type.'/';
    }

    public static function getFileMetaData($file)
    {
        //$dataFile['name'] = $file->getClientOriginalName();
        $dataFile['ext'] = $file->getClientOriginalExtension();
        $dataFile['type'] = $file->getClientMimeType();
        $dataFile['size'] = self::formatBytes($file->getSize());
        return $dataFile;
    }

    public static function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array( 'B', 'KB', 'MB', 'GB', 'TB' );

        return round(pow(1024, $base - floor($base)), $precision).' '.$suffixes[floor($base)];
    }

    public static function getDefaultUserImage()
    {
        return asset('global_assets/images/user.png');
    }
}
