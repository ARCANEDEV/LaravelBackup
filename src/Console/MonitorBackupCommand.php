<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Console;

use Arcanedev\LaravelBackup\Entities\BackupDestinationStatus;
use Arcanedev\LaravelBackup\Entities\BackupDestinationStatusCollection;
use Arcanedev\LaravelBackup\Events\HealthyBackupWasFound;
use Arcanedev\LaravelBackup\Events\UnhealthyBackupWasFound;
use Illuminate\Console\Command;

/**
 * Class     MonitorBackupCommand
 *
 * @package  Arcanedev\LaravelBackup\Console
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MonitorBackupCommand extends Command
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var string */
    protected $signature = 'backup:monitor';

    /** @var string */
    protected $description = 'Monitor the health of all backups.';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the command.
     *
     * @return int
     */
    public function handle(): int
    {
        $statuses = BackupDestinationStatusCollection::makeFromDestinations(
            $this->laravel['config']->get('backup.monitor.destinations', [])
        );

        $allAreHealthy = $statuses->every(function (BackupDestinationStatus $status) {
            $diskName = $status->backupDestination()->diskName();

            if ($status->isHealthy()) {
                $this->info("The backups on disk [{$diskName}] are considered healthy.");
                event(new HealthyBackupWasFound($status));

                return true;
            }
            else {
                $this->error("The backups on disk [{$diskName}] are considered unhealthy!");
                event(new UnhealthyBackupWasFound($status));

                return false;
            }
        });

        return $allAreHealthy ? 0 : 1;
    }
}
