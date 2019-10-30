<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Events;

use Arcanedev\LaravelBackup\Actions\Cleanup\CleanPassable;
use Exception;

/**
 * Class     CleanActionHasFailed
 *
 * @package  Arcanedev\LaravelBackup\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CleanActionHasFailed
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * @var \Arcanedev\LaravelBackup\Actions\Cleanup\CleanPassable
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
     * @param  \Exception                                            $exception
     * @param  \Arcanedev\LaravelBackup\Actions\Cleanup\CleanPassable  $passable
     */
    public function __construct(CleanPassable $passable, Exception $exception)
    {
        $this->passable  = $passable;
        $this->exception = $exception;
    }
}
