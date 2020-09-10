<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Cleanup\Tasks;

use Arcanedev\LaravelBackup\Actions\Cleanup\Strategies\CleanupStrategy;
use Arcanedev\LaravelBackup\Actions\TaskInterface;
use Arcanedev\LaravelBackup\Entities\BackupDestination;
use Closure;

/**
 * Class     ApplyCleanupStrategy
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ApplyCleanupStrategy implements TaskInterface
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Actions\Cleanup\Strategies\DefaultStrategy */
    protected $strategy;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * ApplyCleanupStrategy constructor.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Cleanup\Strategies\CleanupStrategy  $strategy
     */
    public function __construct(CleanupStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the task.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Cleanup\CleanupPassable  $passable
     * @param  \Closure                                                $next
     *
     * @return mixed
     */
    public function handle($passable, Closure $next)
    {
        $periodRanges = $passable->periodRanges();

        $passable->getBackupDestinations()->each(function (BackupDestination $destination) use ($periodRanges) {
            $this->strategy->cleanup($destination->backups(), $periodRanges);
        });

        return $next($passable);
    }
}
