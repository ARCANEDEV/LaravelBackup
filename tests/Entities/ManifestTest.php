<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Entities;

use Arcanedev\LaravelBackup\Entities\Manifest;
use Arcanedev\LaravelBackup\Tests\TestCase;

/**
 * Class     ManifestTest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ManifestTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Entities\Manifest */
    protected $manifest;

    /**
     * @var string
     */
    private $path;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        static::initTempDirectory();

        $this->manifest = new Manifest(
            $this->path = static::getTempDirectory()
        );
    }

    protected function tearDown(): void
    {
        static::deleteTempDirectory();

        parent::tearDown();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated(): void
    {
        static::assertInstanceOf(Manifest::class, $this->manifest);
        static::assertSame($this->path, $this->manifest->path());
        static::assertSame('manifest.json', $this->manifest->filename());
        static::assertSame($this->path.DIRECTORY_SEPARATOR.'manifest.json', $this->manifest->filePath());
    }

    /** @test */
    public function it_can_be_make(): void
    {
        $manifest = Manifest::make($this->path);

        static::assertInstanceOf(Manifest::class, $manifest);
        static::assertSame($this->path, $manifest->path());
        static::assertSame('manifest.json', $manifest->filename());
        static::assertSame($this->path.DIRECTORY_SEPARATOR.'manifest.json', $manifest->filePath());
    }

    /** @test */
    public function it_can_add_files(): void
    {
        $this->manifest->addFiles([
            '/directory-1/file-1.txt',
            '/file-1.txt',
            '/file-2.txt',
        ]);

        static::assertEquals([
            '/directory-1/file-1.txt',
            '/file-1.txt',
            '/file-2.txt',
        ], $this->manifest->files());

        $this->manifest->addFiles('/file-3.txt');

        static::assertEquals([
            '/directory-1/file-1.txt',
            '/file-1.txt',
            '/file-2.txt',
            '/file-3.txt',
        ], $this->manifest->files());
    }

    /** @test */
    public function it_can_save_files_as_json(): void
    {
        $this->manifest->addFiles([
            'application' => [
                '/directory-1/file-1.txt',
                '/file-1.txt',
                '/file-2.txt',
                '/file-3.txt',
            ],
        ]);

        $this->manifest->addFiles([
            'databases' => [
                '/db-mysql.sql',
                '/db-sqlite.sql',
            ],
        ]);

        static::assertTrue($this->manifest->save());

        $expected = [
            'application' => [
                '/directory-1/file-1.txt',
                '/file-1.txt',
                '/file-2.txt',
                '/file-3.txt',
            ],
            'databases' => [
                '/db-mysql.sql',
                '/db-sqlite.sql',
            ],
        ];

        static::assertJsonStringEqualsJsonFile(
            $this->manifest->filePath(),
            json_encode($expected, JSON_PRETTY_PRINT)
        );
    }

    /** @test */
    public function it_can_load_manifest(): void
    {
        $files = [
            'application' => [
                '/directory-1/file-1.txt',
                '/file-1.txt',
                '/file-2.txt',
                '/file-3.txt',
            ],
            'databases' => [
                '/db-mysql.sql',
                '/db-sqlite.sql',
            ],
        ];

        static::assertTrue($this->manifest->setFiles($files)->save());

        $this->manifest->reset();

        static::assertTrue($this->manifest->isEmpty());
        static::assertFalse($this->manifest->isNotEmpty());
        static::assertSame([], $this->manifest->files());

        $this->manifest->load();

        static::assertFalse($this->manifest->isEmpty());
        static::assertTrue($this->manifest->isNotEmpty());
        static::assertEquals($files, $this->manifest->files());
    }
}
