<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Events;

use Arcanedev\LaravelBackup\Entities\Manifest;

/**
 * Class     BackupManifestWasCreated
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupManifestWasCreated
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Entities\Manifest */
    public $manifest;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * BackupManifestWasCreated constructor.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\Manifest  $manifest
     */
    public function __construct(Manifest $manifest)
    {
        $this->manifest = $manifest;
    }
}
