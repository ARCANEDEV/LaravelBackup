<?php

namespace Arcanedev\LaravelBackup\Tests\Concerns;

use DateTime;
use Illuminate\Support\Facades\Storage;

/**
 * Trait     HasDisksManipulation
 *
 * @package  Arcanedev\LaravelBackup\Tests\Concerns
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
trait HasDisksManipulation
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * @param  string     $diskName
     * @param  string     $filePath
     * @param  \DateTime  $date
     *
     * @return string
     */
    protected static function create1MbFileOnDisk(string $diskName, string $filePath, DateTime $date): string
    {
        $file = self::getStubsDirectory('files/1Mb.file');

        return static::createFileOnDisk(
            $diskName, $filePath, $date, file_get_contents($file)
        );
    }

    /**
     * Create a file on disk.
     *
     * @param  string     $diskName
     * @param  string     $filePath
     * @param  \DateTime  $date
     * @param  string     $content
     *
     * @return string
     */
    protected static function createFileOnDisk(string $diskName, string $filePath, DateTime $date, string $content = 'dummy content'): string
    {
        Storage::disk($diskName)->put($filePath, $content);

        touch(static::getFullDiskPath($diskName, $filePath), $date->getTimestamp());

        return $filePath;
    }

    /**
     * Delete a directory on a disk.
     *
     * @param  string  $diskName
     * @param  string  $directory
     *
     * @return bool
     */
    protected static function deleteDirectoryOnDisk(string $diskName, string $directory): bool
    {
        return Storage::disk($diskName)->deleteDirectory($directory);
    }

    /**
     * Get the full disk path.
     *
     * @param  string  $diskName
     * @param  string  $filePath
     *
     * @return string
     */
    protected static function getFullDiskPath(string $diskName, string $filePath): string
    {
        return static::getDiskRootPath($diskName).DIRECTORY_SEPARATOR.$filePath;
    }

    /**
     * Get the disk root path.
     *
     * @param  string  $diskName
     *
     * @return string
     */
    protected static function getDiskRootPath(string $diskName): string
    {
        return Storage::disk($diskName)->getDriver()->getAdapter()->getPathPrefix();
    }
}
