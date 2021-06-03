<?php declare(strict_types=1);

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
        $this->comment(__('Starting cleanup...'));

        try {
            $options  = Arr::only($this->options(), [
                'disable-notifications',
            ]);

            $passable = $action->execute($options);

            $passable->getBackupDestinations()->each(function (BackupDestination $destination) {
                $this->info(
                    __("Used storage after cleanup the :backup_name on disk [:disk_name] : :used_storage", [
                        'backup_name'  => $destination->backupName(),
                        'disk_name'    => $destination->diskName(),
                        'used_storage' => Format::humanReadableSize($destination->fresh()->usedStorage()),
                    ])
                );
            });

            $this->comment(__('Cleanup completed!'));

            return Command::SUCCESS;
        }
        catch (Exception $e) {
            $this->error(__("Cleanup failed because: :message", ['message' => $e->getMessage()]));

            return Command::FAILURE;
        }
    }
}
