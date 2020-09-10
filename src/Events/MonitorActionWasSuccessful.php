<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Events;

use Arcanedev\LaravelBackup\Actions\monitor\MonitorPassable;

/**
 * Class     MonitorActionWasSuccessful
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MonitorActionWasSuccessful
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Actions\monitor\MonitorPassable */
    public $passable;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * MonitorActionHasFailed constructor.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\monitor\MonitorPassable  $passable
     */
    public function __construct(MonitorPassable $passable)
    {
        $this->passable = $passable;
    }
}
