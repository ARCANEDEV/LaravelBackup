<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Helpers;

use Arcanedev\LaravelBackup\Helpers\FilesSelector;
use Arcanedev\LaravelBackup\Tests\TestCase;
use Generator;

/**
 * Class     FileSelectorTest
 *
 * @package  Arcanedev\LaravelBackup\Tests\Helpers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class FileSelectorTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Helpers\FilesSelector */
    private $filesSelector;

    /** @var  string */
    private $sourceDirectory;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->filesSelector   = $this->app->make(FilesSelector::class);
        $this->sourceDirectory = static::getStubsDirectory('files');

        $this->filesSelector->reset();
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated_via_container()
    {
        static::assertInstanceOf(FilesSelector::class, $this->filesSelector);
    }

    /** @test */
    public function it_returns_an_empty_selected_files_when_not_specifying_any_directories()
    {
        // As Generator
        $actual = $this->filesSelector->selected();

        static::assertInstanceOf(Generator::class, $actual);
        static::assertEmpty(iterator_to_array($actual));

        // As Array
        $actual = $this->filesSelector->selectedAsArray();

        static::assertTrue(is_array($actual));
        static::assertEmpty($actual);
    }

    /** @test */
    public function it_returns_an_empty_array_if_everything_is_excluded()
    {
        $actual = $this->filesSelector
            ->include($this->sourceDirectory)
            ->exclude($this->sourceDirectory)
            ->selectedAsArray();

        static::assertEmpty($actual);
    }

    /** @test */
    public function it_can_select_all_the_files_in_a_directory_and_subdirectories()
    {
        $this->filesSelector->include($this->sourceDirectory);

        $actual   = $this->filesSelector->selected();
        $expected = $this->getTestFiles([
            '.dotfile',
            '1Mb.file',
            'directory-1',
            'directory-1/sub-directory-1',
            'directory-1/sub-directory-1/file-1.txt',
            'directory-1/sub-directory-1/file-2.txt',
            'directory-1/file-1.txt',
            'directory-1/file-2.txt',
            'directory-2',
            'directory-2/sub-directory-1',
            'directory-2/sub-directory-1/file-1.txt',
            'file-1.txt',
            'file-2.txt',
            'file-3.txt',
        ]);

        static::assertSameArray($expected, iterator_to_array($actual));
    }

    /** @test */
    public function it_can_exclude_files_from_a_given_subdirectory()
    {
        $this->filesSelector
            ->include($this->sourceDirectory)
            ->exclude("{$this->sourceDirectory}/directory-1");

        $actual   = $this->filesSelector->selected();
        $expected = $this->getTestFiles([
            '.dotfile',
            '1Mb.file',
            'directory-2',
            'directory-2/sub-directory-1',
            'directory-2/sub-directory-1/file-1.txt',
            'file-1.txt',
            'file-2.txt',
            'file-3.txt',
        ]);

        static::assertSameArray($expected, iterator_to_array($actual));
    }

    /** @test */
    public function it_can_exclude_files_with_wildcards_from_a_given_subdirectory()
    {
        $this->filesSelector
            ->include($this->sourceDirectory)
            ->exclude("{$this->sourceDirectory}/*/sub-directory-1");

        $actual   = $this->filesSelector->selected();
        $expected = $this->getTestFiles([
            '.dotfile',
            '1Mb.file',
            'directory-1',
            'directory-1/file-1.txt',
            'directory-1/file-2.txt',
            'directory-2',
            'file-1.txt',
            'file-2.txt',
            'file-3.txt',
        ]);

        static::assertSameArray($expected, iterator_to_array($actual));
    }

    /** @test */
    public function it_can_select_files_from_multiple_directories()
    {
        $this->filesSelector->include([
            $this->sourceDirectory.'/directory-1/sub-directory-1',
            $this->sourceDirectory.'/directory-2/sub-directory-1',
        ]);

        $actual   = $this->filesSelector->selected();
        $expected = $this->getTestFiles([
            'directory-1/sub-directory-1/file-2.txt',
            'directory-1/sub-directory-1/file-1.txt',
            'directory-2/sub-directory-1/file-1.txt',
        ]);

        static::assertSameArrayContent($expected, iterator_to_array($actual));
    }

    /** @test */
    public function it_can_exclude_files_from_multiple_directories()
    {
        $actual = $this->filesSelector
            ->include($this->sourceDirectory)
            ->exclude($this->getTestFiles([
                'directory-1/sub-directory-1',
                'directory-2',
                'file-2.txt',
            ]))
            ->selected();

        $expected = $this->getTestFiles([
            '.dotfile',
            '1Mb.file',
            'directory-1',
            'directory-1/file-1.txt',
            'directory-1/file-2.txt',
            'file-1.txt',
            'file-3.txt',
        ]);

        static::assertSameArray($expected, iterator_to_array($actual));
    }

    /** @test */
    public function it_can_select_a_single_file()
    {
        $actual = $this->filesSelector
            ->include($this->sourceDirectory.'/.dotfile')
            ->selected();

        $expected = $this->getTestFiles(['.dotfile']);

        static::assertSame($expected, iterator_to_array($actual));
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * @param  array  $paths
     *
     * @return array
     */
    protected function getTestFiles(array $paths): array
    {
        return array_map(function (string $path) {
            return realpath($this->sourceDirectory.DIRECTORY_SEPARATOR.$path);
        }, $paths);
    }

    /**
     * Asserts that two arrays have the same content.
     *
     * @param  array   $expected
     * @param  array   $actual
     * @param  string  $message
     */
    protected static function assertSameArrayContent(array $expected, array $actual, string $message = '')
    {
        static::assertCount(count($expected), array_intersect($expected, $actual), $message);
    }

    /**
     * Asserts that two arrays have the same value.
     *
     * @param  array   $expected
     * @param  array   $actual
     * @param  string  $message
     */
    protected static function assertSameArray(array $expected, array $actual, string $message = '')
    {
        sort($expected);
        sort($actual);

        static::assertSame($expected, $actual, $message);
    }
}
