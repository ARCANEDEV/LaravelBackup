<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Exceptions;

use Exception;

/**
 * Class     CannotStartDatabaseDump
 *
 * @package  Arcanedev\LaravelBackup\Exceptions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CannotStartDatabaseDump extends Exception
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public static function emptyParameter(string $name): self
    {
        return new static("Parameter `{$name}` cannot be empty.");
    }
}
