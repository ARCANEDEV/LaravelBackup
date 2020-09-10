<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Entities;

use Exception;
use Illuminate\Support\Collection;

/**
 * Class     BackupDestinationCollection
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupDestinationCollection extends Collection
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make the backup destination from configuration.
     *
     * @return static
     */
    public static function makeFromConfig(): self
    {
        $backupName =
            config('backup.destination.filename-prefix').
            config('backup.name', config('app.name'));


        return static::makeFromDisksNames(
            config('backup.destination.disks', []), $backupName
        );
    }

    /**
     * Make the backup destination from disks.
     *
     * @param  array   $diskNames
     * @param  string  $backupName
     *
     * @return static
     */
    public static function makeFromDisksNames(array $diskNames, string $backupName): self
    {
        return static::make($diskNames)->mapWithKeys(function (string $disk) use ($backupName) {
            return [$disk => BackupDestination::makeFromDiskName($disk, $backupName)];
        });
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if all the backup destinations are reachable.
     *
     * @throws  \Exception
     */
    public function mustAllBeReachable(): void
    {
        $this->each(function (BackupDestination $destination) {
            if ( ! $destination->isReachable()) {
                throw new Exception(
                    "Could not connect to disk {$destination->diskName()} because: {$destination->connectionError()}"
                );
            }
        });
    }
}
