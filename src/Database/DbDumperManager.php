<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Database;

use Arcanedev\LaravelBackup\Database\Dumpers\{
    AbstractDumper, MongoDbDumper, MySqlDumper, PostgreSqlDumper, SqliteDumper
};
use Arcanedev\LaravelBackup\Exceptions\CannotCreateDbDumper;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\ConfigurationUrlParser;
use Illuminate\Support\{Arr, Collection, Str};

/**
 * Class     DbDumperManager
 *
 * @package  Arcanedev\LaravelBackup\Database
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DbDumperManager
{
    /* -----------------------------------------------------------------
     |  Constants
     | -----------------------------------------------------------------
     */

    const SUPPORTED_DRIVERS = [
        'mariadb',
        'mysql',
        'pgsql',
        'mongodb',
        'sqlite',
    ];

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Illuminate\Contracts\Foundation\Application */
    protected $app;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * DbDumperManager constructor.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Create a dumper.
     *
     * @param  string  $connection
     *
     * @return \Arcanedev\LaravelBackup\Database\Dumpers\AbstractDumper|mixed
     *
     * @throws \Arcanedev\LaravelBackup\Exceptions\CannotCreateDbDumper
     */
    public function dumper(string $connection): AbstractDumper
    {
        $config = $this->parseConfig($connection);
        $driver = $config['driver'] ?: $connection;

        switch ($driver) {
            case 'mariadb':
            case 'mysql':
                return $this->createMysqlDumper($config);

            case 'pgsql':
                return $this->createPgsqlDumper($config);

            case 'mongodb':
                return $this->createMongodbDumper($config);

            case 'sqlite':
                return $this->createSqliteDumper($config);

            default:
                throw CannotCreateDbDumper::unsupportedDriver($driver, static::SUPPORTED_DRIVERS);
        }
    }

    /* -----------------------------------------------------------------
     |  Drivers
     | -----------------------------------------------------------------
     */

    /**
     * Create sqlite Database dumper.
     *
     * @param  array  $config
     *
     * @return \Arcanedev\LaravelBackup\Database\Dumpers\SqliteDumper
     */
    protected function createSqliteDumper(array $config = []): SqliteDumper
    {
        $dumper = $this->createDumper(SqliteDumper::class, $config);

        return $dumper;
    }

    /**
     * Create Mysql Database dumper.
     *
     * @param  array  $config
     *
     * @return \Arcanedev\LaravelBackup\Database\Dumpers\MySqlDumper
     */
    protected function createMysqlDumper(array $config = []): MySqlDumper
    {
        /** @var  \Arcanedev\LaravelBackup\Database\Dumpers\MySqlDumper  $dumper */
        $dumper = $this->createDumperWithConnection(MySqlDumper::class, $config);

        return $dumper->setDefaultCharacterSet($config['charset'] ?? '');
    }

    /**
     * Create PostgreSql Database dumper.
     *
     * @param  array  $config
     *
     * @return \Arcanedev\LaravelBackup\Database\Dumpers\PostgreSqlDumper
     */
    protected function createPgsqlDumper(array $config = []): PostgreSqlDumper
    {
        return $this->createDumperWithConnection(PostgreSqlDumper::class, $config);
    }

    /**
     * Create MongoDB Database dumper.
     *
     * @param  array  $config
     *
     * @return \Arcanedev\LaravelBackup\Database\Dumpers\MongoDbDumper
     */
    protected function createMongodbDumper(array $config): MongoDbDumper
    {
        /** @var  \Arcanedev\LaravelBackup\Database\Dumpers\MongoDbDumper  $dumper */
        $dumper = $this->createDumperWithConnection(MongoDbDumper::class, $config);

        return $dumper->setAuthenticationDatabase(
            config('database.connections.mongodb.dump.mongodb_user_auth') ?: ''
        );
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Create dumper with connection.
     *
     * @param  string  $class
     * @param  array   $config
     *
     * @return \Arcanedev\LaravelBackup\Database\Dumpers\AbstractDumper|mixed
     */
    protected function createDumperWithConnection(string $class, array $config): AbstractDumper
    {
        return tap($this->createDumper($class, $config), function ($dumper) {
            $dumper->setHost(Arr::first(Arr::wrap($config['host'] ?? '')))
                   ->setUserName($config['username'] ?? '')
                   ->setPassword($config['password'] ?? '');

            if (isset($config['port'])) {
                $dumper->setPort($config['port']);
            }
        });
    }

    /**
     * Create a db dumper.
     *
     * @param  string  $class
     * @param  array   $config
     *
     * @return \Arcanedev\LaravelBackup\Database\Dumpers\AbstractDumper|mixed
     */
    protected function createDumper(string $class, array $config): AbstractDumper
    {
        return tap($this->app->make($class), function (AbstractDumper $dumper) use ($config) {
            $dumper->setDbName($config['database']);

            if (isset($config['dump'])) {
                static::processExtraDumpParameters($dumper, $config['dump']);
            }
        });
    }

    /**
     * Parse config.
     *
     * @param  string  $connection
     *
     * @return array
     *
     * @throws \Arcanedev\LaravelBackup\Exceptions\CannotCreateDbDumper
     */
    protected function parseConfig(string $connection): array
    {
        try {
            $dbConfig = (new ConfigurationUrlParser)
                ->parseConfiguration(config("database.connections.{$connection}"));
        }
        catch (Exception $e) {
            throw CannotCreateDbDumper::unsupportedDriver($connection, static::SUPPORTED_DRIVERS);
        }

        if (isset($dbConfig['read'])) {
            $dbConfig = Arr::except(
                array_merge($dbConfig, $dbConfig['read']), ['read', 'write']
            );
        }

        return $dbConfig;
    }

    protected static function processExtraDumpParameters(AbstractDumper &$dumper, array $config)
    {
        Collection::make($config)->each(function ($value, $name) use ($dumper) {
            $methodName  = lcfirst(Str::studly(is_numeric($name) ? $value : $name));
            $methodName  = static::determineValidMethodName($dumper, $methodName);
            $methodValue = is_numeric($name) ? null : $value;

            if (method_exists($dumper, $methodName)) {
                if ( ! $methodValue)
                    $dumper->$methodName();
                else
                    $dumper->$methodName($methodValue);
            }
        });
    }

    protected static function determineValidMethodName(AbstractDumper &$dumper, string $methodName): string
    {
        return Collection::make([$methodName, 'set'.ucfirst($methodName)])
            ->first(function (string $methodName) use ($dumper) {
                return method_exists($dumper, $methodName);
            }, '');
    }
}
