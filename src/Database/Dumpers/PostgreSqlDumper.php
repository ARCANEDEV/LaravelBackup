<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Database\Dumpers;

use Arcanedev\LaravelBackup\Database\Command;
use Arcanedev\LaravelBackup\Database\Dumpers\Concerns\HasDbConnection;
use Arcanedev\LaravelBackup\Exceptions\CannotStartDatabaseDump;

/**
 * Class     PostgreSqlDumper
 *
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

    /** @var bool */
    protected $createTables = true;

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the default port.
     *
     * @return string
     */
    protected function getDefaultPort(): string
    {
        return '5432';
    }

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

    /**
     * @return $this
     */
    public function doNotCreateTables()
    {
        $this->createTables = false;

        return $this;
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

        static::checkIfDumpWasSuccessFul($process, $dumpFile);
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
            ->addUnless($this->createTables, '--data-only')
            ->addMany($this->extraOptions)
            ->addUnless(empty($this->includeTables), '-t '.implode(' -t ', $this->includeTables))
            ->addUnless(empty($this->excludeTables), '-T '.implode(' -T ', $this->excludeTables))
            ->echoToFile($dumpFile, $this->getCompressor());
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

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

    /**
     * @param  string  $temporaryCredentialsFile
     *
     * @return array
     */
    protected function getEnvironmentVariablesForDumpCommand(string $temporaryCredentialsFile): array
    {
        return [
            'PGPASSFILE' => $temporaryCredentialsFile,
            'PGDATABASE' => $this->getDbName(),
        ];
    }
}
