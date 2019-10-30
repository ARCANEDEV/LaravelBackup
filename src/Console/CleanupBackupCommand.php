<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Console;

use Arcanedev\LaravelBackup\Actions\Cleanup\CleanAction;
use Arcanedev\LaravelBackup\Entities\BackupDestination;
use Arcanedev\LaravelBackup\Helpers\Format;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

/**
 * Class     CleanupBackupCommand
 *
 * @package  Arcanedev\LaravelBackup\Console
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CleanupBackupCommand extends Command
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var string */
    protected $signature = 'backup:clean {--disable-notifications}';

    /** @var string */
    protected $description = 'Cleanup the older backups based on the configuration.';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the command.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Cleanup\CleanAction  $action
     *
     * @return int
     */
    public function handle(CleanAction $action): int
    {
        $this->comment('Starting cleanup...');

        try {
            $options  = $this->getCommandOptions();
            $passable = $action->run($options);

            $passable->getBackupDestinations()->each(function (BackupDestination $destination) {
                $usedStorage = Format::humanReadableSize($destination->fresh()->usedStorage());

                $this->info(
                    "Used storage after cleanup the {$destination->backupName()} on disk [{$destination->diskName()}] : {$usedStorage}"
                );
            });

            $this->comment('Cleanup completed!');

            return 0;
        }
        catch (Exception $e) {
            $this->error("Cleanup failed because: {$e->getMessage()}");

            return 1;
        }
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the commands options.
     *
     * @return array
     */
    private function getCommandOptions(): array
    {
        return Arr::only($this->options(), [
            'disable-notifications',
        ]);
    }
}
