<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Database\Dumpers;

use Arcanedev\LaravelBackup\Database\Command;
use Arcanedev\LaravelBackup\Database\Dumpers\Concerns\HasDbConnection;
use Arcanedev\LaravelBackup\Exceptions\CannotStartDatabaseDump;

/**
 * Class     MongoDbDumper
 *
 * @package  Arcanedev\LaravelBackup\Database\Dumpers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MongoDbDumper extends AbstractDumper
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

    /** @var null|string */
    protected $collection = null;

    /** @var null|string */
    protected $authenticationDatabase = null;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * MongoDbDumper constructor.
     */
    public function __construct()
    {
        $this->setPort(27017);
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * @param  string  $collection
     *
     * @return $this
     */
    public function setCollection(string $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @param  string  $authenticationDatabase
     *
     * @return $this
     */
    public function setAuthenticationDatabase(string $authenticationDatabase)
    {
        $this->authenticationDatabase = $authenticationDatabase;

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

        $process = $this->runCommand(
            $this->getDumpCommand($dumpFile)
        );

        $this->checkIfDumpWasSuccessFul($process, $dumpFile);
    }

    /**
     * Verifies if the dbname and host options are set.
     *
     * @throws \Arcanedev\LaravelBackup\Exceptions\CannotStartDatabaseDump
     */
    protected function checkDbCredentials()
    {
        foreach (['dbName', 'host'] as $requiredProperty) {
            if (strlen($this->$requiredProperty ?: '') === 0) {
                throw CannotStartDatabaseDump::emptyParameter($requiredProperty);
            }
        }
    }
    /**
     * Generate the dump command for MongoDb.
     *
     * @param string $filename
     *
     * @return string
     */
    public function getDumpCommand(string $filename) : string
    {
        return Command::make([
            "'{$this->dumpBinaryPath}mongodump' --db {$this->getDbName()} --archive",
        ])
            ->addUnless(is_null($this->getUsername()), "--username '{$this->getUsername()}'")
            ->addUnless(is_null($this->getPassword()), "--password '{$this->getPassword()}'")
            ->addUnless(is_null($this->getHost()), "--host {$this->getHost()}")
            ->addUnless(is_null($this->getPort()), "--port {$this->getPort()}")
            ->addUnless(is_null($this->collection), "--collection {$this->collection}")
            ->addUnless(is_null($this->authenticationDatabase), "--authenticationDatabase {$this->authenticationDatabase}")
            ->echoToFile($filename, $this->getCompressor());
    }
}
