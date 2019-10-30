<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Exceptions;

use Exception;

/**
 * Class     CannotCreateDbDumper
 *
 * @package  Arcanedev\LaravelBackup\Exceptions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CannotCreateDbDumper extends Exception
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * @param  string  $driver
     * @param  array   $supported
     *
     * @return static
     */
    public static function unsupportedDriver(string $driver, array $supported): self
    {
        $supported = implode('`, `', $supported);

        return new static(
            "Cannot create a dumper for db driver `{$driver}`. Use `{$supported}`."
        );
    }
}
