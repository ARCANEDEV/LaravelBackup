<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Database\Dumpers;

use Arcanedev\LaravelBackup\Database\Command;
use Arcanedev\LaravelBackup\Database\Dumpers\Concerns\HasDbConnection;
use Arcanedev\LaravelBackup\Exceptions\CannotStartDatabaseDump;

/**
 * Class     PostgreSqlDumper
 *
 * @package  Arcanedev\LaravelBackup\Database\Dumpers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PostgreSqlDumper extends AbstractDumper
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use HasDbConnection;

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var bool */
    protected $useInserts = false;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    public function __construct()
    {
        $this->setPort(5432);
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * @return $this
     */
    public function useInserts()
    {
        $this->useInserts = true;

        return $this;
    }

    /**
     * Get the credentials.
     *
     * @return string
     */
    public function getCredentials(): string
    {
        return implode(':', [
            $this->getHost(),
            $this->getPort(),
            $this->getDbName(),
            $this->getUsername(),
            $this->getPassword(),
        ]);
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Dump the contents of the database to the given file.
     *
     * @param  string  $dumpFile
     */
    public function dump(string $dumpFile): void
    {
        $this->checkDbCredentials();

        $tempFileHandle = tmpfile();
        fwrite($tempFileHandle, $this->getCredentials());
        $temporaryCredentialsFile = stream_get_meta_data($tempFileHandle)['uri'];

        $process = $this->runCommand(
            $this->getDumpCommand($dumpFile),
            null,
            $this->getEnvironmentVariablesForDumpCommand($temporaryCredentialsFile)
        );

        $this->checkIfDumpWasSuccessFul($process, $dumpFile);
    }

    /**
     * Get the command that should be performed to dump the database.
     *
     * @param  string  $dumpFile
     *
     * @return string
     */
    public function getDumpCommand(string $dumpFile): string
    {
        return Command::make([
            "'{$this->dumpBinaryPath}pg_dump'",
            "-U {$this->getUsername()}",
            '-h '.(empty($this->getSocket()) ? $this->getHost() : $this->getSocket()),
            "-p {$this->getPort()}",
        ])
            ->addIf($this->useInserts, '--inserts')
            ->addMany($this->extraOptions)
            ->addUnless(empty($this->includeTables), '-t '.implode(' -t ', $this->includeTables))
            ->addUnless(empty($this->excludeTables), '-T '.implode(' -T ', $this->excludeTables))
            ->echoToFile($dumpFile, $this->getCompressor());
    }

    /**
     * Check the credentials.
     *
     * @throws  \Arcanedev\LaravelBackup\Exceptions\CannotStartDatabaseDump
     */
    protected function checkDbCredentials()
    {
        foreach (['username', 'dbName', 'host'] as $requiredProperty) {
            if (empty($this->$requiredProperty)) {
                throw CannotStartDatabaseDump::emptyParameter($requiredProperty);
            }
        }
    }

    protected function getEnvironmentVariablesForDumpCommand(string $temporaryCredentialsFile): array
    {
        return [
            'PGPASSFILE' => $temporaryCredentialsFile,
            'PGDATABASE' => $this->getDbName(),
        ];
    }
}
