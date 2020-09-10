<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Entities;

use Illuminate\Support\Facades\Storage;

/**
 * Class     BackupTest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupTest extends BackupTestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_determine_the_disk_of_the_backup(): void
    {
        $backup = $this->makeBackupFile("{$this->backupName}/test.zip");

        static::assertTrue($backup->hasDisk());
        static::assertSame(Storage::disk($this->diskName), $backup->disk());
    }

    /** @test */
    public function it_can_determine_the_path_of_the_backup(): void
    {
        $backup = $this->makeBackupFile($path ="{$this->backupName}/test.zip'");

        static::assertSame($path, $backup->path());
    }

    /** @test */
    public function it_can_delete_itself(): void
    {
        $backup = $this->makeBackupFile($path ="{$this->backupName}/test.zip'");

        static::assertTrue($backup->exists());
        Storage::disk($this->diskName)->assertExists($path);

        static::assertTrue($backup->delete());

        Storage::disk($this->diskName)->assertMissing($path);
        static::assertFalse($backup->exists());
    }

    /** @test */
    public function it_can_determine_its_size(): void
    {
        $backup = $this->makeBackupFile($path ="{$this->backupName}/test.zip'");

        $fileSize = (float) Storage::disk($this->diskName)->size($path);

        static::assertSame($fileSize, $backup->size());
        static::assertGreaterThan(0, $backup->size());
    }

    /** @test */
    public function it_need_a_float_type_size(): void
    {
        $backup = $this->makeBackupFile("{$this->backupName}/test.zip");

        static::assertIsFloat($backup->size());
    }

    /** @test */
    public function it_can_determine_its_size_even_after_it_has_been_deleted(): void
    {
        $backup = $this->makeBackupFile("{$this->backupName}/test.zip");

        static::assertTrue($backup->delete());
        static::assertSame(0.0, $backup->size());
    }

    /** @test */
    public function it_can_get_filesystem_adapter_type(): void
    {
        $backup = $this->makeBackupFile("{$this->backupName}/test.zip");

        static::assertSame('local', $backup->filesystemType());
    }

    /** @test */
    public function it_can_get_backup_date(): void
    {
        $backup = $this->makeBackupFile("{$this->backupName}/test.zip");

        static::assertInstanceOf(\Carbon\Carbon::class, $backup->date());
        static::assertSame('2019-01-01 12:30:30', $backup->date()->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_can_stream_backup(): void
    {
        $backup = $this->makeBackupFile("{$this->backupName}/test.zip");

        static::assertSame('stream', get_resource_type($backup->stream()));
    }
}
