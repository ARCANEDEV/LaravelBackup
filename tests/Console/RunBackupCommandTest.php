<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Console;

use Arcanedev\LaravelBackup\Database\Compressors\GzipCompressor;
use Arcanedev\LaravelBackup\Entities\Notifiable;
use Arcanedev\LaravelBackup\Events\{
    BackupActionHasFailed, BackupActionWasSuccessful, BackupManifestWasCreated, BackupZipWasCreated,
};
use Arcanedev\LaravelBackup\Notifications\BackupWasSuccessfulNotification;
use Arcanedev\LaravelBackup\Tests\TestCase;
use Illuminate\Support\Facades\{Event, Notification, Storage};
use Illuminate\Console\Command;

/**
 * Class     RunBackupCommandTest
 *
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
        Notification::fake();
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
             ->assertExitCode(Command::SUCCESS);

        static::assertBackupsExistsInStorages();

        Event::assertDispatched(BackupManifestWasCreated::class);
        Event::assertDispatched(BackupZipWasCreated::class);
        Event::assertDispatched(BackupActionWasSuccessful::class);

        Event::assertNotDispatched(BackupActionHasFailed::class);

        Notification::assertNotSentTo(new Notifiable, BackupWasSuccessfulNotification::class);
    }

    /** @test */
    public function it_can_run_with_disabled_notification(): void
    {
        $this->artisan('backup:run --disable-notifications')
             ->expectsOutput('Starting backup...')
             ->expectsOutput('Backup completed!')
             ->assertExitCode(Command::SUCCESS);

        static::assertBackupsExistsInStorages();

        Event::assertDispatched(BackupManifestWasCreated::class);
        Event::assertDispatched(BackupZipWasCreated::class);
        Event::assertDispatched(BackupActionWasSuccessful::class);

        Event::assertNotDispatched(BackupActionHasFailed::class);
    }

    /** @test */
    public function it_cannot_run_with_both_only_db_and_only_files_options(): void
    {
        $this->artisan('backup:run --only-files --only-db')
             ->expectsOutput('Starting backup...')
             ->expectsOutput('Backup failed because: Cannot use `only-db` and `only-files` together')
             ->assertExitCode(Command::FAILURE);

        static::assertBackupsMissingInStorages();

        Event::assertDispatched(BackupActionHasFailed::class);

        Event::assertNotDispatched(BackupActionWasSuccessful::class);
        Event::assertNotDispatched(BackupManifestWasCreated::class);
        Event::assertNotDispatched(BackupZipWasCreated::class);
    }

    /** @test */
    public function it_can_run_backup_with_compressor(): void
    {
        $this->app['config']->set('backup.backup.db-dump-compressor', GzipCompressor::class);

        $this->artisan('backup:run --only-db')
            ->expectsOutput('Starting backup...')
            ->assertExitCode(Command::SUCCESS);

        static::assertBackupsExistsInStorages();

        static::assertBackupFilesExistsInZipFile([
            'databases/dump-sqlite-db-1.sql.gz',
            'databases/dump-sqlite-db-2.sql.gz',
        ]);

        Event::assertDispatched(BackupManifestWasCreated::class);
        Event::assertDispatched(BackupZipWasCreated::class);
        Event::assertDispatched(BackupActionWasSuccessful::class);

        Event::assertNotDispatched(BackupActionHasFailed::class);
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

    protected static function assertBackupsExistsInStorages(array $disks = ['primary-storage', 'secondary-storage']): void
    {
        foreach ($disks as $disk) {
            Storage::disk($disk)->assertExists('ARCANEDEV/20190101-123030.zip');
        }
    }

    protected static function assertBackupFilesExistsInZipFile(array $files, array $disks = ['primary-storage', 'secondary-storage']): void
    {
        foreach ($disks as $disk) {
            $path = Storage::disk($disk)->path('ARCANEDEV/20190101-123030.zip');

            static::assertFilesExistsInZipArchive($path, $files);
        }
    }

    protected static function assertBackupsMissingInStorages(array $disks = ['primary-storage', 'secondary-storage']): void
    {
        foreach ($disks as $disk) {
            Storage::disk($disk)->assertMissing('ARCANEDEV/20190101-123030.zip');
        }
    }
}
