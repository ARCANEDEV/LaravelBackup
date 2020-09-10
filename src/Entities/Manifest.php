<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use JsonSerializable;

/**
 * Class     Manifest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Manifest implements Arrayable, JsonSerializable
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The manifest's path.
     *
     * @var string
     */
    protected $path;

    /**
     * The manifest's filename.
     *
     * @var string
     */
    private $filename;

    /**
     * The files list.
     *
     * @var array
     */
    protected $files;

    /**
     * Manifest saved status.
     *
     * @var bool
     */
    protected $isSaved = false;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Manifest constructor.
     *
     * @param  string  $path
     * @param  string  $filename
     */
    public function __construct(string $path, string $filename = 'manifest.json')
    {
        $this->setPath($path);
        $this->setFilename($filename);
        $this->reset();
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

    /**
     * Get the filename.
     *
     * @return string
     */
    public function filename(): string
    {
        return $this->filename;
    }

    /**
     * Set the filename.
     *
     * @param  string  $filename
     *
     * @return $this
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        $this->isSaved    = false;

        return $this;
    }

    /**
     * Get the file path.
     *
     * @return string
     */
    public function filePath(): string
    {
        return $this->path().DIRECTORY_SEPARATOR.$this->filename();
    }

    /**
     * Get the files.
     *
     * @return array
     */
    public function files(): array
    {
        return $this->files;
    }

    /**
     * Set the files.
     *
     * @param  array  $files
     *
     * @return $this
     */
    public function setFiles(array $files): self
    {
        $this->files = $files;

        return $this;
    }

    /**
     * Check if the manifest is saved.
     *
     * @return bool
     */
    public function isSaved(): bool
    {
        return $this->isSaved;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make the manifest.
     *
     * @param  string  $path
     * @param  string  $filename
     *
     * @return static
     */
    public static function make(string $path, string $filename = 'manifest.json'): self
    {
        return new static($path, $filename);
    }

    /**
     * Load the manifest file.
     *
     * @return static
     */
    public function load(): self
    {
        $this->setFiles(
            json_decode(file_get_contents($this->filePath()), true)
        );

        $this->isSaved = true;

        return $this;
    }

    /**
     * Add files.
     *
     * @param  array|string  $files
     *
     * @return $this
     */
    public function addFiles($files): self
    {
        return $this->setFiles(
            array_merge($this->files, Arr::wrap($files))
        );
    }

    /**
     * Save the manifest into a file.
     *
     * @return bool
     */
    public function save(): bool
    {
        $saved = true;

        if ( ! $this->isSaved()) {
            $saved = (bool) file_put_contents(
                $this->filePath(), json_encode($this, JSON_PRETTY_PRINT)
            );
        }

        return $saved;
    }

    /**
     * Reset the files.
     *
     * @return $this
     */
    public function reset(): self
    {
        return $this->setFiles([]);
    }

    /**
     * Check if the manifest is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->files());
    }

    /**
     * Check if the manifest is not empty.
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->files();
    }

    /**
     * Get the instance as an array that should be serialized to JSON.

     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
