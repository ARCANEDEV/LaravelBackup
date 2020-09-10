<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Events;

use Arcanedev\LaravelBackup\Helpers\Zip;

/**
 * Class     BackupZipWasCreated
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupZipWasCreated
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The zip archive instance.
     *
     * @var \Arcanedev\LaravelBackup\Helpers\Zip
     */
    public $zip;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * BackupZipWasCreated constructor.
     *
     * @param  \Arcanedev\LaravelBackup\Helpers\Zip  $zip
     */
    public function __construct(Zip $zip)
    {
        $this->zip = $zip;
    }
}
