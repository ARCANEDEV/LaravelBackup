<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Backup;

use Arcanedev\LaravelBackup\Actions\Action;
use Arcanedev\LaravelBackup\Events\{BackupActionHasFailed, BackupActionWasSuccessful};
use Throwable;

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
        return $this->container['config']['backup.backup.tasks'];
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make the passable object.
     *
     * @param  array  $options
     *
     * @return \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable
     */
    protected function makePassable(array $options): BackupPassable
    {
        $options = array_merge(
            config('backup.backup', []),
            compact('options')
        );

        return new BackupPassable($options);
    }

    /**
     * Handle the passable on success.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable|mixed  $passable
     *
     * @return \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable|mixed
     */
    protected function handleOnSuccess($passable)
    {
        event(new BackupActionWasSuccessful($passable));

        return parent::handleOnSuccess($passable);
    }

    /**
     * Handle the given exception.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable|mixed  $passable
     * @param  \Throwable                                                    $e
     *
     * @return mixed|void
     *
     * @throws \Exception
     */
    protected function handleException($passable, Throwable $e)
    {
        event(new BackupActionHasFailed($passable, $e));

        parent::handleException($passable, $e);
    }
}
