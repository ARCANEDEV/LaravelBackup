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
            __("The dump process failed with exit code [:exit_code - :exit_code_text] : :error_output", [
                'exit_code'      => $process->getExitCode(),
                'exit_code_text' => $process->getExitCodeText(),
                'error_output'   => $process->getErrorOutput(),
            ])
        );
    }

    /**
     * @return static
     */
    public static function dumpfileWasNotCreated(): self
    {
        return new static(__('The dumpfile could not be created'));
    }

    /**
     * @return static
     */
    public static function dumpfileWasEmpty(): self
    {
        return new static(__('The created dumpfile is empty'));
    }
}
