<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Exceptions;

use Exception;
use ZipArchive;

/**
 * Class     ZipException
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ZipException extends Exception
{
    /* -----------------------------------------------------------------
     |  Constants
     | -----------------------------------------------------------------
     */

    /**
     * Array of well known zip status codes
     *
     * @var array
     */
    const STATUSES = [
        ZipArchive::ER_OK          => 'No error',
        ZipArchive::ER_MULTIDISK   => 'Multi-disk zip archives not supported',
        ZipArchive::ER_RENAME      => 'Renaming temporary file failed',
        ZipArchive::ER_CLOSE       => 'Closing zip archive failed',
        ZipArchive::ER_SEEK        => 'Seek error',
        ZipArchive::ER_READ        => 'Read error',
        ZipArchive::ER_WRITE       => 'Write error',
        ZipArchive::ER_CRC         => 'CRC error',
        ZipArchive::ER_ZIPCLOSED   => 'Containing zip archive was closed',
        ZipArchive::ER_NOENT       => 'No such file',
        ZipArchive::ER_EXISTS      => 'File already exists',
        ZipArchive::ER_OPEN        => 'Can\'t open file',
        ZipArchive::ER_TMPOPEN     => 'Failure to create temporary file',
        ZipArchive::ER_ZLIB        => 'Zlib error',
        ZipArchive::ER_MEMORY      => 'Malloc failure',
        ZipArchive::ER_CHANGED     => 'Entry has been changed',
        ZipArchive::ER_COMPNOTSUPP => 'Compression method not supported',
        ZipArchive::ER_EOF         => 'Premature EOF',
        ZipArchive::ER_INVAL       => 'Invalid argument',
        ZipArchive::ER_NOZIP       => 'Not a zip archive',
        ZipArchive::ER_INTERNAL    => 'Internal error',
        ZipArchive::ER_INCONS      => 'Zip archive inconsistent',
        ZipArchive::ER_REMOVE      => 'Can\'t remove file',
        ZipArchive::ER_DELETED     => 'Entry has been deleted'
    ];

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make an exception from zip archive status.
     *
     * @param  $status
     *
     * @return $this
     */
    public static function makeFromStatus($status): self
    {
        $message = array_key_exists($status, self::STATUSES)
            ? __(self::STATUSES[$status])
            : __('Unknown status :status', ['status' => $status]);

        return new static($message, $status);
    }
}
