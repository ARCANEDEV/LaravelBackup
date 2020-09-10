<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks;

use Arcanedev\LaravelBackup\Exceptions\InvalidHealthCheck;
use Illuminate\Support\Str;

/**
 * Class     AbstractHealthCheck
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class AbstractHealthCheck implements HealthCheckable
{
    /* -----------------------------------------------------------------
     |  Getters
     | -----------------------------------------------------------------
     */

    /**
     * Get the health check's name.
     *
     * @return string
     */
    public function name(): string
    {
        return Str::title(class_basename(static::class));
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Throw exception if the health check fails.
     *
     * @param  string  $message
     *
     * @throws \Arcanedev\LaravelBackup\Exceptions\InvalidHealthCheck
     */
    protected function fail(string $message)
    {
        throw InvalidHealthCheck::because($message);
    }

    /**
     * @param  bool    $condition
     * @param  string  $message
     */
    protected function failIf(bool $condition, string $message)
    {
        if ($condition) {
            $this->fail($message);
        }
    }

    /**
     * @param  bool    $condition
     * @param  string  $message
     */
    protected function failUnless(bool $condition, string $message)
    {
        $this->failIf( ! $condition, $message);
    }
}
