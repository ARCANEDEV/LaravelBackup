<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Backup;

use Arcanedev\LaravelBackup\Actions\Action;
use Arcanedev\LaravelBackup\Events\BackupActionHasFailed;
use Exception;

/**
 * Class     BackupAction
 *
 * @package  Arcanedev\LaravelBackup\Actions\Backup
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupAction extends Action
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the array of configured pipes.
     *
     * @return array
     */
    protected function pipes(): array
    {
        return [
            Tasks\CheckOptions::class,
            Tasks\CheckBackupDestinations::class,
            Tasks\CreateTemporaryDirectory::class,
            Tasks\PrepareFilesToBackup::class,
            Tasks\CreateBackupFile::class,
            Tasks\MoveBackupToDisks::class,
            Tasks\SendNotification::class,
        ];
    }

    /**
     * Run the task.
     *
     * @param  array  $options
     *
     * @return mixed
     */
    public function run(array $options)
    {
        $config = array_merge(
            config('backup.backup', []), compact('options')
        );

        return $this->send(new BackupPassable($config))
                    ->thenReturn();
    }

    /**
     * Handle the given exception.
     *
     * @param  mixed       $passable
     * @param  \Exception  $e
     *
     * @return mixed|void
     *
     * @throws \Exception
     */
    protected function handleException($passable, Exception $e)
    {
        event(new BackupActionHasFailed($passable, $e));

        parent::handleException($passable, $e);
    }
}
