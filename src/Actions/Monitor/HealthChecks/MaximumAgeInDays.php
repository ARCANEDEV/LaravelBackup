<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks;

use Arcanedev\LaravelBackup\Entities\Backup;
use Arcanedev\LaravelBackup\Entities\BackupDestination;
use Carbon\Carbon;

/**
 * Class     MaximumAgeInDays
 *
 * @package  Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MaximumAgeInDays extends AbstractHealthCheck
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  int|null */
    protected $days;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * MaximumAgeInDays constructor.
     *
     * @param  int|null  $days
     */
    public function __construct($days = 1)
    {
        $this->days = $days;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check the backup destination.
     *
     * @param \Arcanedev\LaravelBackup\Entities\BackupDestination $backupDestination
     *
     * @return mixed|void
     */
    public function check(BackupDestination $backupDestination)
    {
        $this->failIf(
            $this->hasNoBackups($backupDestination),
            trans('backup::notifications.unhealthy_backup_found_empty')
        );

        $newestBackup = $backupDestination->backups()->newest();

        $this->failIf(
            static::isBackupTooOld($newestBackup),
            trans('backup::notifications.unhealthy_backup_found_old', [
                'date' => $newestBackup->date()->format('Y/m/d h:i:s'),
            ])
        );
    }

    /**
     * Check if the destination has no backups.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\BackupDestination  $backupDestination
     *
     * @return bool
     */
    protected function hasNoBackups(BackupDestination $backupDestination)
    {
        return $backupDestination->backups()->isEmpty();
    }

    /**
     * Check if the backup is old.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\Backup  $backup
     *
     * @return bool
     */
    protected function isBackupTooOld(Backup $backup)
    {
        if (is_null($this->days)) {
            return false;
        }

        if ($backup->date()->gt(Carbon::now()->subDays($this->days))) {
            return false;
        }

        return true;
    }
}
