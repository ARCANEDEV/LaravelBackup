<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks;

use Arcanedev\LaravelBackup\Entities\BackupDestination;

/**
 * Class     IsReachable
 *
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
     * @return mixed|void
     */
    public function check(BackupDestination $backupDestination)
    {
        $this->failUnless(
            $backupDestination->isReachable(),
            __('The backup destination cannot be reached. :error', [
                'error' => $backupDestination->connectionError(),
            ])
        );
    }
}
