<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Listeners;

use Arcanedev\LaravelBackup\Events\BackupZipWasCreated;
use Arcanedev\LaravelBackup\Helpers\Zip;
use Arcanedev\LaravelBackup\Listeners\EncryptBackupArchive;
use Arcanedev\LaravelBackup\Tests\TestCase;
use ZipArchive;

/**
 * Class     EncryptBackupArchiveTest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class EncryptBackupArchiveTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    protected const PASSWORD = '24dsjF6BPjWgUfTu';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        static::initTempDirectory();

        copy(static::stubsDirectory('files/archive.zip'), static::tempDirectory('archive.zip'));

        $this->app['config']->set('backup.backup.password', self::PASSWORD);
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_keeps_archive_unencrypted_without_password(): void
    {
        $this->app['config']->set('backup.backup.password', null);

        $zip = $this->zip()->open();

        static::assertEncryptionMethod($zip, ZipArchive::EM_NONE);

        static::assertTrue(
            $zip->extractTo(static::tempDirectory('extraction'))
        );
        static::assertValidExtractedFiles();

        $zip->close();
    }

    /**
     * @test
     *
     * @dataProvider encryptionMethodsDataProvider
     *
     * @param  int  $algorithm
     */
    public function it_encrypts_archive_with_password(int $algorithm): void
    {
        $this->app['config']->set('backup.backup.encryption', $algorithm);

        $zip = $this->zip()->open();

        static::assertEncryptionMethod($zip, $algorithm);

        $zip->setPassword(self::PASSWORD);

        static::assertTrue(
            $zip->extractTo(self::tempDirectory('extraction'))
        );
        static::assertValidExtractedFiles();

        $zip->close();
    }

    /** @test */
    public function it_can_not_open_encrypted_archive_without_password(): void
    {
        $zip = $this->zip()->open();

        static::assertEncryptionMethod($zip, ZipArchive::EM_AES_256);

        static::assertFalse(
            $zip->extractTo(self::tempDirectory('extraction'))
        );

        $zip->close();
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Prepare the zip archive.
     *
     * @return \Arcanedev\LaravelBackup\Helpers\Zip
     */
    protected function zip(): Zip
    {
        return tap(new Zip(static::tempDirectory('archive.zip')), function (Zip $zip): void {
            $this->app->call(EncryptBackupArchive::class.'@handle', [
                'event' => new BackupZipWasCreated($zip)
            ]);
        });
    }

    /* -----------------------------------------------------------------
     |  Asserts
     | -----------------------------------------------------------------
     */

    /**
     * @param  \Arcanedev\LaravelBackup\Helpers\Zip  $zip
     * @param  int                                   $algorithm
     */
    protected static function assertEncryptionMethod(Zip $zip, int $algorithm): void
    {
        foreach (range(0, $zip->numFiles - 1) as $i) {
            static::assertSame($algorithm, $zip->statIndex($i)['encryption_method']);
        }
    }

    protected static function assertValidExtractedFiles(): void
    {
        foreach (['file1.txt', 'file2.txt', 'file3.txt'] as $filename) {
            $filepath = static::tempDirectory('extraction/'.$filename);
            static::assertTrue(file_exists($filepath));
            static::assertSame('lorum ipsum', file_get_contents($filepath));
        }
    }

    /* -----------------------------------------------------------------
     |  Data Providers
     | -----------------------------------------------------------------
     */

    public function encryptionMethodsDataProvider(): array
    {
        return [
            [ZipArchive::EM_AES_128],
            [ZipArchive::EM_AES_192],
            [ZipArchive::EM_AES_256],
        ];
    }
}
