<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Entities\Concerns;

use Illuminate\Contracts\Filesystem\Filesystem;
use League\Flysystem\FilesystemInterface;

/**
 * Trait     HasDisk
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
trait HasDisk
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem|null
     */
    protected $disk;

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the disk driver.
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem|null
     */
    public function disk(): ?Filesystem
    {
        return $this->disk;
    }

    /**
     * Set the disk driver.
     *
     * @param  \Illuminate\Contracts\Filesystem\Filesystem|null  $disk
     *
     * @return $this
     */
    protected function setDisk(?Filesystem $disk): self
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * Check if has disk instance.
     *
     * @return bool
     */
    public function hasDisk(): bool
    {
        return ! is_null($this->disk());
    }

    /**
     * Get the filesystem's type.
     *
     * @return string
     */
    public function filesystemType(): string
    {
        $type = 'unknown';

        if ($this->hasDisk()) {
            $adapter = explode('\\', get_class($this->getDiskDriver()->getAdapter()));

            $type = strtolower(end($adapter));
        }

        return $type;
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the disk driver.
     *
     * @return \League\Flysystem\Filesystem|\League\Flysystem\FilesystemInterface|null
     */
    protected function getDiskDriver(): ?FilesystemInterface
    {
        return $this->hasDisk()
            ? $this->disk()->getDriver()
            : null;
    }
}
