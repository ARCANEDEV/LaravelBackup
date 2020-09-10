<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Events;

use Arcanedev\LaravelBackup\Actions\Cleanup\CleanupPassable;
use Exception;

/**
 * Class     CleanupActionHasFailed
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CleanupActionHasFailed
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * @var \Arcanedev\LaravelBackup\Actions\Cleanup\CleanupPassable
     */
    public $passable;

    /**
     * @var \Exception
     */
    public $exception;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * CleanActionHasFailed constructor.
     *
     * @param  \Exception                                              $exception
     * @param  \Arcanedev\LaravelBackup\Actions\Cleanup\CleanupPassable  $passable
     */
    public function __construct(CleanupPassable $passable, Exception $exception)
    {
        $this->passable  = $passable;
        $this->exception = $exception;
    }
}
