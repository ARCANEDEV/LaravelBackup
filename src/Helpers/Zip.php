<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Helpers;

use Arcanedev\LaravelBackup\Entities\Manifest;
use Arcanedev\LaravelBackup\Exceptions\ZipException;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use ZipArchive;

/**
 * Class     Zip
 *
 * @package  Arcanedev\LaravelBackup\Helpers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @mixin  \ZipArchive
 */
class Zip
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use ForwardsCalls;

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The archive path.
     *
     * @var  string
     */
    protected $path;

    /** @var  \ZipArchive */
    protected $zipArchive;

    /** @var  int */
    protected $fileCount = 0;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Zip constructor.
     *
     * @param  string  $path
     */
    public function __construct(string $path)
    {
        $this->setZipArchive(new ZipArchive);
        $this->setPath($path);
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the zip archive.
     *
     * @return \ZipArchive
     */
    public function zipArchive(): ZipArchive
    {
        return $this->zipArchive;
    }

    /**
     * Set the zip archive.
     *
     * @param  \ZipArchive  $zipArchive
     *
     * @return $this
     */
    public function setZipArchive(ZipArchive $zipArchive)
    {
        $this->zipArchive = $zipArchive;

        return $this;
    }

    /**
     * Get the destination path.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Set the destination path.
     *
     * @param  string  $path
     *
     * @return $this
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the file's size.
     *
     * @return float
     */
    public function size(): float
    {
        if ($this->count() === 0) {
            return 0;
        }

        return filesize($this->path());
    }

    /**
     * The number of the files in the archives.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->fileCount;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Create a zip file.
     *
     * @return mixed
     */
    public function create()
    {
        return tap($this->open(ZipArchive::CREATE | ZipArchive::OVERWRITE), function() {
            $this->fileCount = 0;
        });
    }

    /**
     * Open the zip file.
     *
     * @param  int|null  $flags
     *
     * @return mixed
     */
    public function open(int $flags = ZipArchive::CREATE)
    {
        return $this->zipArchive()->open($this->path(), $flags);
    }

    /**
     * Close the zip file.
     *
     * @return bool
     */
    public function close(): bool
    {
        return $this->zipArchive()->close();
    }

    /**
     * Add a file into the zip archive.
     *
     * @param  string       $file
     * @param  string|null  $localName
     *
     * @return $this
     */
    public function addFile(string $file, string $localName = null): self
    {
        if ($this->zipArchive()->addFile($file, $localName ?: $this->guessFilenameInArchive($file))) {
            $this->fileCount++;
        }

        return $this;
    }

    /**
     * Add files from manifest.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\Manifest  $manifest
     *
     * @return $this
     */
    public function addFilesFromManifest(Manifest $manifest): self
    {
        foreach ($manifest->files() as $group => $files) {
            foreach ($files as $file) {
                $this->addFile($file, $group.DIRECTORY_SEPARATOR.$this->guessFilenameInArchive($file));
            }
        }

        return $this;
    }

    /**
     * Get files (paths) from zip archive.
     *
     * @param  string  $path
     *
     * @return array
     *
     * @throws \Arcanedev\LaravelBackup\Exceptions\ZipException
     */
    public static function getFiles(string $path): array
    {
        $files = [];
        $zip   = new static($path);

        if ($zip->open() === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);

                if ($name === false) {
                    throw ZipException::makeFromStatus($zip->status);
                }

                array_push($files, $name);
            }
            $zip->close();
        }

        return $files;
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Guess the filename used in the zip archive.
     *
     * @param  string  $pathToFile
     *
     * @return string
     */
    public function guessFilenameInArchive(string $pathToFile): string
    {
        $fileDirectory = dirname($pathToFile);
        $zipDirectory  = [
            dirname($this->path()),
            base_path(),
        ];

        $pathToFile = Str::startsWith($fileDirectory, $zipDirectory)
            ? str_replace($zipDirectory, '', $pathToFile)
            : $pathToFile;

        return trim($pathToFile, DIRECTORY_SEPARATOR);
    }

    /**
     * Get a property from the zip archive instance.
     *
     * @param  string  $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->zipArchive()->{$name};
    }

    /**
     * Forward the call to the zip archive.
     *
     * @param  string  $method
     * @param  array   $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->zipArchive(), $method, $parameters);
    }
}
