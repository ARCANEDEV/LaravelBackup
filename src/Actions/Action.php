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
     * Execute the action.
     *
     * @param  array  $options
     *
     * @return \Arcanedev\LaravelBackup\Actions\Passable|mixed
     */
    public function execute(array $options)
    {
        return $this
            ->send($this->makePassable($options))
            ->then(function (Passable $passable) {
                return $this->handleOnSuccess($passable);
            });
    }

    /**
     * Make the passable.
     *
     * @param  array  $options
     *
     * @return \Arcanedev\LaravelBackup\Actions\Passable|mixed
     */
    abstract protected function makePassable(array $options);

    /**
     * Handle the passable on success.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Passable|mixed  $passable
     *
     * @return \Arcanedev\LaravelBackup\Actions\Passable|mixed
     */
    protected function handleOnSuccess($passable)
    {
        return $passable;
    }
}
