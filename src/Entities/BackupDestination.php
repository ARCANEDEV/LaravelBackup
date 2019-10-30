<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Entities;

use Arcanedev\LaravelBackup\Exceptions\InvalidBackupDestination;
use DateTimeInterface;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

/**
 * Class     BackupDestination
 *
 * @package  Arcanedev\LaravelBackup\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupDestination
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use Concerns\HasDisk;

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * @var string
     */
    protected $backupName;

    /**
     * @var string
     */
    protected $diskName;

    /**
     * @var \Exception|null
     */
    protected $connectionError;

    /**
     * @var \Arcanedev\LaravelBackup\Entities\BackupCollection|null
     */
    protected $cachedBackups;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * BackupDestination constructor.
     *
     * @param  \Illuminate\Contracts\Filesystem\Filesystem|null  $disk
     * @param  string                                            $backupName
     * @param  string                                            $diskName
     */
    public function __construct(?Filesystem $disk, string $backupName, string $diskName)
    {
        $this->setDisk($disk);
        $this->setDiskName($diskName);
        $this->setBackupName($backupName);
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the disk name.
     *
     * @return string
     */
    public function diskName(): string
    {
        return $this->diskName;
    }

    /**
     * Set the disk's name.
     *
     * @param  string  $diskName
     *
     * @return $this
     */
    protected function setDiskName(string $diskName): self
    {
        $this->diskName = $diskName;

        return $this;
    }

    /**
     * Set the backup name.
     *
     * @return string
     */
    public function backupName(): string
    {
        return $this->backupName;
    }

    /**
     * Set the backup name.
     *
     * @param  string  $backupName
     *
     * @return $this
     */
    protected function setBackupName(string $backupName): self
    {
        $this->backupName = preg_replace('/[^a-zA-Z0-9.]/', '-', $backupName);

        return $this;
    }

    /**
     * Get the backups collection.
     *
     * @return \Arcanedev\LaravelBackup\Entities\BackupCollection
     */
    public function backups(): BackupCollection
    {
        if ( ! $this->hasCachedBackups()) {
            $this->setBackups(
                $this->fetchBackups()
            );
        }

        return $this->cachedBackups;
    }

    /**
     * Set the backups collection.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\BackupCollection|null  $backups
     *
     * @return $this
     */
    protected function setBackups(?BackupCollection $backups): self
    {
        $this->cachedBackups = $backups;

        return $this;
    }

    /**
     * Get the connection's error.
     *
     * @return \Exception|null
     */
    public function connectionError(): ?Exception
    {
        return $this->connectionError;
    }

    /**
     * Get the disk options.
     *
     * @return array
     */
    public function getDiskOptions(): array
    {
        return config("filesystems.disks.{$this->diskName()}.backup_options") ?? [];
    }

    /**
     * Get the used storage.
     *
     * @return float
     */
    public function usedStorage(): float
    {
        return $this->backups()->size();
    }

    /**
     * Get the newest backup.
     *
     * @return \Arcanedev\LaravelBackup\Entities\Backup|null
     */
    public function newestBackup(): ?Backup
    {
        return $this->backups()->newest();
    }

    /**
     * Get the oldest backup.
     *
     * @return \Arcanedev\LaravelBackup\Entities\Backup|null
     */
    public function oldestBackup(): ?Backup
    {
        return $this->backups()->oldest();
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make a backup destination from disk name.
     *
     * @param  string  $diskName
     * @param  string  $backupName
     *
     * @return static
     */
    public static function makeFromDiskName(string $diskName, string $backupName): self
    {
        try {
            return new static(
                Storage::disk($diskName),
                $backupName,
                $diskName
            );
        }
        catch (Exception $exception) {
            return tap(new static(null, $backupName, $diskName), function (self $buDestination) use ($exception) {
                $buDestination->connectionError = $exception;
            });
        }
    }

    /**
     * Write the file into the disk storage.
     *
     * @param  string  $file
     *
     * @throws \Arcanedev\LaravelBackup\Exceptions\InvalidBackupDestination
     */
    public function write(string $file): void
    {
        if ( ! $this->hasDisk()) {
            throw InvalidBackupDestination::diskNotSet($this->backupName);
        }

        $path   = $this->backupName()."/".basename($file);
        $handle = fopen($file, 'r+');

        $this->disk()->writeStream($path, $handle, $this->getDiskOptions());

        if (is_resource($handle)) {
            fclose($handle);
        }
    }

    /**
     * Reset the cached backups.
     *
     * @return $this
     */
    public function fresh(): self
    {
        $this->cachedBackups = null;

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if has cached backups.
     *
     * @return bool
     */
    public function hasCachedBackups()
    {
        return ! is_null($this->cachedBackups);
    }

    /**
     * Check if the backup destination is reachable.
     *
     * @return bool
     */
    public function isReachable(): bool
    {
        if ( ! $this->hasDisk()) {
            return false;
        }

        try {
            $this->getFiles();

            return true;
        }
        catch (Exception $exception) {
            $this->connectionError = $exception;

            return false;
        }
    }

    /**
     * Check if the new backup is older than the given date.
     *
     * @param  \DateTimeInterface|mixed  $date
     *
     * @return bool
     */
    public function newestBackupIsOlderThan(DateTimeInterface $date): bool
    {
        return is_null($newestBackup = $this->newestBackup())
            ?: $newestBackup->date()->gt($date);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the backups from the disk.
     *
     * @return \Arcanedev\LaravelBackup\Entities\BackupCollection
     */
    private function fetchBackups(): BackupCollection
    {
        return BackupCollection::makeFromFiles(
            $this->disk(),
            $this->hasDisk() ? $this->getFiles() : []
        );
    }

    /**
     * Get the files from the disk.
     *
     * @return array
     */
    private function getFiles(): array
    {
        return $this->disk()->allFiles($this->backupName());
    }
}
