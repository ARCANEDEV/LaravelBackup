<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Monitor;

use Arcanedev\LaravelBackup\Events\MonitorActionHasFailed;
use Exception;
use Illuminate\Pipeline\Pipeline;

/**
 * Class     MonitorAction
 *
 * @package  Arcanedev\LaravelBackup\Actions\Monitor
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MonitorAction extends Pipeline
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
    public function run(array $options)
    {
        return $this->send(new MonitorPassable($options))
                    ->thenReturn();
    }

    /**
     * Handle the given exception.
     *
     * @param  mixed       $passable
     * @param  \Exception  $e
     *
     * @return mixed|void
     *
     * @throws \Exception
     */
    protected function handleException($passable, Exception $e)
    {
        event(new MonitorActionHasFailed($passable, $e));

        parent::handleException($passable, $e);
    }
}
