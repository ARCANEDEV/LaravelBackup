<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Console;

use Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks\MaximumAgeInDays;
use Arcanedev\LaravelBackup\Events\{HealthyBackupWasFound, UnhealthyBackupWasFound};
use Arcanedev\LaravelBackup\Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

/**
 * Class     MonitorBackupCommandTest
 *
 * @package  Arcanedev\LaravelBackup\Tests\Console
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MonitorBackupCommandTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    /* -----------------------------------------------------------------
     |  Tests - Is Reachable
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_succeeds_when_destination_is_reachable()
    {
        $this->artisan('backup:monitor')
             ->assertExitCode(0)
             ->expectsOutput("The backups on disk [local] are considered healthy.");

        Event::assertDispatched(HealthyBackupWasFound::class);
    }

    /** @test */
    public function it_fails_when_backup_destination_is_not_reachable()
    {
        $this->app['config']->set('backup.monitor.destinations.0.disks', ['not-real-disk']);

        $this->artisan('backup:monitor')
             ->assertExitCode(1)
             ->expectsOutput("The backups on disk [not-real-disk] are considered unhealthy!");

        Event::assertDispatched(UnhealthyBackupWasFound::class);
    }

    /* -----------------------------------------------------------------
     |  Tests - Maximum Age In Days
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_succeeds_when_a_fresh_backup_present()
    {
        $this->create1MbFileOnDisk('primary-storage', 'ARCANEDEV/test.zip', Carbon::now()->subSecond());

        $this->app['config']->set('backup.monitor.destinations.0.disks', ['primary-storage']);

        $this->artisan('backup:monitor')
             ->assertExitCode(0)
             ->expectsOutput("The backups on disk [primary-storage] are considered healthy.");

        Event::assertDispatched(HealthyBackupWasFound::class);
    }

    /** @test */
    public function it_fails_when_no_backups_are_present()
    {
        $this->app['config']->set('backup.monitor.destinations.0.disks', ['primary-storage']);

        $this->artisan('backup:monitor')
             ->assertExitCode(1)
             ->expectsOutput("The backups on disk [primary-storage] are considered unhealthy!");

        Event::assertDispatched(UnhealthyBackupWasFound::class);
    }

    /** @test */
    public function it_fails_when_max_days_has_been_exceeded()
    {
        static::create1MbFileOnDisk('primary-storage', 'ARCANEDEV/test.zip', Carbon::now()->subSecond()->subDay());

        $this->app['config']->set('backup.monitor.destinations.0.disks', ['primary-storage']);

        $this->artisan('backup:monitor')
             ->assertExitCode(1)
             ->expectsOutput("The backups on disk [primary-storage] are considered unhealthy!");

        Event::assertDispatched(UnhealthyBackupWasFound::class);
    }

    /** @test */
    public function it_accepts_a_shorthand_value_in_config()
    {
        static::create1MbFileOnDisk('primary-storage', 'ARCANEDEV/test.zip', Carbon::now()->subSecond()->subDay());

        $this->app['config']->set('backup.monitor.destinations.0.disks', ['primary-storage']);
        $this->app['config']->set('backup.monitor.destinations.0.health-checks', [
            MaximumAgeInDays::class => [2],
        ]);

        $this->artisan('backup:monitor')
             ->assertExitCode(0)
             ->expectsOutput("The backups on disk [primary-storage] are considered healthy.");

        Event::assertDispatched(HealthyBackupWasFound::class);
    }
}
