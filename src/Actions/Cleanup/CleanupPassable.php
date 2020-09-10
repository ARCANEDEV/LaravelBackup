<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Cleanup;

use Arcanedev\LaravelBackup\Actions\Passable;
use Arcanedev\LaravelBackup\Entities\{BackupDestinationCollection, Period};
use Illuminate\Support\Collection;

/**
 * Class     CleanupPassable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CleanupPassable extends Passable
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
     * @return \Arcanedev\LaravelBackup\Entities\BackupDestinationCollection|\Arcanedev\LaravelBackup\Entities\BackupDestination[]|null
     */
    public function getBackupDestinations(): ?BackupDestinationCollection
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
     * Get the period ranges.
     *
     * @return \Illuminate\Support\Collection
     */
    public function periodRanges(): Collection
    {
        return Period::makeRanges($this->getConfig('strategy.keep-backups', []));
    }
}
