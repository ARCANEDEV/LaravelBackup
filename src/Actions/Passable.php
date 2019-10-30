<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions;

use Illuminate\Support\Arr;

/**
 * Class     Passable
 *
 * @package  Arcanedev\LaravelBackup\Actions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class Passable
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  array */
    protected $config;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Passable constructor.
     *
     * @param  array  $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the config values.
     *
     * @return array
     */
    public function config(): array
    {
        return $this->config;
    }

    /**
     * Get a config value.
     *
     * @param  string      $key
     * @param  mixed|null  $default
     *
     * @return mixed
     */
    public function getConfig(string $key, $default = null)
    {
        return Arr::get($this->config(), $key, $default);
    }
}
