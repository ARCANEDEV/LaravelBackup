<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Exceptions;

use Exception;

/**
 * Class     InvalidBackupDestination
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class InvalidBackupDestination extends Exception
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * @param  string  $diskName
     *
     * @return static
     */
    public static function connectionError(string $diskName): self
    {
        return new static(
            __("There is a connection error when trying to connect to disk named `:name`", ['name' => $diskName])
        );
    }

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
