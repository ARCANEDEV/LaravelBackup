<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Database\Dumpers;

use Arcanedev\LaravelBackup\Database\Contracts\Compressor;
use Arcanedev\LaravelBackup\Exceptions\DatabaseDumpFailed;
use Symfony\Component\Process\Process;

/**
 * Class     AbstractDumper
 *
 * @package  Arcanedev\LaravelBackup\Database\Dumpers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class AbstractDumper
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  string */
    protected $dbName;

    /** @var  float */
    protected $timeout = 0.0;

    /** @var  string */
    protected $dumpBinaryPath = '';

    /** @var  \Arcanedev\LaravelBackup\Database\Contracts\Compressor|null */
    protected $compressor;

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the db name.
     *
     * @return string|null
     */
    public function getDbName(): ?string
    {
        return $this->dbName;
    }


    /**
     * Set the db name.
     *
     * @param  string  $dbName
     *
     * @return $this|mixed
     */
    public function setDbName(string $dbName): self
    {
        $this->dbName = $dbName;

        return $this;
    }

    /**
     * Get the timeout.
     *
     * @return float
     */
    public function getTimeout(): float
    {
        return $this->timeout;
    }

    /**
     * Set the timeout.
     *
     * @param  float  $timeout
     *
     * @return $this|mixed
     */
    public function setTimeout(float $timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Set the dump binary path.
     *
     * @param  string  $dumpBinaryPath
     *
     * @return $this|mixed
     */
    public function setDumpBinaryPath(string $dumpBinaryPath): self
    {
        if ($dumpBinaryPath !== '' && substr($dumpBinaryPath, -1) !== '/') {
            $dumpBinaryPath .= '/';
        }

        $this->dumpBinaryPath = $dumpBinaryPath;

        return $this;
    }

    /**
     * Get the compressor.
     *
     * @return \Arcanedev\LaravelBackup\Database\Contracts\Compressor|null
     */
    public function getCompressor(): ?Compressor
    {
        return $this->compressor;
    }

    /**
     * Use the given compressor (alias).
     * @see setCompressor()
     *
     * @param  \Arcanedev\LaravelBackup\Database\Contracts\Compressor  $compressor
     *
     * @return $this|mixed
     */
    public function useCompressor(Compressor $compressor)
    {
        return $this->setCompressor($compressor);
    }

    /**
     * Set the compressor.
     *
     * @param  \Arcanedev\LaravelBackup\Database\Contracts\Compressor  $compressor
     *
     * @return $this|mixed
     */
    public function setCompressor(Compressor $compressor)
    {
        $this->compressor = $compressor;

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Dump the contents of the database to a given file.
     *
     * @param  string  $dumpFile
     */
    abstract public function dump(string $dumpFile): void;

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * @param  \Symfony\Component\Process\Process  $process
     * @param  string                              $outputFile
     *
     * @throws \Arcanedev\LaravelBackup\Exceptions\DatabaseDumpFailed
     */
    protected static function checkIfDumpWasSuccessFul(Process $process, string $outputFile)
    {
        if ( ! $process->isSuccessful()) {
            throw DatabaseDumpFailed::processDidNotEndSuccessfully($process);
        }

        if ( ! file_exists($outputFile)) {
            throw DatabaseDumpFailed::dumpfileWasNotCreated();
        }

        if (filesize($outputFile) === 0) {
            throw DatabaseDumpFailed::dumpfileWasEmpty();
        }
    }

    /**
     * Rune the command.
     *
     * @param  string       $command
     * @param  string|null  $cwd
     * @param  array|null   $env
     * @param  mixed|null   $input
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function runCommand(string $command, string $cwd = null, array $env = null, $input = null): Process
    {
        return tap(
            Process::fromShellCommandline($command, $cwd, $env, $input, $this->getTimeout()),
            function (Process $process) {
                $process->run();
            }
        );
    }
}
