<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Exceptions;

use Exception;
use Symfony\Component\Process\Process;

/**
 * Class     DatabaseDumpFailed
 *
 * @package  Arcanedev\LaravelBackup\Exceptions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DatabaseDumpFailed extends Exception
{
    /**
     * @param  \Symfony\Component\Process\Process  $process
     *
     * @return static
     */
    public static function processDidNotEndSuccessfully(Process $process): self
    {
        return new static(
            sprintf("The dump process failed with exit code [%s - %s] : %s",
                $process->getExitCode(),
                $process->getExitCodeText(),
                $process->getErrorOutput()
            )
        );
    }

    /**
     * @return static
     */
    public static function dumpfileWasNotCreated(): self
    {
        return new static('The dumpfile could not be created');
    }

    /**
     * @return static
     */
    public static function dumpfileWasEmpty(): self
    {
        return new static('The created dumpfile is empty');
    }
}
