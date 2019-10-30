<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks;

use Arcanedev\LaravelBackup\Entities\BackupDestination;

/**
 * Class     IsReachable
 *
 * @package  Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class IsReachable extends AbstractHealthCheck
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check the backup destination.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\BackupDestination  $backupDestination
     *
     * @return mixed
     */
    public function check(BackupDestination $backupDestination)
    {
        $this->failUnless(
            $backupDestination->isReachable(),
            trans('backup::notification.unhealthy_backup_found_not_reachable', [
                'error' => $backupDestination->connectionError(),
            ])
        );
    }
}
