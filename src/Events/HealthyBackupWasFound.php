<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Events;

use Arcanedev\LaravelBackup\Entities\BackupDestinationStatus;

/**
 * Class     HealthyBackupWasFound
 *
 * @package  Arcanedev\LaravelBackup\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class HealthyBackupWasFound
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * @var \Arcanedev\LaravelBackup\Entities\BackupDestinationStatus
     */
    public $backupDestinationStatus;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * HealthyBackupWasFound constructor.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\BackupDestinationStatus  $backupDestinationStatus
     */
    public function __construct(BackupDestinationStatus $backupDestinationStatus)
    {
        $this->backupDestinationStatus = $backupDestinationStatus;
    }
}
