<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Database\Dumpers;

use Arcanedev\LaravelBackup\Database\Command;

/**
 * Class     SqliteDumper
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SqliteDumper extends AbstractDumper
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Dump the contents of the database to a given file.
     *
     * @param  string  $dumpFile
     */
    public function dump(string $dumpFile): void
    {
        $process = $this->runCommand(
            $this->getDumpCommand($dumpFile)
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
            "echo 'BEGIN IMMEDIATE;\n.dump' | '{$this->dumpBinaryPath}sqlite3' --bail '{$this->getDbName()}'"
        ])->echoToFile($dumpFile, $this->getCompressor());
    }
}
