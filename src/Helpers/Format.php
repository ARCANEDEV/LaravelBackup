<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Helpers;

use Carbon\CarbonInterface;

/**
 * Class     Format
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Format
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Format the file size into a human readable text.
     *
     * @param  int|float  $sizeInBytes
     *
     * @return string
     */
    public static function humanReadableSize(float $sizeInBytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        if ($sizeInBytes === 0) {
            return '0 '.$units[1];
        }
        for ($i = 0; $sizeInBytes > 1024; $i++) {
            $sizeInBytes /= 1024;
        }

        return round($sizeInBytes, 2).' '.$units[$i];
    }

    /**
     * @param  \Carbon\CarbonInterface  $date
     *
     * @return string
     */
    public static function ageInDays(CarbonInterface $date): string
    {
        return number_format(round($date->diffInMinutes() / (24 * 60), 2), 2).' ('.$date->diffForHumans().')';
    }
}
