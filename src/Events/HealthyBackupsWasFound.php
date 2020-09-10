<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Events;

use Arcanedev\LaravelBackup\Actions\Monitor\MonitorPassable;
use Arcanedev\LaravelBackup\Entities\BackupDestinationStatusCollection;

/**
 * Class     HealthyBackupsWasFound
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class HealthyBackupsWasFound
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var \Arcanedev\LaravelBackup\Actions\Monitor\MonitorPassable */
    public $passable;

    /** @var \Arcanedev\LaravelBackup\Entities\BackupDestinationStatusCollection */
    public $statuses;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * HealthyBackupsWasFound constructor.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Monitor\MonitorPassable             $passable
     * @param  \Arcanedev\LaravelBackup\Entities\BackupDestinationStatusCollection  $statuses
     */
    public function __construct(MonitorPassable $passable, BackupDestinationStatusCollection $statuses)
    {
        $this->passable = $passable;
        $this->statuses = $statuses;
    }
}
