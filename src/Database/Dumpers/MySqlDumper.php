<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Database\Dumpers;

use Arcanedev\LaravelBackup\Database\Command;
use Arcanedev\LaravelBackup\Database\Dumpers\Concerns\HasDbConnection;
use Arcanedev\LaravelBackup\Exceptions\CannotStartDatabaseDump;

/**
 * Class     MySqlDumper
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MySqlDumper extends AbstractDumper
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use HasDbConnection {
        addExtraOption as addExtraOptionParent;
    }

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var bool */
    protected $skipComments = true;

    /** @var bool */
    protected $useExtendedInserts = true;

    /** @var bool */
    protected $useSingleTransaction = false;

    /** @var bool */
    protected $skipLockTables = false;

    /** @var bool */
    protected $useQuick = false;

    /** @var string */
    protected $defaultCharacterSet = '';

    /** @var bool */
    protected $dbNameWasSetAsExtraOption = false;

    /** @var bool */
    protected $allDatabasesWasSetAsExtraOption = false;

    /** @var string */
    protected $setGtidPurged = 'AUTO';

    /** @var bool */
    protected $createTables = true;

    /* -----------------------------------------------------------------
     |  Setters & Getters
     | -----------------------------------------------------------------
     */

    /**
     * Get the default port.
     *
     * @return string
     */
    protected function getDefaultPort(): string
    {
        return '3306';
    }

    /**
     * @return $this
     */
    public function skipComments()
    {
        $this->skipComments = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function dontSkipComments()
    {
        $this->skipComments = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function useExtendedInserts()
    {
        $this->useExtendedInserts = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function dontUseExtendedInserts()
    {
        $this->useExtendedInserts = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function useSingleTransaction()
    {
        $this->useSingleTransaction = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function dontUseSingleTransaction()
    {
        $this->useSingleTransaction = false;

        return $this;
    }

    /**
     * Skip the lock tables.
     *
     * @return $this
     */
    public function skipLockTables()
    {
        $this->skipLockTables = true;

        return $this;
    }

    /**
     * Don't skip the lock tables.
     *
     * @return $this
     */
    public function dontSkipLockTables()
    {
        $this->skipLockTables = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function useQuick()
    {
        $this->useQuick = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function dontUseQuick()
    {
        $this->useQuick = false;

        return $this;
    }

    /**
     * Set the default character set.
     *
     * @param  string  $characterSet
     *
     * @return $this
     */
    public function setDefaultCharacterSet(string $characterSet)
    {
        $this->defaultCharacterSet = $characterSet;

        return $this;
    }

    /**
     * @param  string  $setGtidPurged
     *
     * @return $this
     */
    public function setGtidPurged(string $setGtidPurged)
    {
        $this->setGtidPurged = $setGtidPurged;

        return $this;
    }

    /**
     * Do not create tables.
     *
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
        $this->checkCredentials();

        $tempFileHandle = tmpfile();
        fwrite($tempFileHandle, $this->getDbCredentials());
        $temporaryCredentialsFile = stream_get_meta_data($tempFileHandle)['uri'];

        $process = $this->runCommand(
            $this->getDumpCommand($dumpFile, $temporaryCredentialsFile)
        );

        static::checkIfDumpWasSuccessFul($process, $dumpFile);
    }

    /**
     * Get the command that should be performed to dump the database.
     *
     * @param  string  $dumpFile
     * @param  string  $temporaryCredentialsFile
     *
     * @return string
     */
    public function getDumpCommand(string $dumpFile, string $temporaryCredentialsFile): string
    {
        return Command::make()
            ->add("'{$this->dumpBinaryPath}mysqldump'")
            ->add("--defaults-extra-file=\"{$temporaryCredentialsFile}\"")
            ->addUnless($this->createTables, '--no-create-info')
            ->addIf($this->skipComments, '--skip-comments')
            ->add($this->useExtendedInserts ? '--extended-insert' : '--skip-extended-insert')
            ->addIf($this->useSingleTransaction, '--single-transaction')
            ->addIf($this->skipLockTables, '--skip-lock-tables')
            ->addIf($this->useQuick, '--quick')
            ->addUnless(is_null($this->getSocket()), "--socket={$this->getSocket()}")
            ->addMany(
                array_map(function ($tableName) {
                    return "--ignore-table={$this->getDbName()}.{$tableName}";
                }, $this->excludeTables)
            )
            ->addUnless(empty($this->defaultCharacterSet), "--default-character-set={$this->defaultCharacterSet}")
            ->addMany($this->extraOptions)
            ->addIf($this->setGtidPurged !== 'AUTO', "--set-gtid-purged={$this->setGtidPurged}")
            ->addUnless($this->dbNameWasSetAsExtraOption, $this->getDbName() ?: '')
            ->addUnless(empty($this->includeTables), '--tables '.implode(' ', $this->includeTables))
            ->echoToFile($dumpFile, $this->getCompressor());
    }

    /**
     * Add extra option.
     *
     * @param  string  $extraOption
     *
     * @return \Arcanedev\LaravelBackup\Database\Dumpers\MySqlDumper|mixed
     */
    public function addExtraOption(string $extraOption)
    {
        if (strpos($extraOption, '--all-databases') !== false) {
            $this->dbNameWasSetAsExtraOption = true;
            $this->allDatabasesWasSetAsExtraOption = true;
        }

        if (preg_match('/^--databases (\S+)/', $extraOption, $matches) === 1) {
            $this->setDbName($matches[1]);
            $this->dbNameWasSetAsExtraOption = true;
        }

        return $this->addExtraOptionParent($extraOption);
    }

    /**
     * Get the db credentials.
     *
     * @return string
     */
    public function getDbCredentials(): string
    {
        return implode(PHP_EOL, [
            '[client]',
            "user = '{$this->getUsername()}'",
            "password = '{$this->getPassword()}'",
            "host = '{$this->getHost()}'",
            "port = '{$this->getPort()}'",
        ]);
    }

    /**
     * Check the credentials.
     *
     * @throws \Arcanedev\LaravelBackup\Exceptions\CannotStartDatabaseDump
     */
    protected function checkCredentials()
    {
        foreach (['username', 'host'] as $requiredProperty) {
            if (strlen($this->$requiredProperty ?: '') === 0) {
                throw CannotStartDatabaseDump::emptyParameter($requiredProperty);
            }
        }

        if (strlen($this->getDbName() ?: '') === 0 && ! $this->allDatabasesWasSetAsExtraOption) {
            throw CannotStartDatabaseDump::emptyParameter('dbName');
        }
    }
}
