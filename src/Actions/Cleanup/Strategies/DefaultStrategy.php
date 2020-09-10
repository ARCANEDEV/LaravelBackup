<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Cleanup\Strategies;

use Arcanedev\LaravelBackup\Entities\{Backup, BackupCollection, Period};
use Illuminate\Support\Collection;

/**
 * Class     DefaultStrategy
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DefaultStrategy implements CleanupStrategy
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Entities\Backup */
    protected $newestBackup;

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the the maximum backup's size.
     *
     * @return float
     */
    protected function getMaximumSize(): float
    {
        return config('backup.cleanup.strategy.delete-backups.oldest-when-size-reach') * 1024 * 1024;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Cleanup the backups.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\BackupCollection  $backups
     * @param  \Illuminate\Support\Collection                      $periodRanges
     */
    public function cleanup(BackupCollection $backups, Collection $periodRanges): void
    {
        // Don't ever delete the newest backup.
        $this->newestBackup = $backups->shift();

        $backupsPerPeriod = $this->groupBackupsPerPeriod($backups, $periodRanges);

        $this->deleteBackupsForAllPeriodsExceptOne($backupsPerPeriod);

        $backups->deleteBackupsOlderThan($periodRanges['yearly']->endDate());

        $this->deleteOldBackupsUntilUsingLessThanMaximumStorage($backups);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Group backups per period.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\BackupCollection  $backups
     * @param  \Illuminate\Support\Collection                      $periodRanges
     *
     * @return \Illuminate\Support\Collection
     */
    protected function groupBackupsPerPeriod(BackupCollection $backups, Collection $periodRanges)
    {
        return $periodRanges->map(function (Period $period, string $key) use ($backups) {
            $dateFormat = Period::format($key);

            return $backups
                ->filter(function (Backup $backup) use ($period) {
                    return $backup->date()->between($period->startDate(), $period->endDate());
                })
                ->groupBy(function (Backup $backup) use ($dateFormat) {
                    return $backup->date()->format($dateFormat);
                });
        });
    }

    /**
     * Delete backups for all periods except one.
     *
     * @param  \Illuminate\Support\Collection  $backupsPerPeriod
     */
    protected function deleteBackupsForAllPeriodsExceptOne(Collection $backupsPerPeriod): void
    {
        $backupsPerPeriod->each(function (Collection $groupedBackups) {
            $groupedBackups->each(function (BackupCollection $backups) {
                $backups->deleteAllExceptOne();
            });
        });
    }

    /**
     * Delete old backups until the used storage is less than maximum size.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\BackupCollection  $backups
     */
    protected function deleteOldBackupsUntilUsingLessThanMaximumStorage(BackupCollection $backups)
    {
        if ( ! $oldest = $backups->oldest()) {
            return;
        }

        $maximumSize = $this->getMaximumSize();

        if (($backups->size() + $this->newestBackup->size()) <= $maximumSize) {
            return;
        }

        $oldest->delete();

        $this->deleteOldBackupsUntilUsingLessThanMaximumStorage(
            $backups->filter(function (Backup $backup) {
                return $backup->exists();
            })
        );
    }
}
