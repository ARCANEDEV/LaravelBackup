<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Console;

use Arcanedev\LaravelBackup\Actions\Backup\BackupAction;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

/**
 * Class     RunBackupCommand
 *
 * @package  Arcanedev\LaravelBackup\Console
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RunBackupCommand extends Command
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var string */
    protected $signature = 'backup:run {--filename=} {--only-db} {--db-name=*} {--only-files} {--only-to-disk=} {--disable-notifications}';

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
        $this->comment('Starting backup...');

        try {
            $options = $this->getCommandOptions();

            $action->run($options);

            $this->comment('Backup completed!');

            return 0;
        }
        catch (Exception $e) {
            $this->error("Backup failed because: {$e->getMessage()}");

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
            'filename', 'only-db', 'db-name', 'only-files', 'only-to-disk', 'disable-notifications',
        ]);
    }
}
