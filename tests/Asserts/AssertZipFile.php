<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Asserts;

/**
 * Trait     AssertZipFile
 *
 * @package  Arcanedev\LaravelBackup\Tests\Asserts
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
trait AssertZipFile
{
    /**
     * @param  string  $zipPath
     * @param  array   $files
     */
    protected function assertFilesExistsInZipArchive(string $zipPath, array $files): void
    {
        foreach ($files as $file) {
            static::assertFileExistsInZip($zipPath, $file);
        }
    }

    /**
     * @param  string  $zipPath
     * @param  string  $fileName
     */
    protected function assertFileExistsInZip(string $zipPath, string $fileName): void
    {
        static::assertTrue(
            static::fileExistsInZip($zipPath, $fileName),
            "Failed to assert that {$zipPath} contains a file name {$fileName}"
        );
    }

    /**
     * @param  string  $zipPath
     * @param  string  $fileName
     */
    protected function assertFileDoesntExistsInZip(string $zipPath, string $fileName): void
    {
        static::assertFalse(
            static::fileExistsInZip($zipPath, $fileName),
            "Failed to assert that {$zipPath} doesn't contain a file name {$fileName}"
        );
    }
}
