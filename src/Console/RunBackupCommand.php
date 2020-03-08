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
        $this->comment(__('Starting backup...'));

        try {
            $options = Arr::only($this->options(), [
                'filename', 'only-db', 'db-name', 'only-files', 'only-to-disk', 'disable-notifications',
            ]);

            $action->execute($options);

            $this->comment(__('Backup completed!'));

            return 0;
        }
        catch (Exception $e) {
            $this->error(__('Backup failed because: :message', ['message' => $e->getMessage()]));

            return 1;
        }
    }
}
