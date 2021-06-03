<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Monitor;

use Arcanedev\LaravelBackup\Actions\Passable;
use Arcanedev\LaravelBackup\Entities\BackupDestinationStatusCollection;
use Illuminate\Support\Collection;

/**
 * Class     MonitorPassable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MonitorPassable extends Passable
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * @var \Arcanedev\LaravelBackup\Entities\BackupDestinationStatusCollection
     */
    protected $healthyStatuses;

    /**
     * @var \Arcanedev\LaravelBackup\Entities\BackupDestinationStatusCollection
     */
    protected $unhealthyStatuses;

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get all the statuses.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllStatuses(): Collection
    {
        return $this->getHealthyStatuses()->merge($this->getUnhealthyStatuses());
    }

    /**
     * Get the healthy statuses.
     *
     * @return \Arcanedev\LaravelBackup\Entities\BackupDestinationStatusCollection|null
     */
    public function getHealthyStatuses(): ?BackupDestinationStatusCollection
    {
        return $this->healthyStatuses;
    }

    /**
     * Set the healthy statuses.
     *
     * @param \Arcanedev\LaravelBackup\Entities\BackupDestinationStatusCollection $statuses
     *
     * @return $this
     */
    public function setHealthyStatuses(BackupDestinationStatusCollection $statuses): self
    {
        $this->healthyStatuses = $statuses;

        return $this;
    }

    /**
     * Get the unhealthy statuses.
     *
     * @return \Arcanedev\LaravelBackup\Entities\BackupDestinationStatusCollection|null
     */
    public function getUnhealthyStatuses(): ?BackupDestinationStatusCollection
    {
        return $this->unhealthyStatuses;
    }

    /**
     * Set the unhealthy statuses.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\BackupDestinationStatusCollection  $statuses
     *
     * @return $this
     */
    public function setUnhealthyStatuses(BackupDestinationStatusCollection $statuses): self
    {
        $this->unhealthyStatuses = $statuses;

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Check methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if has unhealthy statuses.
     *
     * @return bool
     */
    public function hasUnhealthyStatuses(): bool
    {
        return $this->getUnhealthyStatuses()->isNotEmpty();
    }
}
