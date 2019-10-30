<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Backup\Tasks;

use Arcanedev\LaravelBackup\Actions\Backup\BackupPassable;
use Arcanedev\LaravelBackup\Exceptions\InvalidTaskOptions;
use Arcanedev\LaravelBackup\Actions\TaskInterface;
use Closure;

/**
 * Class     CheckOptions
 *
 * @package  Arcanedev\LaravelBackup\Actions\Backup\Tasks
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CheckOptions implements TaskInterface
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
        $this->checkOptions($passable);

        return $next($passable);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check the options.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable  $passable
     *
     * @throws \Arcanedev\LaravelBackup\Exceptions\InvalidTaskOptions
     */
    private function checkOptions(BackupPassable $passable)
    {
        if ($passable->isOnlyDatabases() && $passable->isOnlyFiles()) {
            throw new InvalidTaskOptions('Cannot use `only-db` and `only-files` together');
        }
    }
}
