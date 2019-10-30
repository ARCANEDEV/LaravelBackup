<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Console;

use Arcanedev\LaravelBackup\Events\{
    BackupActionHasFailed, BackupManifestWasCreated, BackupWasSuccessful, BackupZipWasCreated,
};
use Arcanedev\LaravelBackup\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

/**
 * Class     RunBackupCommandTest
 *
 * @package  Arcanedev\LaravelBackup\Tests\Console
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RunBackupCommandTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        static::createTempDirectory();
        static::copyStubsDatabasesInto();

        Event::fake();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_run_with_default_options(): void
    {
        $this->artisan('backup:run')
             ->expectsOutput('Starting backup...')
             ->expectsOutput('Backup completed!')
             ->assertExitCode(0);

        static::assertBackupsExistsInStorages();

        Event::assertDispatched(BackupManifestWasCreated::class);
        Event::assertDispatched(BackupZipWasCreated::class);

        Event::assertDispatched(BackupWasSuccessful::class);
        Event::assertNotDispatched(BackupActionHasFailed::class);
    }

    /** @test */
    public function it_can_run_with_disabled_notification(): void
    {
        $this->artisan('backup:run --disable-notifications')
             ->expectsOutput('Starting backup...')
             ->expectsOutput('Backup completed!')
             ->assertExitCode(0);

        static::assertBackupsExistsInStorages();

        Event::assertDispatched(BackupManifestWasCreated::class);
        Event::assertDispatched(BackupZipWasCreated::class);

        Event::assertNotDispatched(BackupWasSuccessful::class);
        Event::assertNotDispatched(BackupActionHasFailed::class);
    }

    /** @test */
    public function it_cannot_run_with_both_only_db_and_only_files_options(): void
    {
        $this->artisan('backup:run --only-files --only-db')
             ->expectsOutput('Starting backup...')
             ->expectsOutput('Backup failed because: Cannot use `only-db` and `only-files` together')
             ->assertExitCode(1);

        static::assertBackupsMissingInStorages();

        Event::assertDispatched(BackupActionHasFailed::class);

        Event::assertNotDispatched(BackupWasSuccessful::class);
        Event::assertNotDispatched(BackupManifestWasCreated::class);
        Event::assertNotDispatched(BackupZipWasCreated::class);
    }

    /* -----------------------------------------------------------------
     |  Asserts
     | -----------------------------------------------------------------
     */

    /**
     * Assert that temporary directory does not exists.
     */
    protected function assertTemporaryDirectoryNotExists(): void
    {
        static::assertFalse(file_exists(storage_path('app/_backup-temp')));
    }

    protected static function assertBackupsExistsInStorages(array $disks = ['primary-storage', 'secondary-storage'])
    {
        foreach ($disks as $disk) {
            Storage::disk($disk)->assertExists('ARCANEDEV/20190101-123030.zip');
        }
    }

    protected static function assertBackupsMissingInStorages(array $disks = ['primary-storage', 'secondary-storage'])
    {
        foreach ($disks as $disk) {
            Storage::disk($disk)->assertMissing('ARCANEDEV/20190101-123030.zip');
        }
    }
}
