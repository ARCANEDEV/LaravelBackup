<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Backup\Tasks;

use Arcanedev\LaravelBackup\Events\BackupWasSuccessful;
use Arcanedev\LaravelBackup\Actions\TaskInterface;
use Closure;

/**
 * Class     SendNotification
 *
 * @package  Arcanedev\LaravelBackup\Tasks\RunBackup
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
     * @param  \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable  $passable
     * @param  \Closure                                                $next
     *
     * @return mixed
     */
    public function handle($passable, Closure $next)
    {
        if ( ! $passable->isNotificationsDisabled()) {
            event(new BackupWasSuccessful());
        }

        return $next($passable);
    }
}
