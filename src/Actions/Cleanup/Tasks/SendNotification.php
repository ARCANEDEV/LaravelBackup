<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Cleanup\Tasks;

use Arcanedev\LaravelBackup\Actions\TaskInterface;
use Arcanedev\LaravelBackup\Events\CleanupWasSuccessful;
use Closure;

/**
 * Class     SendNotification
 *
 * @package  Arcanedev\LaravelBackup\Actions\Cleanup\Tasks
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SendNotification implements TaskInterface
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the task.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Cleanup\CleanPassable  $passable
     * @param \Closure                                                 $next
     *
     * @return mixed
     */
    public function handle($passable, Closure $next)
    {
        if ( ! $passable->isNotificationsDisabled()) {
            event(new CleanupWasSuccessful());
        }

        return $next($passable);
    }
}
