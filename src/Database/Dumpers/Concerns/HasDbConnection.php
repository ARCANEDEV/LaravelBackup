<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Database\Dumpers\Concerns;

use Arcanedev\LaravelBackup\Exceptions\CannotSetDatabaseParameter;

/**
 * Trait     HasDbConnection
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
trait HasDbConnection
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var string */
    protected $username;

    /** @var string */
    protected $password;

    /** @var string */
    protected $host = 'localhost';

    /** @var string */
    protected $port;

    /** @var string|null */
    protected $socket;

    /** @var  array */
    protected $includeTables = [];

    /** @var  array */
    protected $excludeTables = [];

    /** @var  array */
    protected $extraOptions = [];

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the username.
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Set the username.
     *
     * @param  string  $username
     *
     * @return $this|mixed
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set the password.
     *
     * @param string $password
     *
     * @return $this|mixed
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the password.
     *
     * @return string|null
     */
    protected function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Get the host.
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Set the host.
     *
     * @param string $host
     *
     * @return $this|mixed
     */
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Set the port.
     *
     * @param  string  $port
     *
     * @return $this|mixed
     */
    public function setPort(string $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get the port.
     *
     * @return string|null
     */
    public function getPort(): ?string
    {
        return $this->port ?: $this->getDefaultPort();
    }

    /**
     * Get the default port.
     *
     * @return string|null
     */
    abstract protected function getDefaultPort(): ?string;

    /**
     * Set the socket.
     *
     * @param  int|string  $socket
     *
     * @return $this|mixed
     */
    public function setSocket($socket)
    {
        $this->socket = (string) $socket;

        return $this;
    }

    /**
     * Get the socket.
     *
     * @return string
     */
    public function getSocket(): ?string
    {
        return $this->socket;
    }

    /**
     * Include the table to dump.
     *
     * @param  string|array  $tables
     *
     * @return $this|mixed
     *
     * @throws \Arcanedev\LaravelBackup\Exceptions\CannotSetDatabaseParameter
     */
    public function includeTables($tables)
    {
        if ( ! empty($this->excludeTables)) {
            throw CannotSetDatabaseParameter::conflictingParameters('includeTables', 'excludeTables');
        }

        $this->includeTables = is_array($tables)
            ? $tables
            : explode(', ', $tables);

        return $this;
    }

    /**
     * @param  string|array  $tables
     *
     * @return $this|mixed
     *
     * @throws \Arcanedev\LaravelBackup\Exceptions\CannotSetDatabaseParameter
     */
    public function excludeTables($tables)
    {
        if ( ! empty($this->includeTables)) {
            throw CannotSetDatabaseParameter::conflictingParameters('excludeTables', 'includeTables');
        }

        $this->excludeTables = is_array($tables)
            ? $tables
            : explode(', ', $tables);

        return $this;
    }

    /**
     * @param  string  $extraOption
     *
     * @return $this|mixed
     */
    public function addExtraOption(string $extraOption)
    {
        if ( ! empty($extraOption)) {
            $this->extraOptions[] = $extraOption;
        }

        return $this;
    }
}
