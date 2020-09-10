<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Helpers;

use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Class     FileChecker
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class FileChecker
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var array */
    protected static $allowedMimeTypes = [
        'application/zip',
        'application/x-zip',
        'application/x-gzip',
    ];

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the given path is a zip file.
     *
     * @param  \Illuminate\Contracts\Filesystem\Filesystem|null  $disk
     * @param  string                                            $path
     *
     * @return bool
     */
    public static function isZipFile(?Filesystem $disk, string $path) : bool
    {
        return static::hasZipExtension($path)
            ? true
            : static::hasAllowedMimeType($disk, $path);
    }

    /**
     * Check if the given path has a zip extension.
     *
     * @param  string  $path
     *
     * @return bool
     */
    protected static function hasZipExtension(string $path): bool
    {
        return pathinfo($path, PATHINFO_EXTENSION) === 'zip';
    }

    /**
     * Check if has an allowed mimetype.
     *
     * @param  \Illuminate\Contracts\Filesystem\Filesystem|null  $disk
     * @param  string                                            $path
     * @return bool
     */
    protected static function hasAllowedMimeType(?Filesystem $disk, string $path): bool
    {
        return in_array(static::mimeType($disk, $path), self::$allowedMimeTypes);
    }

    /**
     * Get the mimetype of the given path.
     *
     * @param  \Illuminate\Contracts\Filesystem\Filesystem|null  $disk
     * @param  string                                            $path
     *
     * @return string|false
     */
    protected static function mimeType(?Filesystem $disk, string $path)
    {
        try {
            if ($disk && method_exists($disk, 'mimeType')) {
                return $disk->mimeType($path) ?: false;
            }
        }
        catch (Exception $exception) {
            // Some drivers throw exceptions when checking mime types, we'll
            // just fallback to `false`.
        }

        return false;
    }
}
