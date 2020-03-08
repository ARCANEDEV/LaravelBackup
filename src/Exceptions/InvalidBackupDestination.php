<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Exceptions;

use Exception;

/**
 * Class     InvalidBackupDestination
 *
 * @package  Arcanedev\LaravelBackup\Exceptions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class InvalidBackupDestination extends Exception
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * @param  string  $backupName
     *
     * @return static
     */
    public static function diskNotSet(string $backupName): self
    {
        return new static(
            __("There is no disk set for the backup named `:name`", ['name' => $backupName])
        );
    }
}
