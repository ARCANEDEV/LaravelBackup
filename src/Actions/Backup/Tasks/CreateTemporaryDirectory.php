<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Backup\Tasks;

use Arcanedev\LaravelBackup\Actions\TaskInterface;
use Closure;
use Illuminate\Support\Facades\File;

/**
 * Class     CreateTemporaryDirectory
 *
 * @package  Arcanedev\LaravelBackup\Tasks\RunBackup
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CreateTemporaryDirectory implements TaskInterface
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */
    /**
     * Handle the task.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable  $passable
     * @param  \Closure                                                $next
     *
     * @return mixed
     */
    public function handle($passable, Closure $next)
    {
        $path = $passable->temporaryDirectoryPath();

        static::createDirectory($path);

        return tap($next($passable), function () use ($path) {
            static::deleteDirectory($path);
        });
    }

    /**
     * Create directory.
     *
     * @param  string  $path
     *
     * @return bool
     */
    private static function createDirectory(string $path)
    {
        static::deleteDirectory($path); // Delete old temporary directory if exists

        return File::makeDirectory($path, 0755, true);
    }

    /**
     * Delete directory.
     *
     * @param  string  $path
     *
     * @return bool
     */
    private static function deleteDirectory(string $path): bool
    {
        return File::exists($path)
            ? File::deleteDirectory($path)
            : false;
    }
}
