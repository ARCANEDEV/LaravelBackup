<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks;

use Arcanedev\LaravelBackup\Entities\BackupDestination;
use Arcanedev\LaravelBackup\Helpers\Format;

/**
 * Class     MaximumStorageInMegabytes
 *
 * @package  Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MaximumStorageInMegabytes extends AbstractHealthCheck
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var int */
    protected $maximumSizeInMegaBytes;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * MaximumStorageInMegabytes constructor.
     *
     * @param  int  $maximumSizeInMegaBytes
     */
    public function __construct(int $maximumSizeInMegaBytes = 5000)
    {
        $this->maximumSizeInMegaBytes = $maximumSizeInMegaBytes;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check the backup destination.
     *
     * @param \Arcanedev\LaravelBackup\Entities\BackupDestination $backupDestination
     *
     * @return mixed|void
     */
    public function check(BackupDestination $backupDestination)
    {
        $usageInBytes = $backupDestination->usedStorage();

        $this->failIf(
            $this->exceedsAllowance($usageInBytes),
            __('The backups are using too much storage. Current usage is :disk_usage which is higher than the allowed limit of :disk_limit.', [
                'disk_usage' => static::humanReadableSize($usageInBytes),
                'disk_limit' => static::humanReadableSize(static::toBytes($this->maximumSizeInMegaBytes)),
            ])
        );
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if exceed the maximum allowed size.
     *
     * @param  float  $usageInBytes
     *
     * @return bool
     */
    protected function exceedsAllowance(float $usageInBytes): bool
    {
        return $usageInBytes > static::toBytes($this->maximumSizeInMegaBytes);
    }

    /**
     * @param  float  $megaBytes
     *
     * @return float
     */
    protected static function toBytes(float $megaBytes): float
    {
        return $megaBytes * 1024 * 1024;
    }

    /**
     * Format the size into a human readable size.
     *
     * @param  float  $sizeInBytes
     *
     * @return string
     */
    protected static function humanReadableSize(float $sizeInBytes): string
    {
        return Format::humanReadableSize($sizeInBytes);
    }
}
