<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Exceptions;

/**
 * Class     InvalidHealthCheck
 *
 * @package  Arcanedev\LaravelBackup\Exceptions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class InvalidHealthCheck extends \Exception
{
    /**
     * @param  string  $message
     *
     * @return static
     */
    public static function because(string $message): self
    {
        return new static($message);
    }
}
