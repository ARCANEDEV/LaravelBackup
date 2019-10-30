<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Cleanup\Tasks;

use Arcanedev\LaravelBackup\Actions\TaskInterface;
use Arcanedev\LaravelBackup\Entities\BackupDestinationCollection;
use Closure;

/**
 * Class     CheckBackupDestinations
 *
 * @package  Arcanedev\LaravelBackup\Actions\Cleanup\Tasks
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CheckBackupDestinations implements TaskInterface
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the task.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Cleanup\CleanPassable  $passable
     * @param  \Closure                                                $next
     *
     * @return mixed
     */
    public function handle($passable, Closure $next)
    {
        $passable->setBackupDestinations(
            tap(BackupDestinationCollection::makeFromConfig(), function (BackupDestinationCollection $destinations) {
                $destinations->mustAllBeReachable();
            })
        );

        return $next($passable);
    }
}
