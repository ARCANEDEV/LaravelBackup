<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Cleanup\Strategies;

use Arcanedev\LaravelBackup\Entities\BackupCollection;
use Illuminate\Support\Collection;

/**
 * Interface  CleanupStrategy
 *
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface CleanupStrategy
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Cleanup the old backups.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\BackupCollection  $backups
     * @param  \Illuminate\Support\Collection                      $periodRanges
     */
    public function cleanup(BackupCollection $backups, Collection $periodRanges): void;
}
