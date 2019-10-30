<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Entities;

use Illuminate\Support\Collection;

/**
 * Class     BackupDestinationStatusCollection
 *
 * @package  Arcanedev\LaravelBackup\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupDestinationStatusCollection extends Collection
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make the backup destination statuses from configuration.
     *
     * @param  array  $config
     *
     * @return static|\Arcanedev\LaravelBackup\Entities\BackupDestinationStatus[]
     */
    public static function makeFromDestinations(array $config): self
    {
        return static::make($config)->flatMap(function (array $config) {
            return Collection::make($config['disks'])->transform(function ($diskName) use ($config) {
                return BackupDestinationStatus::make($diskName, $config);
            });
        });
    }
}
