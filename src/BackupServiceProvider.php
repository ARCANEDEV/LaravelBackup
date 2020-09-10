<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup;

use Arcanedev\Support\Providers\PackageServiceProvider;

/**
 * Class     BackupServiceProvider
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupServiceProvider extends PackageServiceProvider
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * @var string
     */
    protected $package = 'backup';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        parent::register();

        $this->registerConfig();

        $this->singleton(Database\DbDumperManager::class);
        $this->singleton(Actions\Cleanup\Strategies\CleanupStrategy::class, Actions\Cleanup\Strategies\DefaultStrategy::class);

        $this->registerProvider(Providers\EventServiceProvider::class);

        $this->registerCommands([
            Console\RunBackupCommand::class,
            Console\CleanupBackupCommand::class,
            Console\MonitorBackupCommand::class,
        ]);
    }

    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        $this->loadTranslations();

        if ($this->app->runningInConsole()) {
            $this->publishConfig();
            $this->publishTranslations();
        }
    }
}
