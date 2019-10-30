<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions;

use Illuminate\Pipeline\Pipeline;

/**
 * Class     Action
 *
 * @package  Arcanedev\LaravelBackup\Actions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class Action extends Pipeline
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Run the task.
     *
     * @param  array  $options
     *
     * @return mixed
     */
    abstract public function run(array $options);
}
