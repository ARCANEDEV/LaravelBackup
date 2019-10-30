<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Backup;

use Arcanedev\LaravelBackup\Actions\Passable;
use Arcanedev\LaravelBackup\Entities\{BackupDestinationCollection, Manifest};
use Arcanedev\LaravelBackup\Helpers\Zip;

/**
 * Class     BackupPassable
 *
 * @package  Arcanedev\LaravelBackup\Actions\Backup
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupPassable extends Passable
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Entities\Manifest|null */
    protected $manifest;

    /** @var  \Arcanedev\LaravelBackup\Helpers\Zip */
    protected $zip;

    /** @var  \Arcanedev\LaravelBackup\Entities\BackupDestinationCollection|null */
    protected $backupDestinations;

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the temporary directory path.
     *
     * @return string
     */
    public function temporaryDirectoryPath(): string
    {
        return $this->getConfig('temporary-directory', storage_path('app/_backup-temp'));
    }

    /**
     * Get the manifest.
     *
     * @return \Arcanedev\LaravelBackup\Entities\Manifest|null
     */
    public function manifest(): ?Manifest
    {
        return $this->manifest;
    }

    /**
     * Set the manifest.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\Manifest  $manifest
     *
     * @return $this
     */
    public function setManifest(Manifest $manifest): self
    {
        $this->manifest = $manifest;

        return $this;
    }

    /**
     * Get the zip instance.
     *
     * @return \Arcanedev\LaravelBackup\Helpers\Zip|null
     */
    public function zip(): ?Zip
    {
        return $this->zip;
    }

    /**
     * Set the zip instance.
     *
     * @param \Arcanedev\LaravelBackup\Helpers\Zip $zip
     *
     * @return $this
     */
    public function setZip(Zip $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get the backup destination collection.
     *
     * @return \Arcanedev\LaravelBackup\Entities\BackupDestinationCollection|null
     */
    public function getBackupDestinations(): ?BackupDestinationCollection
    {
        return $this->backupDestinations;
    }

    /**
     * Set the backup destination collection.
     *
     * @param \Arcanedev\LaravelBackup\Entities\BackupDestinationCollection  $backupDestinations
     *
     * @return $this
     */
    public function setBackupDestinations(BackupDestinationCollection $backupDestinations): self
    {
        $this->backupDestinations = $backupDestinations;

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if only databases are backup.
     *
     * @return bool
     */
    public function isOnlyDatabases(): bool
    {
        return $this->getConfig('options.only-db', true);
    }

    /**
     * Check if only files are backup.
     *
     * @return bool
     */
    public function isOnlyFiles(): bool
    {
        return $this->getConfig('options.only-files', true);
    }

    /**
     * Check if the notifications are disabled.
     *
     * @return bool
     */
    public function isNotificationsDisabled(): bool
    {
        return $this->getConfig('options.disable-notifications', false);
    }
}
