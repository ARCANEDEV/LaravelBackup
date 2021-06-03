<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Concerns;

use Arcanedev\LaravelBackup\Helpers\Zip;
use Illuminate\Support\Facades\File;

/**
 * Trait     HasFilesManipulation
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
trait HasFilesManipulation
{
    /* -----------------------------------------------------------------
     |  Directories
     | -----------------------------------------------------------------
     */

    /**
     * Get the tests folder path.
     *
     * @param  string|null  $path
     *
     * @return string
     */
    protected static function getTestsPath(string $path = null): string
    {
        $path = dirname(__DIR__).($path ? '/'.$path : $path);

        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Get the temp directory path.
     *
     * @param  string|null  $path
     *
     * @return string
     */
    protected static function tempDirectory(string $path = null): string
    {
        return static::getTestsPath('temp'.($path ? '/'.$path : $path));
    }

    /**
     * Get the stubs directory path.
     *
     * @param  string|null  $path
     *
     * @return string
     */
    protected static function stubsDirectory(string $path = null): string
    {
        return static::getTestsPath('_stubs'.($path ? '/'.$path : $path));
    }

    /**
     * Get all the files from the given path.
     *
     * @param  string  $path
     * @param  bool    $hidden
     *
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    protected static function getAllFiles(string $path, bool $hidden = false): array
    {
        return File::allFiles($path, $hidden);
    }

    /**
     * Init the temp directory.
     */
    protected static function initTempDirectory(): void
    {
        if (File::isDirectory(static::tempDirectory()))
            static::deleteTempDirectory();

        static::createTempDirectory();

        file_put_contents(static::tempDirectory('.gitignore'), '*'.PHP_EOL.'!.gitignore');
    }

    /**
     * Create the temp directory.
     */
    protected static function createTempDirectory(): void
    {
        File::ensureDirectoryExists(static::tempDirectory());
    }

    /**
     * Copy stubs files into temp directory.
     *
     * @param  string|null  $path
     */
    protected function copyStubsFilesInto(string $path = null): void
    {
        File::copyDirectory(
            static::stubsDirectory('files'),
            $path ?: static::tempDirectory()
        );
    }

    /**
     * Copy stubs files into temp directory.
     *
     * @param  string|null  $path
     */
    protected static function copyStubsDatabasesInto(string $path = null): void
    {
        static::copyStubsInto($path, 'databases');
    }

    /**
     * Copy stubs folders/files into the given path.
     *
     * @param  string|null  $path
     * @param  string|null  $folder
     */
    public static function copyStubsInto(string $path = null, string $folder = null): void
    {
        File::copyDirectory(
            static::stubsDirectory($folder),
            $path ?: static::tempDirectory($folder)
        );
    }

    /**
     * Delete the temp directory.
     */
    protected static function deleteTempDirectory(): void
    {
        if (File::isDirectory($path = static::tempDirectory()))
            File::deleteDirectory($path);
    }

    /**
     * Check if file exists in zip archive.
     *
     * @param  string  $zipPath
     * @param  string  $fileName
     *
     * @return bool
     *
     * @throws \Exception
     */
    protected static function fileExistsInZip(string $zipPath, string $fileName): bool
    {
        foreach (Zip::getFiles($zipPath) as $file) {
            if (static::replaceDirectorySeparator($fileName) === static::replaceDirectorySeparator($file))
                return true;
        }

        return false;
    }

    /**
     * Unify the directory separator to the system one.
     *
     * @param  string  $path
     *
     * @return string
     */
    protected static function replaceDirectorySeparator(string $path): string
    {
        return str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
    }
}
