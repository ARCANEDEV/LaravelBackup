<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests;

use Arcanedev\LaravelBackup\BackupServiceProvider;
use Arcanedev\LaravelBackup\Providers\DeferredServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

/**
 * Class     TestCase
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class TestCase extends OrchestraTestCase
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use Concerns\HasDisksManipulation,
        Concerns\HasFilesManipulation;

    use Asserts\AssertZipFile;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        static::setTestNow(2019, 01, 01, 12, 30, 30);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            BackupServiceProvider::class,
            DeferredServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        /** @var  \Illuminate\Contracts\Config\Repository  $config */
        $config = $app['config'];

        $config->set('backup.monitor_backups.0.health_checks', []);
        $config->set('mail.driver', 'log');

        static::setupDatabases($config);
        static::setupStorages($config);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Setup the databases.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     */
    private static function setupDatabases(Repository $config): void
    {
        $config->set('database.connections.sqlite-db-1', [
            'driver'   => 'sqlite',
            'database' => static::getTempDirectory('databases/database-1.sqlite'),
        ]);

        $config->set('database.connections.sqlite-db-2', [
            'driver'   => 'sqlite',
            'database' => static::getTempDirectory('databases/database-2.sqlite'),
        ]);


        $config->set('database.default', 'sqlite-db-1');

        $config->set('backup.backup.source.databases', ['sqlite-db-1', 'sqlite-db-2']);
    }

    /**
     * Setup the storages.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     */
    private static function setupStorages(Repository $config): void
    {
        static::fakeDiskStorages($disks = [
            'primary-storage',
            'secondary-storage',
        ]);

        $config->set('backup.destination.disks', $disks);
    }

    /**
     * Fake the disk storages.
     *
     * @param  string[]  $disks
     */
    protected static function fakeDiskStorages(array $disks): void
    {
        foreach ($disks as $disk) {
            Storage::fake($disk);
        }
    }

    /**
     * Set a Carbon instance (real or mock) to be returned when a "now" instance is created.
     *
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     * @param  int  $hour
     * @param  int  $minute
     * @param  int  $second
     */
    protected static function setTestNow(int $year, int $month, int $day, int $hour, int $minute, int $second)
    {
        Carbon::setTestNow(Carbon::create($year, $month, $day, $hour, $minute, $second));
    }
}
