<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Exceptions;

use Exception;

/**
 * Class     CannotSetDatabaseParameter
 *
 * @package  Arcanedev\LaravelBackup\Exceptions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CannotSetDatabaseParameter extends Exception
{
    /**
     * @param string $name
     * @param string $conflictName
     *
     * @return static
     */
    public static function conflictingParameters($name, $conflictName): self
    {
        return new static(
            "Cannot set `{$name}` because it conflicts with parameter `{$conflictName}`."
        );
    }
}
