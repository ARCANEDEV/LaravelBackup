<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Monitor\Tasks;

use Arcanedev\LaravelBackup\Actions\TaskInterface;
use Arcanedev\LaravelBackup\Entities\{BackupDestinationStatus, BackupDestinationStatusCollection};
use Arcanedev\LaravelBackup\Events\{HealthyBackupsWasFound, UnhealthyBackupsWasFound};
use Closure;

/**
 * Class     CheckBackupsHealth
 *
 * @package  Arcanedev\LaravelBackup\Actions\Monitor\Tasks
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CheckBackupsHealth implements TaskInterface
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the task.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Monitor\MonitorPassable|mixed  $passable
     * @param  \Closure                                                        $next
     *
     * @return mixed
     */
    public function handle($passable, Closure $next)
    {
        $statuses = BackupDestinationStatusCollection::makeFromDestinations(
            $passable->getConfig('destinations')
        );

        /**
         * @var  \Arcanedev\LaravelBackup\Entities\BackupDestinationStatusCollection  $healthyStatuses
         * @var  \Arcanedev\LaravelBackup\Entities\BackupDestinationStatusCollection  $unhealthyStatuses
         */
        [$healthyStatuses, $unhealthyStatuses] = $statuses->partition(function (BackupDestinationStatus $status) {
            return $status->isHealthy();
        });

        $passable->setHealthyStatuses($healthyStatuses)
                 ->setUnhealthyStatuses($unhealthyStatuses);

        if ($healthyStatuses->isNotEmpty())
            event(new HealthyBackupsWasFound($passable, $healthyStatuses));

        if ($unhealthyStatuses->isNotEmpty())
            event(new UnhealthyBackupsWasFound($passable, $unhealthyStatuses));

        return $next($passable);
    }
}
