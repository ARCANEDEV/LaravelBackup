<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Console;

use Arcanedev\LaravelBackup\Actions\Backup\BackupAction;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

/**
 * Class     RunBackupCommand
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RunBackupCommand extends Command
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var string */
    protected $signature = 'backup:run {--filename=} {--only-db} {--db-name=*} {--only-files} {--only-to-disk=} {--disable-notifications} {--timeout=}';

    /** @var string */
    protected $description = 'Run the backup.';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the command.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Backup\BackupAction  $action
     *
     * @return int
     */
    public function handle(BackupAction $action): int
    {
        $this->comment(__('Starting backup...'));

        $this->setTimeout($this->option('timeout'));

        try {
            $allowedOptions = [
                'filename',
                'only-db',
                'db-name',
                'only-files',
                'only-to-disk',
                'disable-notifications',
            ];

            $action->execute(Arr::only($this->options(), $allowedOptions));

            $this->comment(__('Backup completed!'));

            return Command::SUCCESS;
        }
        catch (Exception $e) {
            $this->error(__('Backup failed because: :message', ['message' => $e->getMessage()]));

            return Command::FAILURE;
        }
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */


    /**
     * Set timeout.
     *
     * @param  string|int|null  $timeout
     */
    private function setTimeout($timeout): void
    {
        if ($timeout && is_numeric($timeout)) {
            set_time_limit((int) $this->option('timeout'));
        }
    }
}
