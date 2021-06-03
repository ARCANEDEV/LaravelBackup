<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Backup\Tasks;

use Arcanedev\LaravelBackup\Actions\TaskInterface;
use Arcanedev\LaravelBackup\Entities\BackupDestinationCollection;
use Closure;

/**
 * Class     CheckBackupDestinations
 *
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
     * @param  \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable  $passable
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
