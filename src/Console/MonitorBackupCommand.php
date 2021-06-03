<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Console;

use Arcanedev\LaravelBackup\Actions\Monitor\MonitorAction;
use Arcanedev\LaravelBackup\Entities\BackupDestinationStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

/**
 * Class     MonitorBackupCommand
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MonitorBackupCommand extends Command
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var string */
    protected $signature = 'backup:monitor {--disable-notifications}';

    /** @var string */
    protected $description = 'Monitor the health of all backups.';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the command.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Monitor\MonitorAction  $action
     *
     * @return int
     */
    public function handle(MonitorAction $action): int
    {
        $options = Arr::only($this->options(), [
            'disable-notifications',
        ]);

        /** @var  \Arcanedev\LaravelBackup\Actions\Monitor\MonitorPassable  $passable */
        $passable = $action->execute($options);

        $passable->getHealthyStatuses()->each(function (BackupDestinationStatus $status) {
            $this->info(__('The backups on disk [:disk] are considered healthy.', [
                'disk' => $status->backupDestination()->diskName(),
            ]));
        });

        $passable->getUnhealthyStatuses()->each(function (BackupDestinationStatus $status) {
            $this->error(__('The backups on disk [:disk] are considered unhealthy!', [
                'disk' => $status->backupDestination()->diskName(),
            ]));
        });

        return $passable->hasUnhealthyStatuses()
            ? Command::FAILURE
            : Command::SUCCESS;
    }
}
