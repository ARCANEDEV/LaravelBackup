<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Exceptions;

use InvalidArgumentException;

/**
 * Class     InvalidDbDriverException
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class InvalidDbDriverException extends InvalidArgumentException
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make undefined driver exception.
     *
     * @param  string  $class
     *
     * @return static
     */
    public static function undefinedDriver(string $class): self
    {
        return new static(
            __("Unable to resolve NULL DB driver for [:class].", ['class' => $class])
        );
    }
}
