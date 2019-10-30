<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions;

use Closure;

/**
 * Interface     TaskInterface
 *
 * @package  Arcanedev\LaravelBackup\Actions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface TaskInterface
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the task.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Passable|mixed  $passable
     * @param  \Closure                                         $next
     *
     * @return mixed
     */
    public function handle($passable, Closure $next);
}
