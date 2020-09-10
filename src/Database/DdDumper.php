<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Database;

/**
 * Class     DdDumper
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DdDumper
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Database\DbDumperManager */
    protected $manager;

    /**
     * @var string
     */
    protected $path;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * DdDumper constructor.
     *
     * @param  \Arcanedev\LaravelBackup\Database\DbDumperManager  $manager
     */
    public function __construct(DbDumperManager $manager)
    {
        $this->manager = $manager;
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the path.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Set the path.
     *
     * @param  string  $path
     *
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Dump the file.
     *
     * @param  string       $connection
     * @param  string|null  $filename
     *
     * @return string
     */
    public function dump(string $connection, string $filename = null): string
    {
        $filename = $filename ?: $connection;

        $path = $this->path().DIRECTORY_SEPARATOR."dump-{$filename}.sql";

        $dumper = $this->manager->dumper($connection);

        if ($compressor = $dumper->getCompressor())
            $path .= ".{$compressor->usedExtension()}";

        $dumper->dump($path);

        return $path;
    }
}
