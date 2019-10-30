<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Entities;

use Arcanedev\LaravelBackup\Actions\Monitor\HealthCheckFailure;
use Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks\HealthCheckable;
use Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks\IsReachable;
use Exception;
use Illuminate\Support\{Arr, Collection};

/**
 * Class     BackupDestinationStatus
 *
 * @package  Arcanedev\LaravelBackup\Entities
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

    /** @var array */
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
        $this->healthChecks = $healthChecks;
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
        return Collection::make($this->healthChecks)->prepend(new IsReachable);
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
            static::buildHealthChecks($config)
        );
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

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Build health checks.
     *
     * @param  array  $config
     *
     * @return array
     */
    protected static function buildHealthChecks(array $config): array
    {
        return Collection::make(Arr::get($config, 'health-checks'))
            ->transform(function (array $options, string $class) {
                return new $class(...$options);
            })
            ->values()
            ->toArray();
    }
}
