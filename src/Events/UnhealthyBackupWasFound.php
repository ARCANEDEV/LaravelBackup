<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Events;

use Arcanedev\LaravelBackup\Entities\BackupDestinationStatus;

/**
 * Class     UnhealthyBackupWasFound
 *
 * @package  Arcanedev\LaravelBackup\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UnhealthyBackupWasFound
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * @var \Arcanedev\LaravelBackup\Entities\BackupDestinationStatus
     */
    private $backupDestinationStatus;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * UnhealthyBackupWasFound constructor.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\BackupDestinationStatus  $backupDestinationStatus
     */
    public function __construct(BackupDestinationStatus $backupDestinationStatus)
    {
        $this->backupDestinationStatus = $backupDestinationStatus;
    }
}
