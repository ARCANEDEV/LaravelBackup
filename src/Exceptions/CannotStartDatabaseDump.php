<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Exceptions;

use Exception;

/**
 * Class     CannotStartDatabaseDump
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CannotStartDatabaseDump extends Exception
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * @param  string  $name
     *
     * @return static
     */
    public static function emptyParameter(string $name): self
    {
        return new static(
            __("Parameter `:name` cannot be empty.", ['name' => $name])
        );
    }
}
