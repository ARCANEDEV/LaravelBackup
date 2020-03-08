<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Monitor;

use Arcanedev\LaravelBackup\Actions\Action;
use Arcanedev\LaravelBackup\Events\{MonitorActionHasFailed, MonitorActionWasSuccessful};
use Exception;

/**
 * Class     MonitorAction
 *
 * @package  Arcanedev\LaravelBackup\Actions\Monitor
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MonitorAction extends Action
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
        return $this->container['config']['backup.monitor.tasks'];
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
     * @return \Arcanedev\LaravelBackup\Actions\Monitor\MonitorPassable
     */
    protected function makePassable(array $options): MonitorPassable
    {
        $options = array_merge(
            $this->container['config']['backup.monitor'] ?? [],
            compact('options')
        );

        return new MonitorPassable($options);
    }

    /**
     * Handle the passable on success.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Monitor\MonitorPassable|mixed  $passable
     *
     * @return \Arcanedev\LaravelBackup\Actions\Monitor\MonitorPassable|mixed
     */
    protected function handleOnSuccess($passable)
    {
        event(new MonitorActionWasSuccessful($passable));

        return parent::handleOnSuccess($passable);
    }

    /**
     * Handle the given exception.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Monitor\MonitorPassable|mixed  $passable
     * @param  \Exception                                                      $e
     *
     * @return mixed|void
     *
     * @throws \Exception
     */
    protected function handleException($passable, Exception $e)
    {
        event(new MonitorActionHasFailed($passable, $e));

        parent::handleException($passable, $e);
    }
}
