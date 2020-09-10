<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Cleanup;

use Arcanedev\LaravelBackup\Actions\Action;
use Arcanedev\LaravelBackup\Events\{CleanupActionHasFailed, CleanupActionWasSuccessful};
use Throwable;

/**
 * Class     CleanAction
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CleanAction extends Action
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
        return $this->container['config']['backup.cleanup.tasks'];
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
     * @return \Arcanedev\LaravelBackup\Actions\Cleanup\CleanupPassable
     */
    protected function makePassable(array $options): CleanupPassable
    {
        $options = array_merge(
            config('backup.cleanup', []),
            compact('options')
        );

        return new CleanupPassable($options);
    }

    /**
     * Handle the passable on success.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Cleanup\CleanupPassable|mixed  $passable
     *
     * @return \Arcanedev\LaravelBackup\Actions\Cleanup\CleanupPassable|mixed
     */
    protected function handleOnSuccess($passable)
    {
        event(new CleanupActionWasSuccessful($passable));

        return parent::handleOnSuccess($passable);
    }

    /**
     * Handle the given exception.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Cleanup\CleanupPassable|mixed  $passable
     * @param  \Throwable                                                      $e
     *
     * @return mixed|void
     *
     * @throws \Exception
     */
    protected function handleException($passable, Throwable $e)
    {
        event(new CleanupActionHasFailed($passable, $e));

        parent::handleException($passable, $e);
    }
}
