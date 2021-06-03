<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Entities;

use Arcanedev\LaravelBackup\Helpers\{FileChecker, Format};
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;

/**
 * Class     BackupCollection
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupCollection extends Collection
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var float|null */
    protected $cachedSize = null;

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the backups' size.
     *
     * @return float
     */
    public function size(): float
    {
        if ($this->cachedSize !== null) {
            return $this->cachedSize;
        }

        return $this->cachedSize = $this->sum(function (Backup $backup) {
            return $backup->size();
        });
    }

    /**
     * Get the backups' size as a human readable text.
     *
     * @return string
     */
    public function humanReadableSize(): string
    {
        return Format::humanReadableSize($this->size());
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make the backup collection from files in disk's storage.
     *
     * @param  \Illuminate\Contracts\Filesystem\Filesystem|null  $disk
     * @param  array                                             $files
     *
     * @return static
     */
    public static function makeFromFiles(?Filesystem $disk, array $files): self
    {
        return static::make($files)
            ->filter(function (string $path) use ($disk) {
                return FileChecker::isZipFile($disk, $path);
            })
            ->transform(function ($path) use ($disk) {
                return new Backup($disk, $path);
            })
            ->sortByDesc(function (Backup $backup) {
                return $backup->date()->timestamp;
            })
            ->values();
    }

    /**
     * Get the newest backup.
     *
     * @return \Arcanedev\LaravelBackup\Entities\Backup|null
     */
    public function newest(): ?Backup
    {
        return $this->first();
    }

    /**
     * Get the oldest backup.
     *
     * @return \Arcanedev\LaravelBackup\Entities\Backup|null
     */
    public function oldest(): ?Backup
    {
        return $this->filter(function (Backup $backup) {
            return $backup->exists();
        })->last();
    }

    /**
     * Delete backups older than the given date.
     *
     * @param  \Carbon\Carbon  $date
     */
    public function deleteBackupsOlderThan(Carbon $date): void
    {
        $this->each(function (Backup $backup) use ($date) {
            if ($backup->exists() && $backup->date()->lt($date))
                $backup->delete();
        });
    }

    /**
     * Delete all except one.
     */
    public function deleteAllExceptOne(): void
    {
        $this->shift();

        $this->each(function (Backup $backup) {
            $backup->delete();
        });
    }
}
