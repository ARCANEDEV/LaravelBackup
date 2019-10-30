<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Console;

use Arcanedev\LaravelBackup\Events\CleanupWasSuccessful;
use Arcanedev\LaravelBackup\Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

/**
 * Class     CleanupBackupCommandTest
 *
 * @package  Arcanedev\LaravelBackup\Tests\Console
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CleanupBackupCommandTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        static::createTempDirectory();

        Event::fake();

        static::setTestNow(2016, 1, 1, 22, 00, 00);
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_remove_old_backups_until_using_less_than_maximum_storage()
    {
        $this->app['config']->set('backup.cleanup.strategy.delete-backups.oldest-when-size-reach', 2);

        static::create1MbFileOnDisk('primary-storage', 'ARCANEDEV/test-1.zip', Carbon::now()->subDays(1));
        static::create1MbFileOnDisk('primary-storage', 'ARCANEDEV/test-2.zip', Carbon::now()->subDays(2));
        static::create1MbFileOnDisk('primary-storage', 'ARCANEDEV/test-3.zip', Carbon::now()->subDays(3));
        static::create1MbFileOnDisk('primary-storage', 'ARCANEDEV/test-4.zip', Carbon::now()->subDays(4));

        $this->artisan('backup:clean')
             ->expectsOutput('Starting cleanup...')
             ->expectsOutput('Cleanup completed!')
             ->assertExitCode(0);

        Storage::disk('primary-storage')->assertExists('ARCANEDEV/test-1.zip');
        Storage::disk('primary-storage')->assertExists('ARCANEDEV/test-2.zip');
        Storage::disk('primary-storage')->assertMissing('ARCANEDEV/test-3.zip');
        Storage::disk('primary-storage')->assertMissing('ARCANEDEV/test-4.zip');
    }

    /** @test */
    public function it_can_remove_old_backups_from_the_backup_directory()
    {
        /** @var  \Illuminate\Support\Collection  $expectedRemainingBackups */
        /** @var  \Illuminate\Support\Collection  $expectedDeletedBackups */
        [$expectedRemainingBackups, $expectedDeletedBackups] = Collection::times(1000)
            ->flatMap(function (int $numberOfDays) {
                $date = Carbon::now()->subDays($numberOfDays);

                return [
                    static::createFileOnDisk('primary-storage', "ARCANEDEV/test_{$date->format('Ymd')}_first.zip", $date),
                    static::createFileOnDisk('primary-storage', "ARCANEDEV/test_{$date->format('Ymd')}_second.zip", $date->addHours(2)),
                ];
            })
            ->partition(function (string $backupPath) {
                return in_array($backupPath, [
                    'ARCANEDEV/test_20131231_first.zip',
                    'ARCANEDEV/test_20141231_first.zip',
                    'ARCANEDEV/test_20150630_first.zip',
                    'ARCANEDEV/test_20150731_first.zip',
                    'ARCANEDEV/test_20150831_first.zip',
                    'ARCANEDEV/test_20150930_first.zip',
                    'ARCANEDEV/test_20151018_first.zip',
                    'ARCANEDEV/test_20151025_first.zip',
                    'ARCANEDEV/test_20151101_first.zip',
                    'ARCANEDEV/test_20151108_first.zip',
                    'ARCANEDEV/test_20151115_first.zip',
                    'ARCANEDEV/test_20151122_first.zip',
                    'ARCANEDEV/test_20151129_first.zip',
                    'ARCANEDEV/test_20151206_first.zip',
                    'ARCANEDEV/test_20151209_first.zip',
                    'ARCANEDEV/test_20151210_first.zip',
                    'ARCANEDEV/test_20151211_first.zip',
                    'ARCANEDEV/test_20151212_first.zip',
                    'ARCANEDEV/test_20151213_first.zip',
                    'ARCANEDEV/test_20151214_first.zip',
                    'ARCANEDEV/test_20151215_first.zip',
                    'ARCANEDEV/test_20151216_first.zip',
                    'ARCANEDEV/test_20151217_first.zip',
                    'ARCANEDEV/test_20151218_first.zip',
                    'ARCANEDEV/test_20151219_first.zip',
                    'ARCANEDEV/test_20151220_first.zip',
                    'ARCANEDEV/test_20151221_first.zip',
                    'ARCANEDEV/test_20151222_first.zip',
                    'ARCANEDEV/test_20151223_first.zip',
                    'ARCANEDEV/test_20151224_first.zip',
                    'ARCANEDEV/test_20151225_second.zip',
                    'ARCANEDEV/test_20151225_first.zip',
                    'ARCANEDEV/test_20151226_second.zip',
                    'ARCANEDEV/test_20151226_first.zip',
                    'ARCANEDEV/test_20151226_first.zip',
                    'ARCANEDEV/test_20151227_second.zip',
                    'ARCANEDEV/test_20151227_first.zip',
                    'ARCANEDEV/test_20151228_second.zip',
                    'ARCANEDEV/test_20151228_first.zip',
                    'ARCANEDEV/test_20151229_second.zip',
                    'ARCANEDEV/test_20151229_first.zip',
                    'ARCANEDEV/test_20151230_second.zip',
                    'ARCANEDEV/test_20151230_first.zip',
                    'ARCANEDEV/test_20151231_second.zip',
                    'ARCANEDEV/test_20151231_first.zip',
                    'ARCANEDEV/test_20160101_second.zip',
                    'ARCANEDEV/test_20160101_first.zip',
                ]);
            });

        $this->artisan('backup:clean')
             ->expectsOutput('Starting cleanup...')
             ->expectsOutput('Cleanup completed!')
             ->assertExitCode(0);

        Storage::disk('primary-storage')->assertExists($expectedRemainingBackups->toArray());
        Storage::disk('primary-storage')->assertMissing($expectedDeletedBackups->toArray());
    }

    /** @test */
    public function it_will_leave_non_zip_files_alone()
    {
        $paths = Collection::make([
            static::createFileOnDisk('primary-storage', 'ARCANEDEV/test1.txt', Carbon::now()->subDays(1)),
            static::createFileOnDisk('primary-storage', 'ARCANEDEV/test2.txt', Carbon::now()->subDays(2)),
            static::createFileOnDisk('primary-storage', 'ARCANEDEV/test1000.txt', Carbon::now()->subDays(1000)),
            static::createFileOnDisk('primary-storage', 'ARCANEDEV/test2000.txt', Carbon::now()->subDays(2000)),
        ]);

        $this->artisan('backup:clean')
             ->expectsOutput('Starting cleanup...')
             ->expectsOutput('Cleanup completed!')
             ->assertExitCode(0);

        $paths->each(function (string $path) {
            Storage::disk('primary-storage')->assertExists($path);
        });
    }

    /** @test */
    public function it_will_never_delete_the_newest_backup()
    {
        $backupPaths = Collection::make(range(5, 10))->map(function (int $numberOfYears) {
            $date = Carbon::now()->subYears($numberOfYears);

            return static::createFileOnDisk('primary-storage', "ARCANEDEV/test_{$date->format('Ymd')}.zip", $date);
        });

        $this->artisan('backup:clean')
             ->expectsOutput('Starting cleanup...')
             ->expectsOutput('Cleanup completed!')
             ->assertExitCode(0);

        Storage::disk('primary-storage')->assertExists($backupPaths->first());

        $backupPaths->shift();

        $backupPaths->each(function (string $path) {
            Storage::disk('primary-storage')->assertMissing($path);
        });
    }

    /** @test */
    public function it_should_trigger_the_cleanup_successful_event()
    {
        $this->artisan('backup:clean')
             ->expectsOutput('Starting cleanup...')
             ->expectsOutput('Cleanup completed!')
             ->assertExitCode(0);

        Event::assertDispatched(CleanupWasSuccessful::class);
    }

    /** @test */
    public function it_should_omit_the_cleanup_successful_event_when_the_notifications_are_disabled()
    {
        $this->artisan('backup:clean --disable-notifications')
             ->expectsOutput('Starting cleanup...')
             ->expectsOutput('Cleanup completed!')
             ->assertExitCode(0);

        Event::assertNotDispatched(CleanupWasSuccessful::class);
    }

    /** @test */
    public function it_should_display_correct_used_storage_amount_after_cleanup()
    {
        $this->app['config']->set('backup.cleanup.strategy.delete-backups.oldest-when-size-reach', 4);

        Collection::times(10)->each(function (int $number) {
            static::create1MbFileOnDisk('primary-storage', "ARCANEDEV/test{$number}.zip", Carbon::now()->subDays($number));
        });


        $this->artisan('backup:clean')
             ->expectsOutput('Starting cleanup...')
             ->expectsOutput('Used storage after cleanup the ARCANEDEV on disk [primary-storage] : 4 MB')
             ->expectsOutput('Cleanup completed!')
             ->assertExitCode(0);
    }
}
