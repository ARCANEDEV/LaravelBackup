<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Database;

use Arcanedev\LaravelBackup\Database\Contracts\Compressor;

/**
 * Class     Command
 *
 * @package  Arcanedev\LaravelBackup\Database
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
    public function addIf(bool $condition, string $command)
    {
        return $condition ? $this->add($command) : $this;
    }

    /**
     * @param  bool    $condition
     * @param  string  $command
     *
     * @return $this|\Arcanedev\LaravelBackup\Database\Command
     */
    public function addUnless(bool $condition, string $command)
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
    public function add(string $command)
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
    public function addMany(array $commands)
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
     * @param  array  $commands
     *
     * @return static
     */
    public static function make(array $commands = [])
    {
        return new static($commands);
    }

    /**
     * @param  string  $glue
     *
     * @return string
     */
    public function toString(string $glue = ' ')
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
        $command  = $this->toString();
        $dumpFile = '"'.addcslashes($dumpFile, '\\"').'"';

        // TODO: Add windows support
        return $compressor
            ? "(((({$command}; echo \$? >&3) | {$compressor->useCommand()} > {$dumpFile}) 3>&1) | (read x; exit \$x))"
            : "{$command} > {$dumpFile}";
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
        if (static::isWindowsOS()) {
            $command = str_replace('\'', '"', $command);
        }

        return $command;
    }

    /**
     * Check if running on Windows OS.
     *
     * @return bool
     */
    private static function isWindowsOS(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}
