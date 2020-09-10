<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Helpers;

use Generator;
use Illuminate\Support\{Arr, Collection, Str};
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * Class     FilesSelector
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class FilesSelector
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Symfony\Component\Finder\Finder */
    protected $finder;

    /** @var  \Illuminate\Support\Collection */
    protected $included;

    /** @var  \Illuminate\Support\Collection */
    protected $excluded;

    /** @var  bool */
    protected $shouldFollowLinks = false;

    /** @var  bool */
    protected $shouldIgnoreUnreadableDirectories = false;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * FileSelection constructor.
     *
     * @param  \Symfony\Component\Finder\Finder  $finder
     */
    public function __construct(Finder $finder)
    {
        $this->setFinder($finder);
        $this->reset();
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Set the finder component.
     *
     * @param  \Symfony\Component\Finder\Finder  $finder
     *
     * @return $this
     */
    public function setFinder(Finder $finder): self
    {
        $this->finder = $finder
            ->ignoreDotFiles(false)
            ->ignoreVCS(false);

        return $this;
    }

    /**
     * Get the included files/directories.
     *
     * @return \Illuminate\Support\Collection
     */
    public function included(): Collection
    {
        return $this->included;
    }

    /**
     * Set the included files/directories.
     *
     * @param  array|string  $paths
     *
     * @return $this
     */
    public function include($paths): self
    {
        $this->included = $this->included->merge(static::sanitizePaths($paths));

        return $this;
    }

    /**
     * Get the excluded files/directories.
     *
     * @return \Illuminate\Support\Collection
     */
    private function excluded(): Collection
    {
        return $this->excluded;
    }

    /**
     * Set the excluded files/directories.
     *
     * @param  array|string  $paths
     *
     * @return $this
     */
    public function exclude($paths): self
    {
        $this->excluded = $this->excluded->merge(static::sanitizePaths($paths));

        return $this;
    }

    /**
     * Forces the following of symlinks.
     *
     * @param  bool  $shouldFollowLinks
     *
     * @return $this
     */
    public function shouldFollowLinks(bool $shouldFollowLinks): self
    {
        $this->shouldFollowLinks = $shouldFollowLinks;

        return $this;
    }

    /**
     * Set if it should ignore the unreadable directories.
     *
     * @param  bool  $ignoreUnreadableDirectories
     *
     * @return $this
     */
    public function shouldIgnoreUnreadableDirs(bool $ignoreUnreadableDirectories): self
    {
        $this->shouldIgnoreUnreadableDirectories = $ignoreUnreadableDirectories;

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Reset the included/excluded paths.
     *
     * @return $this
     */
    public function reset(): self
    {
        $this->included = new Collection;
        $this->excluded = new Collection;

        return $this;
    }

    /**
     * Get the selected files.
     *
     * @return \Generator|string[]
     */
    public function selected(): Generator
    {
        if ($this->included()->isEmpty()) {
            return [];
        }

        if ($this->shouldFollowLinks) {
            $this->finder->followLinks();
        }

        if ($this->shouldIgnoreUnreadableDirectories) {
            $this->finder->ignoreUnreadableDirs();
        }

        /**
         * @var  \Illuminate\Support\Collection  $includedFiles
         * @var  \Illuminate\Support\Collection  $includedDirectories
         */
        [$includedFiles, $includedDirectories] = $this->included()->partition(function (string $path) {
            return is_file($path);
        });

        foreach ($includedFiles as $includedFile) {
            yield $includedFile;
        }

        if ($includedDirectories->isEmpty()) {
            return [];
        }

        $this->finder->in($includedDirectories->toArray());

        foreach ($this->finder->getIterator() as $file) {
            if ( ! $this->shouldExclude($file)) {
                yield $file->getPathname();
            }
        }
    }

    /**
     * Get the selected as an array.
     *
     * @return array
     */
    public function selectedAsArray(): array
    {
        return iterator_to_array($this->selected());
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Sanitize the paths.
     *
     * @param  array|string  $paths
     *
     * @return \Illuminate\Support\Collection
     */
    private static function sanitizePaths($paths): Collection
    {
        return Collection::make(Arr::wrap($paths))
            ->reject(function ($path) {
                return $path === '';
            })
            ->flatMap(function ($path) {
                return glob($path);
            })
            ->map(function ($path) {
                return realpath($path);
            })
            ->reject(function ($path) {
                return $path === false;
            })
            ->unique();
    }

    /**
     * Check if should exclude the given path.
     *
     * @param  \SplFileInfo  $path
     *
     * @return bool
     */
    protected function shouldExclude(SplFileInfo $path): bool
    {
        return Str::startsWith($path->getRealPath(), $this->excluded()->toArray());
    }
}
