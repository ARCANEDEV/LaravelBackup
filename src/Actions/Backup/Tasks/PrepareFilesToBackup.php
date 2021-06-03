<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Actions\Backup\Tasks;

use Arcanedev\LaravelBackup\Actions\Backup\BackupPassable;
use Arcanedev\LaravelBackup\Actions\TaskInterface;
use Arcanedev\LaravelBackup\Database\DbDumper;
use Arcanedev\LaravelBackup\Entities\Manifest;
use Arcanedev\LaravelBackup\Events\BackupManifestWasCreated;
use Arcanedev\LaravelBackup\Helpers\FilesSelector;
use Closure;

/**
 * Class     PrepareFilesToBackup
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PrepareFilesToBackup implements TaskInterface
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Helpers\FilesSelector */
    protected $selector;

    /** @var  \Arcanedev\LaravelBackup\Database\DbDumper */
    protected $dbDumper;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * PrepareFilesToBackup constructor.
     *
     * @param  \Arcanedev\LaravelBackup\Helpers\FilesSelector  $selector
     * @param  \Arcanedev\LaravelBackup\Database\DbDumper         $dbDumper
     */
    public function __construct(FilesSelector $selector, DbDumper $dbDumper)
    {
        $this->selector = $selector;
        $this->dbDumper = $dbDumper;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the task.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable  $passable
     * @param  \Closure                                                $next
     *
     * @return mixed
     */
    public function handle($passable, Closure $next)
    {
        $manifest = $this->createManifest($passable);

        $passable->setManifest($manifest);

        event(new BackupManifestWasCreated($manifest));

        return $next($passable);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Create the manifest file.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable  $passable
     *
     * @return \Arcanedev\LaravelBackup\Entities\Manifest
     */
    protected function createManifest(BackupPassable $passable): Manifest
    {
        return tap(Manifest::make($passable->temporaryDirectoryPath()), function (Manifest $manifest) use ($passable) {
            if ( ! $passable->isOnlyDatabases())
                $manifest->addFiles(['files' => $this->selectedFiles($passable)]);

            if ( ! $passable->isOnlyFiles())
                $manifest->addFiles(['databases' => $this->dumpDatabases($passable)]);

            $manifest->save();
        });
    }

    /**
     * Get the selected files.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable  $passable
     *
     * @return array
     */
    protected function selectedFiles(BackupPassable $passable): array
    {
        $files = $passable->getConfig('source.files', []);

        return $this->selector
            ->include($files['include'])
            ->exclude($files['exclude'])
            ->shouldFollowLinks($files['follow-links'] ?: false)
            ->shouldIgnoreUnreadableDirs($files['ignore-unreadable-directories'] ?: false)
            ->selectedAsArray();
    }

    /**
     * Dump databases.
     *
     * @param  \Arcanedev\LaravelBackup\Actions\Backup\BackupPassable  $passable
     *
     * @return array
     */
    protected function dumpDatabases(BackupPassable $passable): array
    {
        $this->dbDumper->setPath($passable->temporaryDirectoryPath());

        $databases = $passable->getConfig('source.databases', []);

        return array_map(function (string $connection) {
            return $this->dbDumper->dump($connection);
        }, $databases);
    }
}
