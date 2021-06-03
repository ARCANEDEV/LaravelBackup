<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Entities;

use Arcanedev\LaravelBackup\Actions\Monitor\HealthCheckFailure;
use Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks\{HealthCheckable, IsReachable};
use Exception;
use Illuminate\Support\Collection;

/**
 * Class     BackupDestinationStatus
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupDestinationStatus
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Entities\BackupDestination */
    protected $backupDestination;

    /** @var \Illuminate\Support\Collection */
    protected $healthChecks;

    /** @var HealthCheckFailure|null */
    protected $healthCheckFailure;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * BackupDestinationStatus constructor.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\BackupDestination  $backupDestination
     * @param  array                                                $healthChecks
     */
    public function __construct(BackupDestination $backupDestination, array $healthChecks = [])
    {
        $this->backupDestination = $backupDestination;

        $this->setHealthChecks(
            Collection::make($healthChecks)->prepend(new IsReachable)
        );
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the backup destination.
     *
     * @return \Arcanedev\LaravelBackup\Entities\BackupDestination
     */
    public function backupDestination(): BackupDestination
    {
        return $this->backupDestination;
    }

    /**
     * Get the health checks.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getHealthChecks(): Collection
    {
        return $this->healthChecks;
    }

    /**
     * Set the health checks.
     *
     * @param  \Illuminate\Support\Collection  $healthChecks
     *
     * @return $this
     */
    public function setHealthChecks(Collection $healthChecks)
    {
        $this->healthChecks = $healthChecks;

        return $this;
    }

    /**
     * Get the check failure.
     *
     * @return \Arcanedev\LaravelBackup\Actions\Monitor\HealthCheckFailure|null
     */
    public function getHealthCheckFailure(): ?HealthCheckFailure
    {
        return $this->healthCheckFailure;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make a backup destination status instance.
     *
     * @param  string  $diskName
     * @param  array   $config
     *
     * @return static
     */
    public static function make(string $diskName, array $config): self
    {
        return new static(
            BackupDestination::makeFromDiskName($diskName, $config['name']),
            static::buildHealthChecks($config['health-checks'])
        );
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the status is healthy.
     *
     * @return bool
     */
    public function isHealthy(): bool
    {
        foreach ($this->getHealthChecks() as $healthCheck) {
            $checkResult = $this->check($healthCheck);

            if ($checkResult instanceof HealthCheckFailure) {
                $this->healthCheckFailure = $checkResult;

                return false;
            }
        }

        return true;
    }

    /**
     * Check with the health check.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks\HealthCheckable  $healCheck
     *
     * @return \Arcanedev\LaravelBackup\Actions\Monitor\HealthCheckFailure|bool
     */
    public function check(HealthCheckable $healCheck)
    {
        try {
            $healCheck->check($this->backupDestination());

            return true;
        }
        catch (Exception $exception) {
            return new HealthCheckFailure($healCheck, $exception);
        }
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Build health checks.
     *
     * @param  array  $healthChecks
     *
     * @return array
     */
    protected static function buildHealthChecks(array $healthChecks): array
    {
        return Collection::make($healthChecks)
            ->transform(function (array $options, string $class) {
                return new $class(...$options);
            })
            ->values()
            ->toArray();
    }
}
