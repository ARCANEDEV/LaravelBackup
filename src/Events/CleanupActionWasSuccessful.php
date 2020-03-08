<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Events;

use Arcanedev\LaravelBackup\Actions\Cleanup\CleanupPassable;

/**
 * Class     CleanupWasSuccessful
 *
 * @package  Arcanedev\LaravelBackup\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CleanupActionWasSuccessful
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Actions\Cleanup\CleanupPassable */
    public $passable;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * CleanupActionWasSuccessful constructor.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Cleanup\CleanupPassable  $passable
     */
    public function __construct(CleanupPassable $passable)
    {
        $this->passable = $passable;
    }
}
