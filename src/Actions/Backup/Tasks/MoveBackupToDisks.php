<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Backup\Tasks;

use Arcanedev\LaravelBackup\Actions\TaskInterface;
use Arcanedev\LaravelBackup\Entities\BackupDestination;
use Closure;

/**
 * Class     MoveBackupToDisks
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MoveBackupToDisks implements TaskInterface
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
        $backupFile = $passable->zip()->path();

        $passable->getBackupDestinations()->each(function(BackupDestination $destination) use ($backupFile) {
            $destination->write($backupFile);
            // Dispatch an event
        });

        return $next($passable);
    }
}
