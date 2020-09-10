<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Database;

use Arcanedev\LaravelBackup\Database\Contracts\Compressor;

/**
 * Class     Command
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Command
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Command's options.
     *
     * @var array
     */
    protected $commands = [];

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Command constructor.
     *
     * @param  array  $commands
     */
    public function __construct(array $commands = [])
    {
        $this->commands = $commands;
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * @param  bool    $condition
     * @param  string  $command
     *
     * @return $this|\Arcanedev\LaravelBackup\Database\Command
     */
    public function addIf(bool $condition, string $command): self
    {
        if ($condition) {
            return $this->add($command);
        }

        return $this;
    }

    /**
     * @param  bool    $condition
     * @param  string  $command
     *
     * @return $this|\Arcanedev\LaravelBackup\Database\Command
     */
    public function addUnless(bool $condition, string $command): self
    {
        return $this->addIf( ! $condition, $command);
    }

    /**
     * Add a command option.
     *
     * @param  string  $command
     *
     * @return $this
     */
    public function add(string $command): self
    {
        $this->commands[] = $command;

        return $this;
    }

    /**
     * Add many commands.
     *
     * @param  string[]|array  $commands
     *
     * @return $this
     */
    public function addMany(array $commands): self
    {
        foreach ($commands as $command) {
            $this->add($command);
        }

        return $this;
    }

    /**
     * Get all the command options.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->commands;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make the command instance.
     *
     * @param  array  $commands
     *
     * @return self
     */
    public static function make(array $commands = []): self
    {
        return new static($commands);
    }

    /**
     * Convert into a string command.
     *
     * @param  string  $glue
     *
     * @return string
     */
    public function toString(string $glue = ' '): string
    {
        return static::prepareCommand(
            implode($glue, $this->all())
        );
    }

    /**
     * @param  string                                                       $dumpFile
     * @param  \Arcanedev\LaravelBackup\Database\Contracts\Compressor|null  $compressor
     *
     * @return string
     */
    public function echoToFile(string $dumpFile, Compressor $compressor = null): string
    {
        $dumpFile = '"'.addcslashes($dumpFile, '\\"').'"';

        $command = $this->toString();

        if (is_null($compressor)) {
            return "{$command} > {$dumpFile}";
        }

        return static::isWindowsOS()
            ? "{$command} | {$compressor->useCommand()} > {$dumpFile}"
            : "(((({$command}; echo \$? >&3) | {$compressor->useCommand()} > {$dumpFile}) 3>&1) | (read x; exit \$x))";
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Prepare the command.
     *
     * @param  string  $command
     *
     * @return string
     */
    protected function prepareCommand(string $command): string
    {
        return static::isWindowsOS()
            ? str_replace("'", '"', $command)
            : $command;
    }

    /**
     * Check if running on Windows OS.
     *
     * @return bool
     */
    protected static function isWindowsOS(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}
