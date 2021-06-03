<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Events;

use Arcanedev\LaravelBackup\Actions\Backup\BackupPassable;
use Exception;
use Throwable;

/**
 * Class     BackupActionHasFailed
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupActionHasFailed
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable */
    public $passable;

    /** @var \Exception */
    public $exception;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * BackupHasFailed constructor.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable  $passable
     * @param  \Throwable                                              $exception
     */
    public function __construct(BackupPassable $passable, Throwable $exception)
    {
        $this->passable  = $passable;
        $this->exception = $exception;
    }
}
