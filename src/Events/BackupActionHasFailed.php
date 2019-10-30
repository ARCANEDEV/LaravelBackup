<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Events;

use Arcanedev\LaravelBackup\Actions\Backup\BackupPassable;
use Exception;

/**
 * Class     BackupHasFailed
 *
 * @package  Arcanedev\LaravelBackup\Events
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
     * @param  \Exception                                              $exception
     */
    public function __construct(BackupPassable $passable, Exception $exception)
    {
        $this->exception = $exception;

        $this->passable = $passable;
    }
}
