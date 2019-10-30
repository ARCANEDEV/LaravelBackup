<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Events;

use Arcanedev\LaravelBackup\Actions\monitor\MonitorPassable;
use Exception;

/**
 * Class     MonitorActionHasFailed
 *
 * @package  Arcanedev\LaravelBackup\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MonitorActionHasFailed
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Actions\monitor\MonitorPassable */
    public $passable;

    /** @var  \Exception */
    public $exception;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * MonitorActionHasFailed constructor.
     * @param \Arcanedev\LaravelBackup\Actions\monitor\MonitorPassable $passable
     * @param \Exception $exception
     */
    public function __construct(MonitorPassable $passable, Exception $exception)
    {
        $this->passable = $passable;
        $this->exception = $exception;
    }
}
