<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Helpers;

use Arcanedev\LaravelBackup\Helpers\Zip;
use Arcanedev\LaravelBackup\Tests\TestCase;
use SplFileInfo;

/**
 * Class     ZipTest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ZipTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Helpers\Zip */
    protected $zip;

    /** @var  string */
    protected $zipPath;

    /** @var  string */
    private $filesPath;

    /** @var  \SplFileInfo[] */
    protected $files;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        static::initTempDirectory();

        static::copyStubsFilesInto(
            $this->filesPath = static::tempDirectory('zip/files')
        );

        $this->files = static::getAllFiles($this->filesPath, true);
        $this->zip   = new Zip(
            $this->zipPath = static::tempDirectory('zip/file.zip')
        );
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated(): void
    {
        static::assertInstanceOf(Zip::class, $this->zip);
        static::assertSame($this->zipPath, $this->zip->path());
        static::assertSame(0.0, $this->zip->size());
        static::assertSame(0, $this->zip->count());
    }

    /** @test */
    public function it_can_create_zip(): void
    {
        $this->zip->create();

        foreach ($this->files as $file) {
            $this->zip->addFile($file->getPathname());
        }

        $this->zip->close();

        static::assertSame(count($this->files), $this->zip->count());
        static::assertGreaterThan(1500, $this->zip->size());

        $files = array_map(function (SplFileInfo $file) {
            return $this->zip->guessFilenameInArchive($file->getPathname());
        }, $this->files);

        static::assertFilesExistsInZipArchive($this->zip->path(), $files);
    }
}
