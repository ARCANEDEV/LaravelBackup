<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Cleanup;

use Arcanedev\LaravelBackup\Actions\Passable;
use Arcanedev\LaravelBackup\Entities\BackupDestinationCollection;
use Arcanedev\LaravelBackup\Entities\Period;
use Illuminate\Support\Collection;

/**
 * Class     CleanPassable
 *
 * @package  Arcanedev\LaravelBackup\Actions\Cleanup
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CleanPassable extends Passable
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Entities\BackupDestinationCollection */
    protected $backupDestinations;

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the backup destinations.
     *
     * @return \Arcanedev\LaravelBackup\Entities\BackupDestinationCollection|\Arcanedev\LaravelBackup\Entities\BackupDestination[]
     */
    public function getBackupDestinations(): BackupDestinationCollection
    {
        return $this->backupDestinations;
    }

    /**
     * Set the backup destinations.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\BackupDestinationCollection  $backupDestinations
     *
     * @return $this
     */
    public function setBackupDestinations(BackupDestinationCollection $backupDestinations): self
    {
        $this->backupDestinations = $backupDestinations;

        return $this;
    }

    /**
     * Check if the notifications are disabled.
     *
     * @return bool
     */
    public function isNotificationsDisabled(): bool
    {
        return $this->getConfig('options.disable-notifications', false);
    }

    /**
     * Get the period ranges.
     *
     * @return \Illuminate\Support\Collection
     */
    public function periodRanges(): Collection
    {
        return Period::makeRanges($this->getConfig('strategy.keep-backups', []));
    }
}
