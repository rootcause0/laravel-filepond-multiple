<?php

namespace rootcause0\LaravelFilepond;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use rootcause0\LaravelFilepond\Exceptions\InvalidPathException;

class Filepond
{
    /**
     * Converts the given path into a filepond server id
     *
     * @param  string $path
     *
     * @return string
     */
    public function getServerIdFromPath($path)
    {
        return Crypt::encryptString($path);
    }

    /**
     * Converts the given filepond server id into a path
     *
     * @param  string $serverId
     *
     * @return string
     */
    public function getPathFromServerId($serverId)
    {
        $filePath = array();
        foreach($serverId as $serverI)
        {
           if(!is_null($serverI) && !str_contains($serverI,'://'))       //Protocol Identifier vasıtasıyla dosyamızın pre-load olup olmadığını kontrol ediyoruz.
           {
               array_push($filePath, Crypt::decryptString($serverI));
           }
        }
        return $filePath;
    }

    /**
     * Get the storage base path for files.
     *
     * @return string
     */
    public function getBasePath()
    {
        return Storage::disk(config('filepond.temporary_files_disk', 'local'))
            ->path(config('filepond.temporary_files_path', 'filepond'));
    }
}
