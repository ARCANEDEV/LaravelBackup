<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Backup\Tasks;

use Arcanedev\LaravelBackup\Actions\TaskInterface;
use Arcanedev\LaravelBackup\Entities\{Backup, Manifest};
use Arcanedev\LaravelBackup\Events\BackupZipWasCreated;
use Arcanedev\LaravelBackup\Helpers\Zip;
use Closure;
use Illuminate\Support\Carbon;

/**
 * Class     CreateBackupFile
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CreateBackupFile implements TaskInterface
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
        $zip = static::createZipFile(
            $passable->manifest(),
            $passable->temporaryDirectoryPath()
        );

        $passable->setZip($zip);

        event(new BackupZipWasCreated($zip));

        return $next($passable);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Create the zip archive.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\Manifest  $manifest
     * @param  string                                      $path
     *
     * @return \Arcanedev\LaravelBackup\Helpers\Zip
     */
    protected static function createZipFile(Manifest $manifest, string $path): Zip
    {
        $zipPath = $path.DIRECTORY_SEPARATOR.static::getFilename();

        return tap(new Zip($zipPath), function (Zip $zip) use ($manifest) {
            $zip->create();
            $zip->addFilesFromManifest($manifest);
            $zip->close();
        });
    }

    /**
     * Get the zip filename.
     *
     * @return string
     */
    protected static function getFilename(): string
    {
        return Carbon::now()->format(Backup::FILENAME_FORMAT);
    }
}
