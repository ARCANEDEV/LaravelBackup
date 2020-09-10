<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks;

use Arcanedev\LaravelBackup\Entities\BackupDestination;

/**
 * Interface  HealthCheckable
 *
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface HealthCheckable
{
    /**
     * Check the backup destination.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\BackupDestination  $backupDestination
     *
     * @return mixed|void
     */
    public function check(BackupDestination $backupDestination);
}
