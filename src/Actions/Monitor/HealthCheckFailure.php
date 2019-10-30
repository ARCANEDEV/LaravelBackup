<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Monitor;

use Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks\HealthCheckable;
use Arcanedev\LaravelBackup\Exceptions\InvalidHealthCheck;
use Exception;

/**
 * Class     HealthCheckFailure
 *
 * @package  Arcanedev\LaravelBackup\Actions\Monitor
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class HealthCheckFailure
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var \Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks\HealthCheckable */
    protected $healthCheck;

    /** @var \Exception */
    protected $exception;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * HealthCheckFailure constructor.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks\HealthCheckable  $healthCheck
     * @param  \Exception                                                             $exception
     */
    public function __construct(HealthCheckable $healthCheck, Exception $exception)
    {
        $this->healthCheck = $healthCheck;
        $this->exception = $exception;
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the health check instance.
     *
     * @return \Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks\HealthCheckable
     */
    public function healthCheck(): HealthCheckable
    {
        return $this->healthCheck;
    }

    /**
     * Get the exception.
     *
     * @return \Exception
     */
    public function exception(): Exception
    {
        return $this->exception;
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the exception was unexpected.
     *
     * @return bool
     */
    public function wasUnexpected(): bool
    {
        return ! $this->exception instanceof InvalidHealthCheck;
    }
}
