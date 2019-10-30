<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Cleanup;

use Arcanedev\LaravelBackup\Actions\Action;
use Arcanedev\LaravelBackup\Events\CleanActionHasFailed;
use Exception;

/**
 * Class     CleanAction
 *
 * @package  Arcanedev\LaravelBackup\Actions\Cleanup
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
        return [
            Tasks\CheckBackupDestinations::class,
            Tasks\ApplyCleanupStrategy::class,
            Tasks\SendNotification::class,
        ];
    }

    /**
     * Run the task.
     *
     * @param  array  $options
     *
     * @return \Arcanedev\LaravelBackup\Actions\Cleanup\CleanPassable
     */
    public function run(array $options)
    {
        $config = array_merge(
            config('backup.cleanup', []), compact('options')
        );

        return $this->send(new CleanPassable($config))
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
        event(new CleanActionHasFailed($passable, $e));

        parent::handleException($passable, $e);
    }
}
