<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Events;

use Arcanedev\LaravelBackup\Actions\Backup\BackupPassable;

/**
 * Class     BackupActionWasSuccessful
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupActionWasSuccessful
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable */
    public $passable;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * BackupWasSuccessful constructor.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable  $passable
     */
    public function __construct(BackupPassable $passable)
    {
        $this->passable = $passable;
    }
}
