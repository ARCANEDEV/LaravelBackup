<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Entities;

use Arcanedev\LaravelBackup\Entities\BackupCollection;
use Arcanedev\LaravelBackup\Entities\BackupDestination;
use Arcanedev\LaravelBackup\Entities\BackupDestinationCollection;
use Arcanedev\LaravelBackup\Exceptions\InvalidBackupDestination;
use Carbon\Carbon;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

/**
 * Class     BackupDestinationTest
 *
 * @package  Arcanedev\LaravelBackup\Tests\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupDestinationTest extends BackupTestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated()
    {
        $destination = static::makeBackupDestination();

        static::assertInstanceOf(BackupDestination::class, $destination);
        static::assertNull($destination->connectionError());
    }

    /** @test */
    public function it_can_make_instance_from_disk_name()
    {
        $destination = BackupDestination::makeFromDiskName($this->diskName, $this->backupName);

        static::assertInstanceOf(BackupDestination::class, $destination);
        static::assertNull($destination->connectionError());
    }

    /** @test */
    public function it_can_make_instance_with_unhandled_disk()
    {
        $destination = BackupDestination::makeFromDiskName('not-found', $this->backupName);

        static::assertFalse($destination->hasDisk());
        static::assertNull($destination->disk());

        $exception = $destination->connectionError();

        static::assertInstanceOf(InvalidArgumentException::class, $exception);
        static::assertSame('Disk [not-found] does not have a configured driver.', $exception->getMessage());
    }

    /** @test */
    public function it_can_get_backup_name()
    {
        $destination = static::makeBackupDestination();

        static::assertSame('ARCANEDEV', $destination->backupName());
    }

    /** @test */
    public function it_can_get_disk_name()
    {
        $destination = static::makeBackupDestination($diskName = 'secondary-storage');

        static::assertSame($diskName, $destination->diskName());
    }

    /** @test */
    public function it_can_get_disk_storage()
    {
        $destination = static::makeBackupDestination();

        static::assertTrue($destination->hasDisk());
        static::assertInstanceOf(FilesystemAdapter::class, $destination->disk());
    }

    /** @test */
    public function it_can_get_backups()
    {
        $destination = static::makeBackupDestination();

        static::assertInstanceOf(BackupCollection::class, $destination->backups());
        static::assertCount(0, $destination->backups());
    }

    /** @test */
    public function it_push_backup_extra_option_to_write_stream_if_set()
    {
        static::fakeDiskStorages($disks = ['s3-storage']);

        $this->app['config']->set('backup.backup.destination.disks', $disks);
        $this->app['config']->set('filesystems.disks.s3-storage', [
            'driver'         => 's3',
            'backup_options' => [
                'StorageClass' => 'COLD',
            ],
        ]);


        /** @var  \Arcanedev\LaravelBackup\Entities\BackupDestination  $backupDestination */
        $backupDestination = BackupDestinationCollection::makeFromDisksNames($disks, 'ARCANEDEV')->first();

        static::assertEquals(['StorageClass' => 'COLD'], $backupDestination->getDiskOptions());
    }

    /** @test */
    public function it_push_empty_default_backup_extra_option_to_write_stream_if_not_set()
    {
        static::fakeDiskStorages($disks = ['local-storage']);

        $this->app['config']->set('backup.backup.destination.disks', $disks);
        $this->app['config']->set('filesystems.disks.local-storage', [
            'driver' => 'local',
        ]);

        /** @var  \Arcanedev\LaravelBackup\Entities\BackupDestination  $backupDestination */
        $backupDestination = BackupDestinationCollection::makeFromDisksNames($disks, 'ARCANEDEV')->first();

        static::assertSame([], $backupDestination->getDiskOptions());
    }

    /** @test */
    public function it_can_write_write_file_into_destination()
    {
        $destination = static::makeBackupDestination();

        $destination->write($this->getStubsDirectory($path = 'files/.dotfile'));

        Storage::disk($this->diskName)->assertExists('ARCANEDEV/.dotfile');
    }

    /** @test */
    public function it_cannot_write_file_on_invalid_disk()
    {
        $this->expectException(InvalidBackupDestination::class);
        $this->expectExceptionMessage('There is no disk set for the backup named `ARCANEDEV`');

        BackupDestination::makeFromDiskName('not-found', $this->backupName)
             ->write($this->getStubsDirectory('files/.dotfile'));
    }

    /** @test */
    public function it_can_check_if_destination_is_reachable()
    {
        $disks = ['primary-storage', 'secondary-storage'];

        foreach ($disks as $disk) {
            $destination = BackupDestination::makeFromDiskName($disk, $this->backupName);

            static::assertTrue($destination->isReachable());
        }

        $destination = BackupDestination::makeFromDiskName('not-found', $this->backupName);

        static::assertFalse($destination->hasDisk());
        static::assertFalse($destination->isReachable());

        $this->app['config']->set('filesystems.disks.s3-test-backup', [
            'driver'         => 's3',
            'region'         => 'us-west-2',
            'bucket'         => 'public',
            'backup_options' => [
                'StorageClass' => 'COLD',
            ],
        ]);

        $destination = BackupDestination::makeFromDiskName('s3-test-backup', $this->backupName);

        static::assertTrue($destination->hasDisk());
        static::assertFalse($destination->isReachable());
    }

    /** @test */
    public function it_can_get_and_reset_backups_collection()
    {
        $destination = static::makeBackupDestination();

        $this->makeBackupFile($path = "{$this->backupName}/backup-1.zip");
        Storage::disk($this->diskName)->assertExists($path);

        static::assertFalse($destination->hasCachedBackups());
        static::assertCount(1, $destination->backups());
        static::assertTrue($destination->hasCachedBackups());

        $this->makeBackupFile($path = "{$this->backupName}/backup-2.zip");
        Storage::disk($this->diskName)->assertExists($path);

        static::assertCount(1, $destination->backups());
        static::assertTrue($destination->hasCachedBackups());

        $destination->fresh();

        static::assertFalse($destination->hasCachedBackups());
        static::assertCount(2, $destination->backups());
        static::assertTrue($destination->hasCachedBackups());
    }

    /** @test */
    public function it_can_get_used_storage()
    {
        $destination = static::makeBackupDestination();

        static::assertSame(0.0, $destination->usedStorage());

        $this->makeBackupFile($path = "{$this->backupName}/backup-1.zip");
        Storage::disk($this->diskName)->assertExists($path);

        static::assertSame(0.0, $destination->usedStorage());

        $destination->fresh();

        static::assertGreaterThan(0.0, $destination->usedStorage());
    }

    /** @test */
    public function it_can_get_newest_and_oldest_backups()
    {
        $this->makeBackupFile($path = "{$this->backupName}/backup-newest.zip");
        $this->makeBackupFile($path = "{$this->backupName}/backup-between.zip", 1);
        $this->makeBackupFile($path = "{$this->backupName}/backup-oldest.zip", 2);

        $destination = static::makeBackupDestination();

        static::assertCount(3, $destination->backups());

        static::assertSame("ARCANEDEV/backup-newest.zip", $destination->newestBackup()->path());
        static::assertSame("ARCANEDEV/backup-oldest.zip", $destination->oldestBackup()->path());
    }

    /** @test */
    public function it_can_check_if_the_newest_backup_is_older_than_the_given_date()
    {
        $this->makeBackupFile($path = "{$this->backupName}/backup.zip");

        $destination = static::makeBackupDestination();

        static::assertFalse($destination->newestBackupIsOlderThan(Carbon::now()->addSecond()));
        static::assertFalse($destination->newestBackupIsOlderThan(Carbon::now()));
        static::assertTrue($destination->newestBackupIsOlderThan(Carbon::now()->subSecond()));
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make a destination backup.
     *
     * @param  string  $diskName
     *
     * @return \Arcanedev\LaravelBackup\Entities\BackupDestination
     */
    protected function makeBackupDestination(string $diskName = null): BackupDestination
    {
        $diskName = $diskName ?: $this->diskName;

        return new BackupDestination(
            Storage::disk($diskName), $this->backupName, $diskName
        );
    }
}
