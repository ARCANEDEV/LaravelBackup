<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Entities;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Class     Backup
 *
 * @package  Arcanedev\LaravelBackup\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Backup
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use Concerns\HasDisk;

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /** @var string */
    protected $path;

    /** @var bool */
    protected $exists;

    /** @var Carbon */
    protected $date;

    /** @var float */
    protected $size;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Backup constructor.
     *
     * @param  \Illuminate\Contracts\Filesystem\Filesystem  $disk
     * @param  string                                       $path
     */
    public function __construct(Filesystem $disk, string $path)
    {
        $this->setDisk($disk);
        $this->setPath($path);
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the backup's path.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Set the backup's path.
     *
     * @param  string  $path
     *
     * @return $this
     */
    protected function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Check if the file exists.
     *
     * @return bool
     */
    public function exists(): bool
    {
        if (is_null($this->exists)) {
            $this->exists = $this->disk()->exists($this->path());
        }

        return $this->exists;
    }

    /**
     * Get the file's last modified date.
     *
     * @return \Illuminate\Support\Carbon
     */
    public function date(): Carbon
    {
        if ($this->date === null) {
            $this->date = Carbon::createFromTimestamp($this->disk()->lastModified($this->path()));
        }

        return $this->date;
    }

    /**
     * Get the backup's size in bytes.
     *
     * @return float
     */
    public function size(): float
    {
        if ( ! is_null($this->size)) {
            return $this->size;
        }

        if ( ! $this->exists()) {
            return 0;
        }

        return $this->size = $this->disk()->size($this->path());
    }

    /**
     * Get the file's steam.
     *
     * @return resource|null
     */
    public function stream()
    {
        return $this->disk()->readStream($this->path());
    }

    /**
     * Delete the backup.
     *
     * @return bool
     */
    public function delete(): bool
    {
        $this->exists = null;

        return $this->disk()->delete($this->path());
    }
}
