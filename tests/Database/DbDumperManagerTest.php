<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Database;

use Arcanedev\LaravelBackup\Database\DbDumperManager;
use Arcanedev\LaravelBackup\Tests\TestCase;

/**
 * Class     DbDumperManagerTest
 *
 * @package  Arcanedev\LaravelBackup\Tests\Database
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DbDumperManagerTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Database\DbDumperManager */
    protected $manager;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareDatabases();

        $this->manager = $this->app->get(DbDumperManager::class);
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated()
    {
        static::assertInstanceOf(DbDumperManager::class, $this->manager);
    }

    /** @test */
    public function it_must_throw_exception_on_unsupported_connection()
    {
        $this->expectException(\Arcanedev\LaravelBackup\Exceptions\CannotCreateDbDumper::class);
        $this->expectExceptionMessage(
            'Cannot create a dumper for db driver `unsupported`. Use `mariadb`, `mysql`, `pgsql`, `mongodb`, `sqlite`.'
        );

        $this->manager->dumper('unsupported');
    }

    /**
     * @test
     *
     * @dataProvider getDbDumpersDataProvider
     *
     * @param  string  $driver
     * @param  string  $class
     */
    public function it_can_get_db_dumper_based_on_given_driver(string $driver, string $class)
    {
        $dumper = $this->manager->dumper($driver);

        $classes = [
            \Arcanedev\LaravelBackup\Database\Dumpers\AbstractDumper::class,
            $class,
        ];

        foreach ($classes as $expected) {
            static::assertInstanceOf($expected, $dumper);
        }
    }

    /* -----------------------------------------------------------------
     |  Data Providers
     | -----------------------------------------------------------------
     */

    /**
     * Get db dumpers drivers.
     *
     * @return array
     */
    public function getDbDumpersDataProvider(): array
    {
        return [
            [
                'sqlite',
                \Arcanedev\LaravelBackup\Database\Dumpers\SqliteDumper::class,
            ],
            [
                'mysql',
                \Arcanedev\LaravelBackup\Database\Dumpers\MySqlDumper::class,
            ],
            [
                'mariadb',
                \Arcanedev\LaravelBackup\Database\Dumpers\MySqlDumper::class,
            ],
            [
                'pgsql',
                \Arcanedev\LaravelBackup\Database\Dumpers\PostgreSqlDumper::class,
            ],
            [
                'mongodb',
                \Arcanedev\LaravelBackup\Database\Dumpers\MongoDbDumper::class,
            ]
        ];
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    private function prepareDatabases()
    {
        /** @var  \Illuminate\Contracts\Config\Repository  $config */
        $config = $this->app['config'];

        $config->set('database.connections.mariadb', [
            'driver'         => 'mariadb',
            'url'            => null,
            'host'           => env('DB_HOST', '127.0.0.1'),
            'port'           => env('DB_PORT', 27017),
            'database'       => env('DB_DATABASE'),
            'username'       => env('DB_USERNAME'),
            'password'       => env('DB_PASSWORD'),
            'unix_socket'    => '',
            'charset'        => 'utf8mb4',
            'collation'      => 'utf8mb4_unicode_ci',
            'prefix'         => '',
            'prefix_indexes' => true,
            'strict'         => true,
            'engine'         => null,
            'options'        => [],
        ]);

        $config->set('database.connections.mongodb', [
            'driver'   => 'mongodb',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', 27017),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'options'  => [
                'database' => 'admin',
            ]
        ]);
    }
}
